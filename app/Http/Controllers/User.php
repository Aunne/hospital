<?php
namespace App\Http\Controllers;

use App\Models\User as usermodel;
use App\Models\shift as ShiftModel;
use App\Models\Appointment as AppointmentModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class User extends Controller
{
    protected $usermodel;
    protected $shiftmodel;
    protected $appointmentmodel;
    public function __construct()
    {
        $this->usermodel = new usermodel();
        $this->shiftmodel = new ShiftModel();
        $this->appointmentmodel = new AppointmentModel();
    }

    public function userGetUser(Request $request)
    {
        $jwt = (array) JWT::decode($request->header('jwtToken'), new Key('YOUR_SECRET_KEY', 'HS256'));
        $id = $jwt['data']->id;

        $response['result'] = $this->usermodel->showUser($id);
        if (count($response['result']) != 0) {
            $response['status'] = 200;
            $response['message'] = '查詢成功';
        } else {
            $response['status'] = 204;
            $response['message'] = '無查詢結果';
        }
        return response(json_encode($response), $response['status']);
    }

    public function getShift(Request $request)
    {
        $res = $this->empty_check(['divisionID'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $divisionID = $request->input("divisionID");
        $response['result'] = $this->shiftmodel->getShift($divisionID);
        if (count($response['result']) != 0) {
            $response['status'] = 200;
            $response['message'] = '查詢成功';
        } else {
            $response['status'] = 204;
            $response['message'] = '無查詢結果';
        }

        return response(json_encode($response), $response['status']);
    }

    public function getAllDivision()
    {
        $response['result'] = $this->usermodel->getAllDivision();
        if (count($response['result']) != 0) {
            $response['status'] = 200;
            $response['message'] = '查詢成功';
        } else {
            $response['status'] = 204;
            $response['message'] = '無查詢結果';
        }
        return response(json_encode($response), $response['status']);

    }

    public function newUser(Request $request)
    {
        $res = $this->empty_check(['account', 'password'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $account = $request->input("account");
        $password = $request->input("password");
        $name = $request->input("name");
        $phoneNumber = $request->input("phoneNumber");

        if (!$this->id_card($account))
            return response("證件格式錯誤", 400);

        if (count($this->usermodel->getUseraccount($account)) == 1)
            return response('帳號重複', 202);

        $res = $this->usermodel->newUser($account, $password);
        if ($res == 0)
            return response("帳號新增失敗", 400);
        if (empty($name))
            $name = NAN;
        if (empty($phoneNumber))
            $phoneNumber = NAN;

        $user = $this->usermodel->getUserAccount($account);
        if ($user == 0)
            return response("資料庫查詢錯誤", 400);
        if ($this->usermodel->newUserInfo($user[0]->id, $name, $phoneNumber) == 0)
            return response("使用者資訊新增失敗", 400);
        if ($this->usermodel->newUserRole($user[0]->id) == 0)
            return response("使用者Role新增失敗", 400);

        return response('新增成功', 200);
    }

    public function userUpdateUser(Request $request)
    {
        $jwt = (array) JWT::decode($request->header('jwtToken'), new Key('YOUR_SECRET_KEY', 'HS256'));
        $id = $jwt['data']->id;
        $name = $request->input("name");
        $phoneNumber = $request->input("phoneNumber");

        $user = $this->usermodel->showUser($id);
        if ($user == 0)
            return response('無此帳號', 404);

        if (empty($name) && empty($phoneNumber))
            return response('無更新內容', 400);
        if ($user[0]->name == $name && $user[0]->phoneNumber == $phoneNumber)
            return response('無更新內容', 400);

        if (empty($name))
            $name = $user[0]->name;
        if (empty($phoneNumber))
            $phoneNumber = $user[0]->phoneNumber;

        $response['result'] = $this->usermodel->userUpdateUser($id, $name, $phoneNumber);

        if ($response['result'] == 1) {
            $response['status'] = 200;
            $response['message'] = '更新成功';
        } else {
            $response['status'] = 202;
            $response['message'] = '更新失敗';
        }
        return response(json_encode($response), $response['status']);
    }

    public function userLogin(Request $request)
    {
        $res = $this->empty_check(['account', 'password'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $account = $request->input('account');
        $password = $request->input('password');

        if (!$this->id_card($account))
            return response("證件格式錯誤", 400);

        $res = $this->usermodel->getUserAccount($account);

        if (count($res) == 0)
            return response('無此帳號', 404);
        if ($password == $res[0]->password) {
            $id = $res[0]->id;
            $token = $this->genToken($id, $account);
            return response(json_encode($token), 200);
        } else {
            return response('密碼錯誤', 400);
        }
    }

    public function userCancelAppointment(Request $request)
    {
        $res = $this->empty_check(['shiftID'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $shiftID = $request->input('shiftID');
        $jwt = (array) JWT::decode($request->header('jwtToken'), new Key('YOUR_SECRET_KEY', 'HS256'));
        $userID = $jwt['data']->id;
        $today = date('Y-m-d');
        $appointmnet = $this->appointmentmodel->getValidAppointmentUserIDShiftID($userID, $shiftID);

        if (count($appointmnet) == 0)
            return response('查無此掛號', 404);

        if ($appointmnet[0]->date <= $today)
            return response("不能取消當日或過往的掛號", 403);

        if ($this->appointmentmodel->cancelAppointment($userID, $shiftID) == 0)
            return response("取消失敗", 400);

        return response("取消成功", 200);
    }

    public function userGetValidAppointmentUserID(Request $request)
    {
        $jwt = (array) JWT::decode($request->header('jwtToken'), new Key('YOUR_SECRET_KEY', 'HS256'));
        $userID = $jwt['data']->id;

        $appointment = $this->appointmentmodel->getValidAppointmentUserID($userID);
        if ($appointment == 0)
            return response("查詢失敗", 400);

        $response['result'] = $appointment;
        if (count($response['result']) != 0) {
            $response['status'] = 200;
            $response['message'] = '查詢成功';
        } else {
            $response['status'] = 204;
            $response['message'] = '無查詢結果';
        }
        return response(json_encode($response), $response['status']);
    }

    public function userAddAppointment(Request $request)
    {
        $res = $this->empty_check(['shiftID'], $request);
        if ($res['status'])
            return response($res['message'], 400);

        $jwt = (array) JWT::decode($request->header('jwtToken'), new Key('YOUR_SECRET_KEY', 'HS256'));

        $userID = $jwt['data']->id;
        $shiftID = $request->input('shiftID');
        $today = date('Y-m-d');

        $shift = $this->shiftmodel->getShift($shiftID);
        if (count($shift) == 0)
            return response("無此班表", 400);

        if ($today >= $shift[0]->date)
            return response('掛號日期錯誤，', 400);

        $appointment = $this->appointmentmodel->getAppointmentShiftID($shiftID);
        if (count($appointment) >= 50)
            return response("額滿不可掛號", 202);

        if ($this->check_appointment($appointment, $userID))
            return response("重複的掛號", 400);

        $divisionID = $appointment[0]->divisionID;
        if (count($this->appointmentmodel->getUserAppointDivision($userID, $divisionID, $shift[0]->date)) > 1)
            return response("科別重複掛號", 400);

        $appointmentNumberArr = $this->convert_array($appointment, 'appointmentNumber');
        if (count($appointmentNumberArr) == 0)
            $appointmentNumber = 1;
        else
            $appointmentNumber = max($appointmentNumberArr) + 1;

        $res = $this->appointmentmodel->addAppointment($userID, $shiftID, $appointmentNumber);
        if ($res == 0)
            return response("新增失敗", 400);

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

    public function convert_array($obj, $key)
    {
        $arr = array();
        for ($i = 0; $i < count($obj); $i++) {
            $arr[$i] = $obj[$i]->$key;
        }

        return $arr;
    }

    public function check_appointment($appointment, $userID)
    {
        for ($i = 0; $i < count($appointment); $i++) {
            if ($appointment[$i]->isCancel == 'false' && $appointment[$i]->userID == $userID) {
                return true;
            }
        }

        return false;
    }

    public function id_card($cardid)
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

}