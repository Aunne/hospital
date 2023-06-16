<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Admin
{
    public function getDepartmentName($departmentName)
    {
        $sql = "select * from department where departmentName = :departmentName ";
        $response = DB::select($sql, ['departmentName' => $departmentName]);
        return $response;
    }

    public function adminGetAllDepartment()
    {
        $sql = "select * from department";
        $response = DB::select($sql);
        return $response;
    }
    public function addDepartment($departmentName)
    {
        $sql = "insert into department ( departmentName) values ( :departmentName)";
        $response = DB::insert($sql, ['departmentName' => $departmentName]);
        return $response;
    }

    public function getDivisionName($divisionName)
    {
        $sql = "select * from division where divisionName = :divisionName ";
        $response = DB::select($sql, ['divisionName' => $divisionName]);
        return $response;
    }

    public function getDivisionID($divisionID)
    {
        $sql = "select * from division where divisionID = :divisionID ";
        $response = DB::select($sql, ['divisionID' => $divisionID]);
        return $response;
    }

    public function updateDivision($divisionID, $divisionName, $departmentID)
    {
        $sql = "
        update division set divisionName = :divisionName, departmentID = :departmentID 
        where divisionID = :divisionID";
        $response = DB::update($sql, ['divisionID' => $divisionID, 'divisionName' => $divisionName, 'departmentID' => $departmentID]);
        return $response;
    }


    public function adminGetAllDivision($departmentID)
    {
        $sql = "select * from division where departmentID = :departmentID";
        $response = DB::select($sql, ['departmentID' => $departmentID]);
        return $response;
    }

    public function addDivision($divisionName, $departmentID)
    {
        $sql = "insert into division ( divisionName, departmentID) values ( :divisionName, :departmentID)";
        $response = DB::insert($sql, ['divisionName' => $divisionName, 'departmentID' => $departmentID]);
        return $response;
    }

    public function getShiftAllParameter($doctorID, $divisionID, $date, $timePeriod)
    {
        $sql = "select * from shift 
                where doctorID	 = :doctorID 
                AND divisionID = :divisionID
                AND date = :date
                AND timePeriod = :timePeriod";
        $response = DB::select($sql, ['doctorID' => $doctorID, 'divisionID' => $divisionID, 'date' => $date, 'timePeriod' => $timePeriod]);
        return $response;
    }

    public function addShift($doctorID, $divisionID, $date, $timePeriod)
    {
        $sql = "insert into shift ( doctorID, divisionID, date, timePeriod) values ( :doctorID, :divisionID, :date, :timePeriod)";
        $response = DB::insert($sql, ['doctorID' => $doctorID, 'divisionID' => $divisionID, 'date' => $date, 'timePeriod' => $timePeriod]);
        return $response;
    }

    public function getDivisionDepartmentID($departmentID)  
    {
        $sql = "select * from division where departmentID = :departmentID";
        $response = DB::select($sql, ['departmentID' => $departmentID]);
        return $response;
    }
}