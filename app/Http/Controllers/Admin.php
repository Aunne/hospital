<?php
namespace App\Http\Controllers;

use App\Models\Admin as AdminModel;
use App\Models\Doctor as DoctorModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Admin extends Controller
{
    protected $adminmodel;
    protected $doctormodel;
    public function __construct()
    {
        $this->adminmodel = new AdminModel();
        $this->doctormodel = new DoctorModel();
    }


    public function addDepartment(Request $request)
    {
        $res = $this->empty_check(['departmentName'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $departmentName = $request->input('departmentName');

        if (count($this->adminmodel->getDepartmentName($departmentName)) == 1)
            return response("大科別重複", 202);

        $res = $this->adminmodel->addDepartment($departmentName);
        if ($res == 0)
            return response("新增失敗", 400);

        return response("新增成功", 201);
    }

    public function addDivision(Request $request)
    {
        $res = $this->empty_check(['departmentName', 'divisionName'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $divisionName = $request->input('divisionName');
        $departmentName = $request->input('departmentName');

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

    public function addDoctor(Request $request)
    {
        $res = $this->empty_check(['doctorName', 'doctorIDNumber'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $doctorName = $request->input('doctorName');
        $doctorIDNumber = $request->input('doctorIDNumber');

        $doctor = $this->doctormodel->getDoctorIDNumber($doctorIDNumber);
        if (count($doctor) != 0)
            return response("醫師重複", 202);

        $res = $this->doctormodel->newDoctor($doctorName, $doctorIDNumber);
        if ($res != 1)
            return response("新增失敗", 202);

        return response("新增成功", 201);
    }

    public function addShift(Request $request)
    {
        $res = $this->empty_check(['divisionName', 'doctorIDNumber', 'date', 'timePeriod'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $doctorIDNumber = $request->input('doctorIDNumber');
        $divisionName = $request->input('divisionName');
        $date = $request->input('date');
        $timePeriod = $request->input('timePeriod');

        $doctor = $this->doctormodel->getDoctorIDNumber($doctorIDNumber);
        if (count($doctor) == 0)
            return response("無此醫師");
        $division = $this->adminmodel->getDivisionName($divisionName);
        if (count($division) == 0)
            return response("無此小科別", 400);

        if (!$this->validateDate($date))
            return response('日期格式錯誤', 400);
        $today = date("Y-m-d");
        if ($today >= $date)
            return response("錯誤的新增日期", 400);
        if (!in_array($timePeriod, ['上午', '下午', '晚上']))
            return response('錯誤的時段', 400);

        if (count($this->adminmodel->getShiftAllParameter($doctor[0]->doctorID, $division[0]->divisionID, $date, $timePeriod)) != 0)
            return response('重複的班表', 400);

        $res = $this->adminmodel->addShift(
            $doctor[0]->doctorID,
            $division[0]->divisionID,
            $date,
            $timePeriod
        );
        if ($res != 1)
            return response("新增失敗", 202);

        return response("新增成功", 201);
    }

    public function validateDate(string $date, string $format = 'Y-m-d')
    {
        return date($format, strtotime($date)) == $date;
    }

    public function empty_check($key, $request)
    {
        for ($i = 0; $i < count($key); $i++) {
            if (empty($request->input($key[$i]))) {
                $res['message'] = $key[$i] . " 不能為空";
                $res['status'] = true;
                return $res;
            }
        }

        $res['message'] = "OK";
        $res['status'] = false;
        return $res;
    }
}