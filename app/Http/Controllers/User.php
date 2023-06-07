<?php
namespace App\Http\Controllers;

use App\Models\User as UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class User extends Controller
{
    protected $usermodel;
    public function __construct()
    {
        $this->usermodel = new UserModel();
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
}