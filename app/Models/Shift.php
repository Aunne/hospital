<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;


class shift
{
    public function getShift($shiftID)
    {
        $todayY = date("Y");
        $todayM = date("m");

        $sql = "select * 
        from shift, doctor, division, department
        where shift.doctorID = doctor.doctorID
        AND shift.divisionID = division.divisionID 
        AND division.departmentID = department.departmentID
        AND year(shift.date) >= :todayY
        AND month(shift.date) >= :todayM
        AND shiftID = :shiftID ";
        $response = DB::select($sql, ['shiftID' => $shiftID, 'todayY' => $todayY, 'todayM' => $todayM]);
        return $response;
    }
}