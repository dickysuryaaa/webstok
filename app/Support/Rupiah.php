<?php

namespace App\Support;

class Rupiah
{
    /** Format rupiah bulat, contoh: Rp 12.500 */
    public static function format(int|float|null $nilai): string
    {
        $nilai = (int) round($nilai ?? 0);
        $teks = number_format(abs($nilai), 0, ',', '.');

        return ($nilai < 0 ? '-Rp ' : 'Rp ').$teks;
    }
}
