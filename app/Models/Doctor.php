<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Doctor
{
    public function newDoctor($doctorName, $doctorIDNumber)
    {
        $sql = "insert into doctor ( doctorName, doctorIDNumber) values ( :doctorName, :doctorIDNumber)";
        $response = DB::insert($sql, ['doctorName' => $doctorName, 'doctorIDNumber' => $doctorIDNumber]);
        return $response;
    }

    public function getDoctorIDNumber($doctorIDNumber)
    {
        $sql = "select * from doctor where doctorIDNumber = :doctorIDNumber ";
        $response = DB::select($sql, ['doctorIDNumber' => $doctorIDNumber]);
        return $response;
    }
}