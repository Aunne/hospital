<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;


class shift
{
    public function getShift($shiftID)
    {
        $sql = "select * from shift where shiftID = :shiftID ";
        $response = DB::select($sql, ['shiftID' => $shiftID]);
        return $response;
    }
}