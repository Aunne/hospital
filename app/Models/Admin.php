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

    public function addDivision($divisionName, $departmentID)
    {
        $sql = "insert into division ( divisionName, departmentID) values ( :divisionName, :departmentID)";
        $response = DB::insert($sql, ['divisionName' => $divisionName, 'departmentID'=> $departmentID]);
        return $response;
    }
}