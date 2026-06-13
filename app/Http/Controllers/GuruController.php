<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GuruController extends Controller
{
        /**
     * Dashboard guru
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();

            // Ambil data ujian milik guru yang login
            $exams = Exam::where('guru_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

            // Hitung statistik
            $totalExams = $exams->count();

            // HITUNG UNIQUE PESERTA (distinct berdasarkan siswa_id)
            $totalParticipants = ExamParticipant::whereIn('exam_id', $exams->pluck('id'))
                                ->distinct('siswa_id')
                                ->count('siswa_id');

            // Hitung rata-rata nilai dari peserta yang unik
            $averageScore = ExamParticipant::whereIn('exam_id', $exams->pluck('id'))
                            ->where('status', 'selesai')
                            ->avg('total_score') ?? 0;

            // Bulatkan rata-rata
            $averageScore = round($averageScore, 1);

            return view('guru.dashboard', compact('exams', 'totalExams', 'totalParticipants', 'averageScore'));

        } catch (\Exception $e) {
            Log::error('Guru Dashboard Error: ' . $e->getMessage());
            return view('guru.dashboard', compact('exams', 'totalExams', 'totalParticipants', 'averageScore'))->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Show form create exam
     */
    public function createExam()
    {
        return view('guru.buat-ujian');
    }

    /**
     * Store new exam
     */
    public function storeExam(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'durasi' => 'required|integer|min:1',
            'jumlah_peserta' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Buat kode ujian unik
            $kodeUjian = strtoupper(Str::random(8));

            // Simpan exam ke database
            $exam = Exam::create([
                'guru_id' => Auth::id(),
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'durasi' => $request->durasi,
                'jumlah_peserta' => $request->jumlah_peserta,
                'kode_ujian' => $kodeUjian,
                'acak_soal' => $request->has('acak_soal') ? true : false,
                'tampilkan_hasil' => $request->has('tampilkan_hasil') ? true : true,
                'status' => 'menunggu'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ujian berhasil dibuat!',
                'exam_id' => $exam->id,
                'redirect' => route('guru.buat-soal', $exam->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Store Exam Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Show form create questions
 */
public function createQuestions($examId)
{
    $exam = Exam::with('questions')->findOrFail($examId);

    // Cek kepemilikan ujian
    if ($exam->guru_id !== Auth::id()) {
        abort(403, 'Unauthorized');
    }

    // Ambil soal yang sudah ada untuk ditampilkan
    $existingQuestions = $exam->questions;

    // DEBUG: Cek apakah ada data (hapus setelah berhasil)
    Log::info('createQuestions - Exam ID: ' . $examId);
    Log::info('Jumlah soal yang ditemukan: ' . $existingQuestions->count());
    foreach ($existingQuestions as $q) {
        Log::info('Soal: ' . $q->id . ' - ' . $q->type . ' - ' . $q->question_text);
    }

    return view('guru.buat-soal', compact('exam', 'existingQuestions'));
}

   /**
     * Store or update questions with images
     */
    public function storeQuestions(Request $request, $examId)
    {
        // DEBUG: Log untuk debugging
        Log::info('=== STORE QUESTIONS - DEBUG ===');
        Log::info('Exam ID from URL: ' . $examId);

        $exam = Exam::findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'questions' => 'required|array',
            'questions.*.type' => 'required|in:pg,essay',
            'questions.*.question_text' => 'required|string',
            'questions.*.score' => 'required|integer|min:1',
            'questions.*.image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'questions.*.option_images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        try {
            $savedCount = 0;

            foreach ($request->input('questions') as $index => $questionData) {
                Log::info('Processing question ' . $index, $questionData);

                $questionId = $questionData['id'] ?? null;

                // ========== PERBAIKAN: Deteksi soal baru ==========
                $isNewQuestion = !$questionId ||
                                $questionId === 'new' ||
                                $questionId === 'new_' ||
                                (is_string($questionId) && (str_starts_with($questionId, 'new_') || str_starts_with($questionId, 'temp_')));

                // Upload gambar soal (jika ada)
                $imagePath = null;
                if ($request->hasFile("questions.{$index}.image")) {
                    $file = $request->file("questions.{$index}.image");
                    $imagePath = $file->store('questions', 'public');
                    Log::info('Image uploaded: ' . $imagePath);
                }

                // Upload gambar opsi untuk PG
                $optionImages = [];
                if ($questionData['type'] === 'pg') {
                    for ($i = 0; $i < 4; $i++) {
                        if ($request->hasFile("questions.{$index}.option_images.{$i}")) {
                            $file = $request->file("questions.{$index}.option_images.{$i}");
                            $optionImages[] = $file->store('options', 'public');
                        } else {
                            $optionImages[] = null;
                        }
                    }
                }

                // Ambil options sebagai array
                $options = $questionData['options'] ?? null;
                if (is_string($options)) {
                    $options = json_decode($options, true);
                }

                if ($isNewQuestion) {
                    // ========== BUAT SOAL BARU ==========
                    $newQuestion = Question::create([
                        'exam_id' => $exam->id,
                        'type' => $questionData['type'],
                        'question_text' => $questionData['question_text'],
                        'image' => $imagePath,
                        'options' => $options,
                        'option_images' => !empty($optionImages) ? $optionImages : null,
                        'correct_answer' => $questionData['correct_answer'] ?? null,
                        'score' => $questionData['score']
                    ]);
                    Log::info('Question CREATED: ID ' . $newQuestion->id . ' for exam ' . $exam->id);
                    $savedCount++;
                } else {
                    // ========== UPDATE SOAL EXISTING ==========
                    $question = Question::find($questionId);
                    if ($question && $question->exam_id == $exam->id) {
                        $updateData = [
                            'question_text' => $questionData['question_text'],
                            'type' => $questionData['type'],
                            'options' => $options,
                            'correct_answer' => $questionData['correct_answer'] ?? null,
                            'score' => $questionData['score']
                        ];

                        // Update gambar hanya jika ada gambar baru diupload
                        if ($imagePath) {
                            if ($question->image) {
                                $oldImagePath = storage_path('app/public/' . $question->image);
                                if (file_exists($oldImagePath)) {
                                    unlink($oldImagePath);
                                }
                            }
                            $updateData['image'] = $imagePath;
                        }

                        // Update gambar opsi jika ada
                        if (!empty($optionImages) && count(array_filter($optionImages)) > 0) {
                            if ($question->option_images) {
                                $oldOptionImages = is_array($question->option_images) ? $question->option_images : json_decode($question->option_images, true);
                                if (is_array($oldOptionImages)) {
                                    foreach ($oldOptionImages as $oldImg) {
                                        if ($oldImg) {
                                            $oldImgPath = storage_path('app/public/' . $oldImg);
                                            if (file_exists($oldImgPath)) {
                                                unlink($oldImgPath);
                                            }
                                        }
                                    }
                                }
                            }
                            $updateData['option_images'] = $optionImages;
                        }

                        $question->update($updateData);
                        Log::info('Question UPDATED: ID ' . $question->id);
                        $savedCount++;
                    } else {
                        Log::warning('Question not found or not owned: ' . $questionId);
                    }
                }
            }

            $finalCount = Question::where('exam_id', $exam->id)->count();
            Log::info('Final question count for exam ' . $exam->id . ': ' . $finalCount);

            return response()->json([
                'success' => true,
                'message' => 'Semua soal berhasil disimpan! (' . $savedCount . ' soal)',
                'redirect' => route('guru.kelola-peserta', $exam->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Store Questions Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit questions page
     */
    public function editQuestions($examId)
    {
        $exam = Exam::with('questions')->findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            abort(403);
        }

        return view('guru.edit-soal', compact('exam'));
    }

    /**
     * Update questions with image support
     */
    public function updateQuestions(Request $request, $examId)
    {
        try {
            $exam = Exam::findOrFail($examId);

            if ($exam->guru_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $request->validate([
                'questions' => 'required|array',
                'questions.*.id' => 'required',
                'questions.*.question_text' => 'required|string',
                'questions.*.score' => 'required|integer|min:1',
                'questions.*.image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'questions.*.option_images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
            ]);

            foreach ($request->questions as $index => $questionData) {
                // Upload gambar soal (jika ada)
                $imagePath = null;
                if ($request->hasFile("questions.{$index}.image")) {
                    $file = $request->file("questions.{$index}.image");
                    $imagePath = $file->store('questions', 'public');
                }

                // Upload gambar opsi untuk PG
                $optionImages = [];
                if ($questionData['type'] === 'pg') {
                    for ($i = 0; $i < 4; $i++) {
                        if ($request->hasFile("questions.{$index}.option_images.{$i}")) {
                            $file = $request->file("questions.{$index}.option_images.{$i}");
                            $optionImages[] = $file->store('options', 'public');
                        } else {
                            $optionImages[] = null;
                        }
                    }
                }

                // Ambil options sebagai array
                $options = $questionData['options'] ?? null;
                if (is_string($options)) {
                    $options = json_decode($options, true);
                }

                // Cek apakah soal baru atau existing
                if (strpos($questionData['id'], 'new_') === 0) {
                    // Soal baru - buat baru dengan gambar
                    Question::create([
                        'exam_id' => $exam->id,
                        'type' => $questionData['type'],
                        'question_text' => $questionData['question_text'],
                        'image' => $imagePath,
                        'options' => $options,
                        'option_images' => !empty($optionImages) ? $optionImages : null,
                        'correct_answer' => $questionData['correct_answer'] ?? null,
                        'score' => $questionData['score']
                    ]);
                } else {
                    // Soal existing - update
                    $question = Question::findOrFail($questionData['id']);

                    $updateData = [
                        'question_text' => $questionData['question_text'],
                        'options' => $options,
                        'correct_answer' => $questionData['correct_answer'] ?? $question->correct_answer,
                        'score' => $questionData['score']
                    ];

                    // Update gambar soal jika ada gambar baru
                    if ($imagePath) {
                        // Hapus gambar lama jika ada
                        if ($question->image) {
                            $oldPath = storage_path('app/public/' . $question->image);
                            if (file_exists($oldPath)) {
                                unlink($oldPath);
                            }
                        }
                        $updateData['image'] = $imagePath;
                    }

                    // Update gambar opsi jika ada gambar baru
                    if (!empty($optionImages) && count(array_filter($optionImages)) > 0) {
                        // Hapus gambar opsi lama jika ada
                        if ($question->option_images) {
                            $oldImages = is_array($question->option_images) ? $question->option_images : json_decode($question->option_images, true);
                            if (is_array($oldImages)) {
                                foreach ($oldImages as $oldImg) {
                                    if ($oldImg) {
                                        $oldPath = storage_path('app/public/' . $oldImg);
                                        if (file_exists($oldPath)) {
                                            unlink($oldPath);
                                        }
                                    }
                                }
                            }
                        }
                        $updateData['option_images'] = $optionImages;
                    }

                    $question->update($updateData);
                }
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Upadate soal berhasil disimpan!',
                'redirect' => route('guru.kelola-peserta', $examId)
            ]);

        } catch (\Exception $e) {
            Log::error('Update Questions Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a question (beserta gambar-gambarnya)
     */
    public function deleteQuestion($questionId)
    {
        try {
            $question = Question::findOrFail($questionId);
            $exam = $question->exam;

            // Cek kepemilikan
            if ($exam->guru_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Anda tidak memiliki akses ke soal ini'
                ], 403);
            }

            // ========== HAPUS FILE GAMBAR SOAL ==========
            if ($question->image) {
                $imagePath = storage_path('app/public/' . $question->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    Log::info('Gambar soal dihapus: ' . $imagePath);
                }
            }

            // ========== HAPUS GAMBAR OPSI ==========
            if ($question->option_images) {
                $optionImages = is_array($question->option_images)
                    ? $question->option_images
                    : json_decode($question->option_images, true);

                if (is_array($optionImages)) {
                    foreach ($optionImages as $optImage) {
                        if ($optImage) {
                            $optPath = storage_path('app/public/' . $optImage);
                            if (file_exists($optPath)) {
                                unlink($optPath);
                                Log::info('Gambar opsi dihapus: ' . $optPath);
                            }
                        }
                    }
                }
            }

            // ========== HAPUS SOAL DARI DATABASE ==========
            $questionIdDeleted = $question->id;
            $question->delete();

            Log::info('Soal berhasil dihapus', [
                'question_id' => $questionIdDeleted,
                'exam_id' => $exam->id,
                'type' => $question->type,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Soal dan gambar berhasil dihapus!',
                'deleted_id' => $questionIdDeleted
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Question Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete question image only (without deleting the question)
     */
    public function deleteQuestionImage($questionId)
    {
        try {
            $question = Question::findOrFail($questionId);
            $exam = $question->exam;

            // Cek kepemilikan
            if ($exam->guru_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Anda tidak memiliki akses ke soal ini'
                ], 403);
            }

            // Hapus file gambar soal
            if ($question->image) {
                $imagePath = storage_path('app/public/' . $question->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                // Update database hapus path gambar
                $question->update(['image' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Gambar soal berhasil dihapus!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada gambar yang ditemukan'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Question Image Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete option image only (without deleting the question)
     */
    public function deleteOptionImage($questionId, $optionIndex)
    {
        try {
            $question = Question::findOrFail($questionId);
            $exam = $question->exam;

            // Cek kepemilikan
            if ($exam->guru_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Ambil option_images
            $optionImages = is_array($question->option_images) ? $question->option_images : json_decode($question->option_images, true);

            if ($optionImages && isset($optionImages[$optionIndex]) && $optionImages[$optionIndex]) {
                // Hapus file gambar
                $imagePath = storage_path('app/public/' . $optionImages[$optionIndex]);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

                // Update array option_images
                $optionImages[$optionIndex] = null;
                $question->update(['option_images' => $optionImages]);

                return response()->json([
                    'success' => true,
                    'message' => 'Gambar opsi berhasil dihapus!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada gambar yang ditemukan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplateExcel($examId)
    {
        $exam = Exam::findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            abort(403);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ========== SET JUDUL ==========
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT SOAL UJIAN CBT SYSTEM');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF4472C4');
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFFFFFF');

        // ========== BARIS PETUNJUK ==========
        $sheet->setCellValue('A3', '📌 PETUNJUK PENGISIAN: (KUNCI JAWABAN ESAY: HINDARI PENYEBUTAN SATU PER SATU KATA CONTOH [Karnivora, Omnivora, Herbivora])');
        $sheet->mergeCells('A3:I3');
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $instructions = [
            '1. Isi data soal pada baris yang telah disediakan (mulai baris 8)',
            '2. Untuk soal Pilihan Ganda (PG): isi kolom A, B, C, D, E, F, G, H',
            '3. Untuk soal Essay: isi kolom A, B, C, I (kolom D s/d H dikosongkan)',
            '4. Kolom NILAI diisi angka (minimal 1, maksimal 100)',
            '5. Jangan menghapus atau mengubah struktur header tabel!',
            '6. Setelah selesai mengisi, simpan file dan upload kembali'
        ];

        $row = 4;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue('A' . $row, $instruction);
            $sheet->mergeCells('A' . $row . ':I' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setSize(10);
            $sheet->getStyle('A' . $row)->getFont()->getColor()->setARGB('FF666666');
            $row++;
        }

        // ========== HEADER TABEL (DENGAN BORDER) ==========
        $headers = [
            'A' => 'TIPE SOAL',
            'B' => 'SOAL',
            'C' => 'NILAI (1-100)',
            'D' => 'KUNCI JAWABAN',
            'E' => 'OPSI A',
            'F' => 'OPSI B',
            'G' => 'OPSI C',
            'H' => 'OPSI D',
            'I' => 'KUNCI ESSAY'
        ];

        $headerRow = 8;
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $headerRow, $header);
            $sheet->getColumnDimension($col)->setWidth(20);
            if ($col == 'B') $sheet->getColumnDimension($col)->setWidth(50);
            if ($col == 'I') $sheet->getColumnDimension($col)->setWidth(40);
        }

        // ========== SUB HEADER (KETERANGAN) ==========
        $subHeaders = [
            'A' => '(pg/essay)',
            'B' => '(Tulis soal di sini)',
            'C' => '(1-100)',
            'D' => '(A/B/C/D)',
            'E' => '(Teks Opsi A)',
            'F' => '(Teks Opsi B)',
            'G' => '(Teks Opsi C)',
            'H' => '(Teks Opsi D)',
            'I' => '(Untuk soal Essay)'
        ];

        $subHeaderRow = 9;
        foreach ($subHeaders as $col => $subHeader) {
            $sheet->setCellValue($col . $subHeaderRow, $subHeader);
            $sheet->getStyle($col . $subHeaderRow)->getFont()->setSize(9);
            $sheet->getStyle($col . $subHeaderRow)->getFont()->getColor()->setARGB('FF888888');
        }

        // ========== STYLE HEADER TABEL ==========
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]
            ]
        ];
        $sheet->getStyle('A8:I8')->applyFromArray($headerStyle);

        // ========== CONTOH SOAL ==========
        $examples = [
            [
                'pg', 'Apa kepanjangan dari PHP?', '5', 'B',
                'Personal Home Page', 'PHP: Hypertext Preprocessor',
                'Preprocessed Hypertext Page', 'Pre Hypertext Processor', ''
            ],
            [
                'pg', 'Bahasa pemrograman apa yang digunakan untuk mengembangkan Laravel?', '5', 'C',
                'Python', 'Java', 'PHP', 'Ruby', ''
            ],
            [
                'essay', 'Jelaskan apa yang dimaksud dengan Object Oriented Programming (OOP)!', '20',
                '', '', '', '', '', 'Pemrograman berorientasi objek adalah paradigma pemrograman yang menggunakan objek dan kelas'
            ],
            [
                'essay', 'Apa yang dimaksud dengan Puisi?', '25',
                '', '', '', '', '', 'Puisi adalah karya sastra yang mengungkapkan perasaan pikiran atau imajinasi penyair dengan bahasa yang indah dan penuh makna'
            ]
        ];

        $exampleStartRow = 11;
        foreach ($examples as $index => $example) {
            $currentRow = $exampleStartRow + $index;
            $sheet->setCellValue('A' . $currentRow, $example[0]);
            $sheet->setCellValue('B' . $currentRow, $example[1]);
            $sheet->setCellValue('C' . $currentRow, $example[2]);
            $sheet->setCellValue('D' . $currentRow, $example[3]);
            $sheet->setCellValue('E' . $currentRow, $example[4]);
            $sheet->setCellValue('F' . $currentRow, $example[5]);
            $sheet->setCellValue('G' . $currentRow, $example[6]);
            $sheet->setCellValue('H' . $currentRow, $example[7]);
            $sheet->setCellValue('I' . $currentRow, $example[8]);

            // Warna background untuk contoh soal
            $bgColor = ($example[0] == 'pg') ? 'FFE6F0FF' : 'FFFFF0E6';
            $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->getFill()
                  ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgColor);
        }

        // ========== BARIS KOSONG UNTUK DIISI (20 BARIS) ==========
        $emptyStartRow = $exampleStartRow + count($examples);
        for ($i = 0; $i < 20; $i++) {
            $currentRow = $emptyStartRow + $i;
            for ($col = 'A'; $col <= 'I'; $col++) {
                $sheet->setCellValue($col . $currentRow, '');
            }
        }

        // ========== BORDER UNTUK SEMUA BARIS DATA ==========
        $dataEndRow = $emptyStartRow + 19;
        $borderStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]
            ]
        ];
        $sheet->getStyle('A8:I' . $dataEndRow)->applyFromArray($borderStyle);

        // ========== TINGGI BARIS ==========
        $sheet->getRowDimension($headerRow)->setRowHeight(25);
        for ($i = $exampleStartRow; $i <= $dataEndRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(40);
        }

        // ========== WRAP TEXT UNTUK KOLOM SOAL ==========
        $sheet->getStyle('B8:B' . $dataEndRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('I8:I' . $dataEndRow)->getAlignment()->setWrapText(true);

        // ========== FREEZE PANE ==========
        $sheet->freezePane('A10');

        // ========== SET VIEW ==========
        $sheet->setSelectedCell('A11');

        // ========== CATATAN PENUTUP ==========
        $noteRow = $dataEndRow + 2;
        $sheet->setCellValue('A' . $noteRow, '=========================================');
        $sheet->mergeCells('A' . $noteRow . ':I' . $noteRow);
        $sheet->setCellValue('A' . ($noteRow + 1), '📌 CATATAN:');
        $sheet->mergeCells('A' . ($noteRow + 1) . ':I' . ($noteRow + 1));
        $sheet->setCellValue('A' . ($noteRow + 2), '- Untuk menambah soal, isi pada baris kosong di atas');
        $sheet->mergeCells('A' . ($noteRow + 2) . ':I' . ($noteRow + 2));
        $sheet->setCellValue('A' . ($noteRow + 3), '- Simpan file dengan format Excel Workbook (.xlsx) atau CSV');
        $sheet->mergeCells('A' . ($noteRow + 3) . ':I' . ($noteRow + 3));
        $sheet->setCellValue('A' . ($noteRow + 4), '- Upload file kembali ke sistem');
        $sheet->mergeCells('A' . ($noteRow + 4) . ':I' . ($noteRow + 4));

        // ========== DOWNLOAD FILE ==========
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="template_soal_' . $exam->judul . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
 * Import questions from Excel file only
 */
public function importQuestionsFromExcel(Request $request, $examId)
{
    $exam = Exam::findOrFail($examId);

    if ($exam->guru_id !== Auth::id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $request->validate([
        'import_file' => 'required|file|mimes:xlsx,xls|max:5120'
    ]);

    try {
        $file = $request->file('import_file');

        // Load Excel file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Parse Excel ke questions
        $questions = $this->parseExcelToQuestions($rows);

        if (empty($questions)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data soal yang valid ditemukan! Pastikan format Excel sesuai template.'
            ]);
        }

        // Simpan soal ke database
        $importedQuestions = [];
        foreach ($questions as $questionData) {
            $question = Question::create([
                'exam_id' => $exam->id,
                'type' => $questionData['type'],
                'question_text' => $questionData['question_text'],
                'options' => $questionData['options'] ?? null,
                'correct_answer' => $questionData['correct_answer'],
                'score' => $questionData['score'],
                'option_images' => null
            ]);

            $importedQuestions[] = $question;
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil import ' . count($questions) . ' soal!',
            'questions' => $this->formatQuestionsForDisplay($importedQuestions)
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}

    /**
     * Format questions untuk ditampilkan di frontend
     */
    private function formatQuestionsForDisplay($questions)
    {
        $formatted = [];
        foreach ($questions as $question) {
            $options = is_array($question->options) ? $question->options : json_decode($question->options, true);

            $formatted[] = [
                'id' => $question->id,
                'type' => $question->type,
                'question_text' => $question->question_text,
                'score' => $question->score,
                'correct_answer' => $question->correct_answer,
                'options' => $options ?: ['', '', '', ''],
                'image' => $question->image,
                'option_images' => $question->option_images
            ];
        }
        return $formatted;
    }

    private function parseExcelToQuestions($rows)
    {
        $questions = [];
        $startProcessing = false;

        foreach ($rows as $row) {
            if (empty($row) || count($row) < 3) continue;

            $firstCell = trim($row[0] ?? '');

            // Mulai proses jika menemukan 'pg' atau 'essay'
            if (in_array(strtolower($firstCell), ['pg', 'essay'])) {
                $type = strtolower($firstCell);
                $questionText = trim($row[1] ?? '');
                $score = intval($row[2] ?? 0);
                $correctAnswer = trim($row[3] ?? '');

                if (!empty($questionText) && $score > 0) {
                    if ($type === 'pg') {
                        $options = [
                            trim($row[4] ?? ''),
                            trim($row[5] ?? ''),
                            trim($row[6] ?? ''),
                            trim($row[7] ?? '')
                        ];

                        $questions[] = [
                            'type' => 'pg',
                            'question_text' => $questionText,
                            'score' => $score,
                            'correct_answer' => strtoupper($correctAnswer),
                            'options' => $options
                        ];
                    } else {
                        $essayKeyword = trim($row[8] ?? '');
                        $questions[] = [
                            'type' => 'essay',
                            'question_text' => $questionText,
                            'score' => $score,
                            'correct_answer' => $essayKeyword,
                            'options' => null
                        ];
                    }
                }
            }
        }

        return $questions;
    }

    /**
     * Manage participants
     */
    public function manageParticipants($examId)
    {
        $exam = Exam::with(['participants.siswa', 'questions'])->findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            abort(403);
        }

        $participants = $exam->participants;
        $currentParticipants = $participants->count();

        // Hitung jumlah soal
        $totalQuestions = $exam->questions->count();
        $pgCount = $exam->questions->where('type', 'pg')->count();
        $essayCount = $exam->questions->where('type', 'essay')->count();

        return view('guru.kelola-peserta', compact('exam', 'participants', 'currentParticipants',
                    'totalQuestions', 'pgCount', 'essayCount'));
    }

    /**
     * Get exam link
     */
    public function getExamLink($examId)
    {
        $exam = Exam::findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $link = url('/ujian/register/' . $exam->kode_ujian);

        return response()->json([
            'success' => true,
            'link' => $link
        ]);
    }

    /**
     * Start exam
     */
    public function startExam($examId)
    {
        $exam = Exam::findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $currentParticipants = $exam->participants()->count();

        if ($currentParticipants < $exam->jumlah_peserta) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah peserta belum mencapai target'
            ], 400);
        }

        $exam->update(['status' => 'berlangsung']);

        return response()->json([
            'success' => true,
            'message' => 'Ujian dimulai!'
        ]);
    }

    /**
     * Exam results
     */
    public function examResults($examId)
    {
        $exam = Exam::with(['participants.siswa'])->findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            abort(403);
        }

        $participants = $exam->participants()
                            ->orderBy('total_score', 'desc')
                            ->orderBy('finished_at', 'asc')
                            ->get();

        return view('guru.hasil-ujian', compact('exam', 'participants'));
    }

    /**
     * Get current participants count (AJAX)
     */
    public function getCurrentParticipants($examId)
    {
        $exam = Exam::findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $currentParticipants = $exam->participants()->count();

        return response()->json([
            'current_peserta' => $currentParticipants,
            'target_peserta' => $exam->jumlah_peserta
        ]);
    }

    /**
     * Update target peserta ujian
     */
    public function updateTargetPeserta(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'jumlah_peserta' => 'required|integer|min:1'
        ]);

        $exam->update([
            'jumlah_peserta' => $request->jumlah_peserta
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Target peserta berhasil diupdate!',
            'new_target' => $request->jumlah_peserta
        ]);
    }

    /**
     * Check ready participants (AJAX)
     */
    public function checkReadyParticipants($examId)
    {
        $exam = Exam::findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $participants = ExamParticipant::where('exam_id', $examId)->with('siswa')->get();

        $totalRegistered = $participants->count();
        $totalReady = $participants->where('is_ready', true)->count();
        $totalWaiting = $totalRegistered - $totalReady;
        $totalCompleted = $participants->where('status', 'selesai')->count();
        $targetParticipants = $exam->jumlah_peserta;

        return response()->json([
            'success' => true,
            'total_registered' => $totalRegistered,
            'total_ready' => $totalReady,
            'total_waiting' => $totalWaiting,
            'total_completed' => $totalCompleted,
            'target_participants' => $targetParticipants,
            'can_start' => $totalReady >= $targetParticipants && $exam->status === 'menunggu',
            'participants' => $participants->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->siswa->name,
                    'nomor_absen' => $p->siswa->nomor_absen,
                    'kelas' => $p->siswa->kelas,
                    'jurusan' => $p->siswa->jurusan,
                    'is_ready' => $p->is_ready,
                    'ready_at' => $p->ready_at ? $p->ready_at->format('H:i:s') : null,
                    'status' => $p->status
                ];
            })
        ]);
    }

    /**
     * Update status ujian menjadi selesai jika semua peserta sudah selesai
     */
    public function updateExamStatus($examId)
    {
        $exam = Exam::findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $totalParticipants = $exam->participants()->count();
        $completedParticipants = $exam->participants()->where('status', 'selesai')->count();

        if ($totalParticipants > 0 && $totalParticipants === $completedParticipants) {
            $exam->update(['status' => 'selesai']);
            return response()->json([
                'success' => true,
                'message' => 'Status ujian telah diubah menjadi selesai'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Belum semua peserta selesai. Peserta selesai: {$completedParticipants} dari {$totalParticipants}"
        ]);
    }

    /**
     * Tampilkan semua hasil ujian guru
     */
    public function semuaHasilUjian()
    {
        $user = Auth::user();

        // Ambil semua ujian milik guru
        $exams = Exam::where('guru_id', $user->id)
                    ->with(['participants.siswa'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Hitung statistik per ujian
        $examStats = [];
        foreach ($exams as $exam) {
            $totalParticipants = $exam->participants->count();
            $completedParticipants = $exam->participants->where('status', 'selesai')->count();
            $averageScore = $exam->participants->where('status', 'selesai')->avg('total_score') ?? 0;

            $examStats[$exam->id] = [
                'total_participants' => $totalParticipants,
                'completed_participants' => $completedParticipants,
                'average_score' => round($averageScore, 2),
                'completion_rate' => $totalParticipants > 0 ? round(($completedParticipants / $totalParticipants) * 100, 1) : 0
            ];
        }

        return view('guru.semua-hasil', compact('exams', 'examStats'));
    }

    /**
     * Detail hasil ujian per ujian
     */
    public function detailHasilUjian($examId)
    {
        $exam = Exam::with(['participants.siswa', 'questions'])
                    ->where('guru_id', Auth::id())
                    ->findOrFail($examId);

        // Hitung total nilai maksimal dari semua soal
        $totalMaxScore = $exam->questions->sum('score');

        $participants = $exam->participants()
                            ->with('siswa')
                            ->orderBy('total_score', 'desc')
                            ->orderBy('finished_at', 'asc')
                            ->get();

        // Hitung statistik
        $totalParticipants = $participants->count();
        $completedParticipants = $participants->where('status', 'selesai')->count();
        $averageScore = $participants->where('status', 'selesai')->avg('total_score') ?? 0;
        $highestScore = $participants->where('status', 'selesai')->max('total_score') ?? 0;
        $lowestScore = $participants->where('status', 'selesai')->min('total_score') ?? 0;

        // Hitung distribusi nilai berdasarkan persentase dari total maksimal
        $scoreDistribution = [
            'A' => 0, // 85-100%
            'B' => 0, // 70-84%
            'C' => 0, // 60-69%
            'D' => 0, // 50-59%
            'E' => 0, // < 50%
        ];

        foreach ($participants as $p) {
            if ($p->status !== 'selesai') continue;

            $percentage = $totalMaxScore > 0 ? ($p->total_score / $totalMaxScore) * 100 : 0;

            if ($percentage >= 85) {
                $scoreDistribution['A']++;
            } elseif ($percentage >= 70) {
                $scoreDistribution['B']++;
            } elseif ($percentage >= 60) {
                $scoreDistribution['C']++;
            } elseif ($percentage >= 50) {
                $scoreDistribution['D']++;
            } else {
                $scoreDistribution['E']++;
            }
        }

        // Hitung rentang nilai untuk setiap grade
        $gradeRanges = [
            'A' => ['min' => ceil($totalMaxScore * 0.85), 'max' => $totalMaxScore],
            'B' => ['min' => ceil($totalMaxScore * 0.70), 'max' => floor($totalMaxScore * 0.84)],
            'C' => ['min' => ceil($totalMaxScore * 0.60), 'max' => floor($totalMaxScore * 0.69)],
            'D' => ['min' => ceil($totalMaxScore * 0.50), 'max' => floor($totalMaxScore * 0.59)],
            'E' => ['min' => 0, 'max' => floor($totalMaxScore * 0.49)],
        ];

        return view('guru.detail-hasil', compact('exam', 'participants', 'totalParticipants',
                    'completedParticipants', 'averageScore', 'highestScore', 'lowestScore',
                    'scoreDistribution', 'totalMaxScore', 'gradeRanges'));
    }

    /**
     * Export hasil ujian ke Excel (opsional)
     */
    public function exportHasilUjian($examId)
    {
        $exam = Exam::findOrFail($examId);

        if ($exam->guru_id !== Auth::id()) {
            abort(403);
        }

        $participants = $exam->participants()
                            ->with('siswa')
                            ->orderBy('total_score', 'desc')
                            ->get();

        // Generate CSV
        $filename = 'hasil_ujian_' . $exam->judul . '_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Header CSV
        fputcsv($handle, ['No', 'Nama Siswa', 'Kelas', 'Jurusan', 'Nomor Absen', 'Nilai', 'Status', 'Selesai Pada']);

        // Data CSV
        foreach ($participants as $index => $p) {
            fputcsv($handle, [
                $index + 1,
                $p->siswa->name,
                $p->siswa->kelas ?? '-',
                $p->siswa->jurusan ?? '-',
                $p->siswa->nomor_absen ?? '-',
                $p->total_score ?? 0,
                $p->status == 'selesai' ? 'Selesai' : 'Belum Selesai',
                $p->finished_at ? $p->finished_at->format('d/m/Y H:i:s') : '-'
            ]);
        }

        fclose($handle);
        exit;
    }


}