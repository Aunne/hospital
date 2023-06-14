<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;


class Appointment
{
    public function getAppointment($shiftID)
    {
        $sql = "
        select * 
        from appointment 
        where shiftID = :shiftID 
        AND isCancel = false";
        $response = DB::select($sql, ['shiftID' => $shiftID]);
        return $response;
    }

    public function addAppointment($userID,$shiftID,$appointmentNumber){
        $sql = "
        insert into appointment ( userID, shiftID, appointmentNumber, isCancel) 
        values ( :userID, :shiftID, :appointmentNumber, :isCancel)";
        $response = DB::insert($sql, ['userID' => $userID, 'shiftID' => $shiftID, 'appointmentNumber' => $appointmentNumber, 'isCancel'=>'false']);
        return $response;
    }
}