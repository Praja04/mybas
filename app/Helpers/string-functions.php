<?php

if (!function_exists('penyebut')) {
    function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }
}

if (!function_exists('angkaTerbilang')) {
    function angkaTerbilang($angka)
    {
        if ($angka < 0) {
            $angka = "minus " . trim(penyebut($angka));
        } else {
            $angka = trim(penyebut($angka));
        }
        return $angka;
    }
}

if (!function_exists('angkaRomawi')) {
    function angkaRomawi($angka)
    {
        $angka_romawi = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
        return $angka_romawi[$angka];
    }
}

if (!function_exists('setEmail')) {
    function setEmail($email)
    {
        $mailer = 'smtp';

        if ($email != null && $email != '') {
            switch (explode('@', $email)[1]) {
                case 'myemail.pas':
                    $mailer = 'myemail';
                    break;
                case 'prakarsaalamsegar.com':
                    $mailer = 'prakarsa';
                    break;
                default:
                    $mailer = 'smtp';
                    break;
            }
        }
        return $mailer;
    }
}
