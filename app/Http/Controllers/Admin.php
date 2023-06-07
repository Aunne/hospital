<?php
namespace App\Http\Controllers;

use App\Models\Admin as AdminModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Admin extends Controller
{
    protected $adminmodel;
    public function __construct()
    {
        $this->adminmodel = new AdminModel();
    }


    public function addDepartment(Request $request)
    {
        $departmentName = $request->input('departmentName');

        if (empty($departmentName))
            return response("大科別不能為空", 400);
        if (count($this->adminmodel->getDepartmentName($departmentName)) == 1)
            return response("大科別重複", 202);

        $res = $this->adminmodel->addDepartment($departmentName);
        if ($res == 0)
            return response("新增失敗", 400);

        return response("新增成功", 201);
    }

    public function addDivision(Request $request)
    {
        $divisionName = $request->input('divisionName');
        $departmentName = $request->input('departmentName');
        
        if (empty($divisionName))
            return response("小科別不能為空", 400);
        if (empty($departmentName))
            return response("大科別不能為空", 400);

        $res = $this->adminmodel->getDepartmentName($departmentName);
        if (count($res) == 0)
            return response("無此大科別", 400);

        
        if (count($this->adminmodel->getDivisionName($divisionName)) == 1)
            return response("小科別重複", 202);

        $departmentID = $res[0]->departmentID;
        $res = $this->adminmodel->addDivision($divisionName, $departmentID);
        if ($res != 1)
            return response("新增失敗", 400);

        return response("新增成功", 201);
    }




}