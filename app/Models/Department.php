<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Department
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

    public function getDepartmentID($departmentID)
    {
        $sql = "select * from department where departmentID = :departmentID ";
        $response = DB::select($sql, ['departmentID' => $departmentID]);
        return $response;
    }

    public function updateDepartment($departmentID, $departmentName)
    {
        $sql = "update department set departmentName = :departmentName where departmentID = :departmentID";
        $response = DB::update($sql, ['departmentName' => $departmentName, 'departmentID' => $departmentID]);
        return $response;
    }

    public function deleteDepartment($departmentID)
    {
        $sql = "delete from department where departmentID = :departmentID";
        $response = DB::delete($sql, ['departmentID' => $departmentID]);
        return $response;
    }


}