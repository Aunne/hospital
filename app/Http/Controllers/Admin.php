<?php
namespace App\Http\Controllers;

use App\Models\Admin as AdminModel;
use App\Models\Doctor as DoctorModel;
use App\Models\User as UserModel;
use App\Models\shift as ShiftModel;
use App\Models\Department as DepartmentModel;
use App\Models\Appointment as AppointmentModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Admin extends Controller
{
    protected $adminmodel;
    protected $doctormodel;
    protected $usermodel;
    protected $shiftmodel;
    protected $departmentmodel;
    protected $appointmentmodel;
    public function __construct()
    {
        $this->adminmodel = new AdminModel();
        $this->doctormodel = new DoctorModel();
        $this->usermodel = new UserModel();
        $this->shiftmodel = new ShiftModel();
        $this->departmentmodel = new DepartmentModel();
        $this->appointmentmodel = new AppointmentModel();
    }

    public function adminUpdateDepartment(Request $request)
    {
        $res = $this->empty_check(['departmentID', 'departmentName'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $departmentID = $request->input('departmentID');
        $departmentName = $request->input('departmentName');

        $department = $this->departmentmodel->getDepartmentID($departmentID);
        if (count($department) == 0)
            return response("無此大科別", 404);

        if ($department[0]->departmentName == $departmentName)
            return response("大科別未更改", 202);

        if (count($this->departmentmodel->getDepartmentName($departmentName)) == 1)
            return response("大科別重複", 202);

        $res = $this->departmentmodel->updateDepartment($departmentID, $departmentName);

        if ($res == 0)
            return response("更新失敗", 400);

        return response("更新成功", 200);
    }

    public function adminUpdateDivision(Request $request)
    {
        $res = $this->empty_check(['divisionID'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $divisionID = $request->input('divisionID');
        $divisionName = $request->input('divisionName');
        $departmentName = $request->input('departmentName');

        $division = $this->adminmodel->getDivisionID($divisionID);
        if (count($division) == 0)
            return response("無此小科別", 404);

        $department = $this->departmentmodel->getDepartmentName($departmentName);
        if (count($department) == 0)
            return response("無此大科別", 404);

        if (empty($divisionName) && empty($departmentName))
            return response("小科別資料未更改", 202);

        if ($division[0]->divisionName == $divisionName && $department[0]->departmentID == $division[0]->departmentID)
            return response("小科別資料未更改", 202);

        if (empty($divisionName))
            $divisionName = $division[0]->divisionName;

        $departmentID = $department[0]->departmentID;
        if (empty($departmentName))
            $departmentID = $division[0]->departmentID;

        if ($division[0]->divisionID != $divisionID)
            return response("小科別名稱重複", 202);

        $res = $this->adminmodel->updateDivision($divisionID, $divisionName, $departmentID);
        if ($res == 0)
            return response("更新失敗", 400);

        return response("更新成功", 200);
    }

    public function adminUpdateDoctor(Request $request)
    {
        $res = $this->empty_check(['doctorID'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $doctorID = $request->input('doctorID');
        $doctorName = $request->input('doctorName');
        $doctorIDNumber = $request->input('doctorIDNumber');

        $doctor = $this->doctormodel->getDoctorID($doctorID);
        if (count($doctor) == 0)
            return response("無此醫生", 404);

        if (empty($doctorName) && empty($doctorIDNumber))
            return response("醫生資料未更改", 202);

        if (empty($doctorName))
            $doctorName = $doctor[0]->doctorName;

        if (empty($doctorIDNumber))
            $doctorIDNumber = $doctor[0]->doctorIDNumber;

        if (!$this->id_card($doctorIDNumber))
            return response("身分證字號格式錯誤", 400);

        if ($doctor[0]->doctorName == $doctorName && $doctor[0]->doctorIDNumber == $doctorIDNumber)
            return response("醫生資料未更改", 202);

        $doctor = $this->doctormodel->getDoctorIDNumber($doctorIDNumber);
        if ($doctor[0]->doctorID != $doctorID)
            return response("身分證字號重複", 202);

        $res = $this->doctormodel->updateDoctor($doctorID, $doctorName, $doctorIDNumber);
        if ($res == 0)
            return response("更新失敗", 400);

        return response("更新成功", 200);
    }

    public function adminUpdateShift(Request $request)
    {
        // shiftID, doctorIDNumber, divisionName, date, timePeriod   
        $res = $this->empty_check(['shiftID'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $shiftID = $request->input('shiftID');
        $doctorIDNumber = $request->input('doctorIDNumber');
        $divisionName = $request->input('divisionName');
        $date = $request->input('date');
        $timePeriod = $request->input('timePeriod');

        $shift = $this->shiftmodel->getShiftID($shiftID);
        if (count($shift) == 0)
            return response("無此班表", 404);

        if (empty($doctorIDNumber) && empty($divisionName) && empty($date) && empty($timePeriod))
            return response("班表資料未更改", 202);

        if (empty($doctorIDNumber))
            $doctorIDNumber = $shift[0]->doctorIDNumber;

        if (empty($divisionName))
            $divisionName = $shift[0]->divisionName;

        if (empty($date))
            $date = $shift[0]->date;

        if (empty($timePeriod))
            $timePeriod = $shift[0]->timePeriod;

        if (!$this->id_card($doctorIDNumber))
            return response("身分證字號格式錯誤", 400);

        $doctor = $this->doctormodel->getDoctorIDNumber($doctorIDNumber);
        if (count($doctor) == 0)
            return response("無此醫生", 404);

        $division = $this->adminmodel->getDivisionName($divisionName);
        if (count($division) == 0)
            return response("無此小科別", 404);

        if ($this->validateDate($date) == false)
            return response("日期格式錯誤", 400);

        if (!$this->validteTimePeriod($timePeriod))
            return response("時段格式錯誤", 400);

        if ($shift[0]->doctorIDNumber == $doctorIDNumber && $shift[0]->divisionName == $divisionName && $shift[0]->date == $date && $shift[0]->timePeriod == $timePeriod)
            return response("班表資料未更改", 202);

        $doctorID = $doctor[0]->doctorID;
        $divisionID = $division[0]->divisionID;
        $res = $this->shiftmodel->updateShift($shiftID, $doctorID, $divisionID, $date, $timePeriod);
        if ($res == 0)
            return response("更新失敗", 400);
        
        return response("更新成功", 200);
    }

    public function adminDeleteDepartment(Request $request) 
    {
        $res = $this->empty_check(['departmentName'], $request);
        if ($res['status'])
            return response($res['message'], 400);
        
        $departmentName = $request->input('departmentName');
        $department = $this->adminmodel->getDepartmentName($departmentName);
        if (count($department) == 0)
            return response("無此科別", 404);
        
        $departmentID = $department[0]->departmentID;

        $division = $this->adminmodel->getDivisionDepartmentID($departmentID);
        if (count($division) != 0)
            return response("此科別仍有小科別", 400);
        
        $res = $this->departmentmodel->deleteDepartment($departmentID);
        if ($res == 0)
            return response("刪除失敗", 400);
        
        return response("刪除成功", 200);
    }

    public function adminDeleteDivision( Request $request ) 
    {
        $res = $this->empty_check(['divisionName'], $request);
        if ($res['status'])
            return response($res['message'], 400);
        
        $divisionName = $request->input('divisionName');
        $division = $this->adminmodel->getDivisionName($divisionName);
        if (count($division) == 0)
            return response("無此小科別", 404);
        
        $divisionID = $division[0]->divisionID;

        $shift = $this->shiftmodel->getShiftDivisionID($divisionID);
        if (count($shift) != 0)
            return response("此小科別仍有班表", 400);
        
        $appointment = $this->appointmentmodel->getAppointmentDivisionID($divisionID);
        if (count($appointment) != 0)
            return response("此小科別仍有預約", 400);
        
        $res = $this->shiftmodel->deleteDivision($divisionID);
        if ($res == 0)
            return response("刪除失敗", 400);
        
        return response("刪除成功", 200);
    }

    public function adminDeleteDoctor(Request $request) 
    {
        $res = $this->empty_check(['doctorIDNumber'], $request);
        if ($res['status'])
            return response($res['message'], 400);
        
        $doctorIDNumber = $request->input('doctorIDNumber');
        $doctor = $this->doctormodel->getDoctorIDNumber($doctorIDNumber);
        if (count($doctor) == 0)
            return response("無此醫生", 404);
        
        $doctorID = $doctor[0]->doctorID;

        $shift = $this->doctormodel->getShiftDoctorID($doctorID);
        if (count($shift) != 0)
            return response("此醫生仍有班表", 400);
        
        $res = $this->doctormodel->deleteDoctor($doctorID);
        if ($res == 0)
            return response("刪除失敗", 400);
        
        return response("刪除成功", 200);    
    }

    public function adminDeleteShift(Request $request) 
    {
        $res = $this->empty_check(['shiftID'], $request);
        if ($res['status'])
            return response($res['message'], 400);
        
        $shiftID = $request->input('shiftID');
        $shift = $this->shiftmodel->getShiftID($shiftID);
        if (count($shift) == 0)
            return response("無此班表", 404);
        
        $appointment = $this->appointmentmodel->getAppointmentShiftID($shiftID);
        if (count($appointment) != 0)
            return response("此班表仍有預約", 400);
        
        $res = $this->shiftmodel->deleteShift($shiftID);
        if ($res == 0)
            return response("刪除失敗", 400);
        
        return response("刪除成功", 200);    
    }

    public function adminLogin(Request $request)
    {
        $res = $this->empty_check(['account', 'password'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $account = $request->input('account');
        $password = $request->input('password');

        if ($this->verify($account, 'admin'))
            return response("帳號格式錯誤", 400);

        $res = $this->usermodel->getUserAccount($account);

        if (count($res) == 0)
            return response('無此帳號', 404);
        if ($password == $res[0]->password) {
            $id = $res[0]->id;
            $token = $this->genToken($id, $account);
            return response($token, 200);
        } else {
            return response('密碼錯誤', 400);
        }
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

    public function adminGetAllDivision(Request $request)
    {
        $res = $this->empty_check(['departmentName'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $departmentName = $request->input('departmentName');

        $department = $this->adminmodel->getDepartmentName($departmentName);
        if (count($department) == 0)
            return response("無此大科別", 400);

        $departmentID = $department[0]->departmentID;
        $res = $this->adminmodel->adminGetAllDivision($departmentID);
        if (count($res) == 0)
            return response("無資料", 404);

        return response(json_encode($res), 200);
    }

    public function adminGetAllShift(Request $request)
    {
        $res = $this->empty_check(['divisionName'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $divisionName = $request->input('divisionName');

        $division = $this->adminmodel->getDivisionName($divisionName);
        if (count($division) == 0)
            return response("無此小科別", 400);

        $divisionID = $division[0]->divisionID;
        $res = $this->shiftmodel->adminGetShiftDivisionID($divisionID);
        if (count($res) == 0)
            return response("無資料", 404);

        return response(json_encode($res), 200);
    }

    public function adminGetAllDepartment()
    {
        $res = $this->adminmodel->adminGetAllDepartment();
        if (count($res) == 0)
            return response("無資料", 404);

        return response(json_encode($res), 200);
    }

    public function adminGetAllDoctor()
    {
        $res = $this->doctormodel->adminGetAllDoctor();
        if (count($res) == 0)
            return response("無資料", 404);

        return response(json_encode($res), 200);
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

    public function validteTimePeriod($timePeriod)
    {
        return in_array($timePeriod, ['上午', '下午', '晚上']);
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

    public function verify($account, $key)
    {
        for ($i = 0; $i < strlen($key); $i++) {
            if ($key[$i] != $account[$i])
                return true;
        }
        return false;
    }

    private function genToken($id, $account)
    {
        $secret_key = "YOUR_SECRET_KEY";
        $issuer_claim = "http://rainbowHospital.org.tw";
        $audience_claim = "http://rainbowHospital.org.tw";
        $issuedat_claim = time(); // issued at
        $expire_claim = $issuedat_claim + 60000;
        $payload = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $id,
                "account" => $account
            )
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return $jwt;
    }

    public function id_card($cardid)
    // 格式正確會回傳 true
    {
        $err = '';
        //先將字母數字存成陣列
        $alphabet = [
            'A' => '10',
            'B' => '11',
            'C' => '12',
            'D' => '13',
            'E' => '14',
            'F' => '15',
            'G' => '16',
            'H' => '17',
            'I' => '34',
            'J' => '18',
            'K' => '19',
            'L' => '20',
            'M' => '21',
            'N' => '22',
            'O' => '35',
            'P' => '23',
            'Q' => '24',
            'R' => '25',
            'S' => '26',
            'T' => '27',
            'U' => '28',
            'V' => '29',
            'W' => '32',
            'X' => '30',
            'Y' => '31',
            'Z' => '33'
        ];
        //檢查字元長度
        if (strlen($cardid) != 10) { //長度不對
            $err = '1';
            return false;
        }

        //驗證英文字母正確性
        $alpha = substr($cardid, 0, 1); //英文字母
        $alpha = strtoupper($alpha); //若輸入英文字母為小寫則轉大寫
        if (!preg_match("/[A-Za-z]/", $alpha)) {
            $err = '2';
            return false;
        } else {
            //計算字母總和
            $nx = $alphabet[$alpha];
            $ns = $nx[0] + $nx[1] * 9; //十位數+個位數x9
        }

        //驗證男女性別
        $gender = substr($cardid, 1, 1); //取性別位置
        //驗證性別
        if ($gender != '1' && $gender != '2') {
            $err = '3';
            return false;
        }

        //N2x8+N3x7+N4x6+N5x5+N6x4+N7x3+N8x2+N9+N10
        if ($err == '') {
            $i = 8;
            $j = 1;
            $ms = 0;
            //先算 N2x8 + N3x7 + N4x6 + N5x5 + N6x4 + N7x3 + N8x2
            while ($i >= 2) {
                $mx = substr($cardid, $j, 1); //由第j筆每次取一個數字
                $my = $mx * $i; //N*$i
                $ms = $ms + $my; //ms為加總
                $j += 1;
                $i--;
            }
            //最後再加上 N9 及 N10
            $ms = $ms + substr($cardid, 8, 1) + substr($cardid, 9, 1);
            //最後驗證除10
            $total = $ns + $ms; //上方的英文數字總和 + N2~N10總和
            if (($total % 10) != 0) {
                $err = '4';
                return false;
            }
        }
        //錯誤訊息返回
        // switch($err){
        //     case '1':$msg = '字元數錯誤';break;
        //     case '2':$msg = '英文字母錯誤';break;
        //     case '3':$msg = '性別錯誤';break;
        //     case '4':$msg = '驗證失敗';break;
        //     default:$msg = '驗證通過';break;
        // }
        // \App\Library\CommonTools::writeErrorLogByMessage('身份字號：'.$cardid);
        // \App\Library\CommonTools::writeErrorLogByMessage($msg);
        return true;
    }
}