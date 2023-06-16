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

    public function adminGetShiftDivisionID($divisionID)
    {
        $todayY = date("Y");
        $todayM = date("m");

        $sql = "
        select *
        from shift, doctor, division, department
        where shift.doctorID = doctor.doctorID
        AND shift.divisionID = division.divisionID
        AND division.departmentID = department.departmentID
        AND year(shift.date) >= :todayY
        AND month(shift.date) >= :todayM
        AND shift.divisionID = :divisionID; ";

        $response = DB::select($sql, ['divisionID' => $divisionID, 'todayY' => $todayY, 'todayM' => $todayM]);
        return $response;
    }

    public function getShiftID($shiftID)
    {
        $sql = "
        select * 
        from shift, doctor, division
        where shift.doctorID = doctor.doctorID
        AND shift.divisionID = division.divisionID
        AND shift.shiftID = :shiftID ";
        $response = DB::select($sql, ['shiftID' => $shiftID]);
        return $response;
    }

    public function updateShift($shiftID, $doctorID, $divisionID, $date, $timePeriod)
    {
        $sql = "
        update shift 
        set doctorID = :doctorID, divisionID = :divisionID, date = :date, timePeriod = :timePeriod 
        where shiftID = :shiftID";
        $response = DB::update($sql, ['shiftID' => $shiftID, 'doctorID' => $doctorID, 'divisionID' => $divisionID, 'date' => $date, 'timePeriod' => $timePeriod]);
        return $response;

    }

    public function getShiftDivisionID( $divisionID )
    {
        $sql = "
        select *
        from shift
        where divisionID = :divisionID; ";

        $response = DB::select($sql, ['divisionID' => $divisionID]);
        return $response;
    }

    public function deleteDivision($divisionID)
    {
        $sql = "
        delete from division
        where divisionID = :divisionID";
        $response = DB::delete($sql, ['divisionID' => $divisionID]);
        return $response;
    }

    public function deleteShift($shiftID)
    {
        $sql = "
        delete from shift
        where shiftID = :shiftID";
        $response = DB::delete($sql, ['shiftID' => $shiftID]);
        return $response;
    }
}