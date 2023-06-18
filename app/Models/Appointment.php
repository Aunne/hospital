<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;


class Appointment
{
    public function getAppointmentUserID($userID)
    {
        $sql = "
        select * 
        from appointment 
        where userID = :userID 
        AND isCancel = 'false'";
        $response = DB::select($sql, ['userID' => $userID]);
        return $response;
    }

    public function getValidAppointmentUserIDShiftID($userID, $shiftID)
    {
        $today = date('Y-m-d');
        $sql = "
        select * 
        from appointment, shift
        where userID = :userID 
        AND appointment.shiftID = shift.shiftID
        AND shift.shiftID = :shiftID
        AND appointment.isCancel = 'false'
        AND shift.date >= :today ";
        $response = DB::select($sql, ['userID' => $userID, 'shiftID' => $shiftID, 'today' => $today]);
        return $response;
    }

    public function getValidAppointmentUserID($userID)
    {
        $today = date('Y-m-d');
        $sql = "
        select * 
        from appointment, shift
        where userID = :userID 
        AND appointment.shiftID = shift.shiftID
        AND appointment.isCancel = 'false'
        AND shift.date >= :today ";
        $response = DB::select($sql, ['userID' => $userID, 'today' => $today]);
        return $response;
    }

    public function getUserAppointDivision($userID , $divisionID, $date){
        $sql = "
        select * 
        from appointment, shift
        where userID = :userID 
        AND appointment.shiftID = shift.shiftID
        AND shift.divisionID = :divisionID
        AND shift.date = :date
        AND appointment.isCancel = 'false'";
        $response = DB::select($sql, ['userID' => $userID, 'divisionID' => $divisionID, 'date' => $date]);
        return $response;
    }
        

    public function getAppointmentShiftID($shiftID)
    {
        $sql = "
        select * 
        from appointment 
        where shiftID = :shiftID 
        AND isCancel = 'false'";
        $response = DB::select($sql, ['shiftID' => $shiftID]);
        return $response;
    }

    public function addAppointment($userID, $shiftID, $appointmentNumber)
    {
        $sql = "
        insert into appointment ( userID, shiftID, appointmentNumber, isCancel) 
        values ( :userID, :shiftID, :appointmentNumber, :isCancel)";
        $response = DB::insert($sql, ['userID' => $userID, 'shiftID' => $shiftID, 'appointmentNumber' => $appointmentNumber, 'isCancel' => 'false']);
        return $response;
    }

    public function cancelAppointment($userID, $shiftID)
    {
        $sql = "
        update appointment,shift 
        set appointment.isCancel = 'true'
        where appointment.shiftID = shift.shiftID
        AND appointment.shiftID = :shiftID
        AND appointment.userID = :userID
        AND appointment.isCancel = 'false'";

        $response = DB::update($sql, ['shiftID' => $shiftID, 'userID' => $userID]);

        return $response;
    }

    public function getAppointmentDivisionID($divisionID)
    {
        $sql = "
        select * 
        from appointment, shift
        where appointment.shiftID = shift.shiftID
        AND shift.divisionID = :divisionID
        AND appointment.isCancel = 'false'";
        $response = DB::select($sql, ['divisionID' => $divisionID]);
        return $response;
    }
}