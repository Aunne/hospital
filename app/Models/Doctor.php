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

    public function getDoctorID($doctorID)
    {
        $sql = "select * from doctor where doctorID = :doctorID ";
        $response = DB::select($sql, ['doctorID' => $doctorID]);
        return $response;
    }

    public function updateDoctor($doctorID, $doctorName, $doctorIDNumber)
    {
        $sql = "
        update doctor set doctorName = :doctorName, doctorIDNumber = :doctorIDNumber 
        where doctorID = :doctorID";
        $response = DB::update($sql, ['doctorID' => $doctorID, 'doctorName' => $doctorName, 'doctorIDNumber' => $doctorIDNumber]);
        return $response;

    }

    public function adminGetAllDoctor()
    {
        $sql = "select * from doctor";
        $response = DB::select($sql);
        return $response;
    }

    public function getShiftDoctorID($doctorID)
    {
        $sql = "select * from shift where doctorID = :doctorID ";
        $response = DB::select($sql, ['doctorID' => $doctorID]);
        return $response;   
    }

    public function deleteDoctor($doctorID)
    {
        $sql = "delete from doctor where doctorID = :doctorID";
        $response = DB::delete($sql, ['doctorID' => $doctorID]);
        return $response;   
    }
}