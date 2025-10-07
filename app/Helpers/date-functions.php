<?php

if ( !function_exists('formatBulanRomawi') )
{
    function formatBulanRomawi($date){
        $_bulan = date('m', strtotime($date));
        $bulan_romawi = [1 => 'I','II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    
        return $bulan_romawi[(int)$_bulan];

    }
}

if ( !function_exists('formatHariIndonesia') )
{
    function formatHariIndonesia($date){
        $_hari = date('D', strtotime($date));
        switch($_hari){
            case 'Sun':
                $hari = "Minggu";
            break;
    
            case 'Mon':			
                $hari = "Senin";
            break;
    
            case 'Tue':
                $hari = "Selasa";
            break;
    
            case 'Wed':
                $hari = "Rabu";
            break;
    
            case 'Thu':
                $hari = "Kamis";
            break;
    
            case 'Fri':
                $hari = "Jumat";
            break;
    
            case 'Sat':
                $hari = "Sabtu";
            break;
            
            default:
                $hari = "Tidak di ketahui";		
            break;
        }
    
        return $hari;

    }
}

if ( !function_exists('formatTanggalIndonesia') )
{
    function kasihNol($data) {
        if($data < 10)
        {
            return '0'+$data;
        }else{
            return $data;
        }
    }
}

if ( !function_exists('formatTanggalIndonesia') )
{
    function formatTanggalIndonesia($date){
        $tanggal = date('d/m/Y', strtotime($date));
        return $tanggal;
    }
}

if ( !function_exists('formatTanggalIndonesia2') )
{
    function formatTanggalIndonesia2($date){
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tanggal = date('d/m/Y', strtotime($date));
        $formated = kasihNol(explode('/', $tanggal)[0]) . ' ' . $bulan[(int)explode('/', $tanggal)[1]-1] . ' ' . explode('/', $tanggal)[2];
        return $formated;
    }
}

if ( !function_exists('getMonth') )
{
    function getMonth($monthNumber) {
        $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $monthNumber = (int)$monthNumber;
        return $months[$monthNumber];
    }
}

function formatBulanIndonesia($tanggal) {
    $bulan = date('m', strtotime($tanggal));
    $namaBulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    return $namaBulan[$bulan];
}
