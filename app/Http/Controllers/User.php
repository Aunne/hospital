<?php
namespace App\Http\Controllers;

use App\Models\User as UserModel;
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
        $this->usermodel = new UserModel();
        $this->shiftmodel = new ShiftModel();
        $this->appointmentmodel = new AppointmentModel();
    }

    public function getUser(Request $request)
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


    public function newUser(Request $request)
    {
        $acccount = $request->input("acccount");
        $password = $request->input("password");


        if ($this->usermodel->newUser($acccount, $password) == 1) {
            $response['status'] = 200;
            $response['message'] = '新增成功';
        } else {
            $response['status'] = 204;
            $response['message'] = '新增失敗';
        }

        return response(json_encode($response), $response['status']);

    }

    public function updateUser(Request $request)
    {
        $id = $request->input("id");
        $acccount = $request->input("acccount");
        $password = $request->input("password");

        if ($this->getUser($id) == 0)
            return response('無此帳號', 404);

        $response['result'] = $this->usermodel->updateUser($id, $acccount, $password);

        if ($response['result'] == 1) {
            $response['status'] = 200;
            $response['message'] = '更新成功';
        } else {
            $response['status'] = 202;
            $response['message'] = '更新失敗';
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

        $appointment = $this->appointmentmodel->getAppointment($shiftID);
        if (count($appointment) >= 50)
            return response("額滿不可掛號", 202);

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

}