<?php
namespace App\Http\Controllers;

use App\Models\Staff as StaffModel;
use App\Models\Appointment as AppointmentModel;
use App\Models\shift as ShiftModel;
use App\Models\User as UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Staff extends Controller
{
    protected $staffmodel;
    protected $appointmentmodel;
    protected $shiftmodel;
    protected $usermodel;

    public function __construct()
    {
        $this->staffmodel = new StaffModel();
        $this->appointmentmodel = new AppointmentModel();
        $this->shiftmodel = new ShiftModel();
        $this->usermodel = new UserModel();
    }

    public function staffAddAppointment(Request $request)
    {
        $res = $this->empty_check(['shiftID', 'IdNumber'], $request);
        if ($res['status'])
            return response($res['message'], 400);
        
        $IdNumber = $request->input('IdNumber');
        $shiftID = $request->input('shiftID');
        $today = date('Y-m-d');

        if(!$this->id_card($IdNumber))
            return response("錯誤的身分證字號",400);
        
        $user = $this->usermodel->getUserAccount($IdNumber);
        if(count($user)==0)
            return response("病患不存在",404);
        
        $shift = $this->shiftmodel->getShift($shiftID);
        if (count($shift) == 0)
            return response("無此班表", 400);

        if ($today >= $shift[0]->date)
            return response('掛號日期錯誤，', 400);

        $appointment = $this->appointmentmodel->getAppointment($shiftID);
        if (count($appointment) >= 50)
            return response("額滿不可掛號", 202);

        $userID = $user[0]->id;
        if ($this->check_appointment($appointment, $userID))
            return response("重複的掛號", 400);

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
}