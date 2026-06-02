<?php

namespace App\Services;

use NlpTools\Similarity\CosineSimilarity;
use NlpTools\Tokenizers\WhitespaceTokenizer;

class AIEssayScoringService
{
    private $cosineSimilarity;
    private $tokenizer;
    private $stopWords;

    public function __construct()
    {
        $this->cosineSimilarity = new CosineSimilarity();
        $this->tokenizer = new WhitespaceTokenizer();

        // Daftar stop words (kata-kata umum yang tidak memiliki makna signifikan)
        $this->stopWords = [
            'yang', 'dan', 'atau', 'dari', 'ke', 'di', 'ini', 'itu', 'tersebut',
            'adalah', 'ialah', 'untuk', 'dengan', 'pada', 'dalam', 'oleh', 'karena',
            'jika', 'maka', 'sebagai', 'akan', 'telah', 'bisa', 'dapat', 'juga',
            'sangat', 'cukup', 'sudah', 'masih', 'lagi', 'saja', 'pun', 'per',
            'the', 'and', 'of', 'to', 'in', 'for', 'on', 'with', 'by', 'at',
            'is', 'are', 'was', 'were', 'be', 'been', 'being', 'a', 'an'
        ];
    }

    /**
     * Menilai jawaban essay menggunakan NLP Cosine Similarity
     *
     * @param string $jawabanUser Jawaban dari peserta
     * @param string $kunciJawaban Kunci jawaban dari guru
     * @param int $nilaiMaksimal Nilai maksimal soal
     * @return array Hasil penilaian
     */
    public function nilai($jawabanUser, $kunciJawaban, $nilaiMaksimal)
    {
        // 1. Preprocessing teks
        $processedJawaban = $this->preprocessText($jawabanUser);
        $processedKunci = $this->preprocessText($kunciJawaban);

        // 2. Tokenisasi
        $tokensJawaban = $this->tokenizer->tokenize($processedJawaban);
        $tokensKunci = $this->tokenizer->tokenize($processedKunci);

        // 3. Hitung Cosine Similarity
        $similarity = $this->cosineSimilarity->similarity($tokensJawaban, $tokensKunci);

        // 4. Konversi ke nilai
        $score = round($similarity * $nilaiMaksimal);

        // 5. Batasi nilai tidak melebihi maksimal
        $score = min($score, $nilaiMaksimal);

        return [
            'similarity' => round($similarity * 100, 2), // dalam persen
            'score' => $score,
            'max_score' => $nilaiMaksimal,
            'jawaban_processed' => $processedJawaban,
            'kunci_processed' => $processedKunci
        ];
    }

    /**
     * Preprocessing teks: lowercase, hapus stop words, hapus tanda baca
     */
    private function preprocessText($text)
    {
        // Ubah ke lowercase
        $text = strtolower($text);

        // Hapus tanda baca (koma, titik, dll)
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        // Hapus spasi berlebih
        $text = preg_replace('/\s+/', ' ', $text);

        // Trim whitespace
        $text = trim($text);

        // Hapus stop words
        $words = explode(' ', $text);
        $filteredWords = array_filter($words, function($word) {
            return !in_array($word, $this->stopWords) && strlen($word) > 1;
        });

        return implode(' ', $filteredWords);
    }
}