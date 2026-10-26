<?php

namespace App\Support;

/**
 * Captcha aritmatika buatan sendiri (tanpa layanan eksternal).
 * Soal dibuat baru setiap formulir dirender; jawaban disimpan di session.
 */
class Captcha
{
    public static function enabled(): bool
    {
        return (bool) config('captcha.enabled', true);
    }

    /**
     * Buat soal baru, simpan jawabannya di session, kembalikan teks soal.
     */
    public static function question(): string
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);

        if (random_int(0, 1) === 1) {
            $soal = "{$a} + {$b}";
            $jawaban = $a + $b;
        } else {
            // Pengurangan: pastikan hasil tidak negatif.
            [$a, $b] = $a >= $b ? [$a, $b] : [$b, $a];
            $soal = "{$a} - {$b}";
            $jawaban = $a - $b;
        }

        session(['captcha_answer' => $jawaban]);

        return $soal;
    }

    /**
     * Validasi jawaban. Bila captcha nonaktif selalu lolos.
     * Jawaban yang sudah dipakai langsung dibuang (satu kali pakai).
     */
    public static function check(mixed $input): bool
    {
        if (!self::enabled()) {
            return true;
        }

        $expected = session()->pull('captcha_answer');

        return $expected !== null
            && is_numeric($input)
            && (int) $input === (int) $expected;
    }
}
