<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Exception;
use App\Models\User as UserModel;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;
    protected $userModel;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->userModel = new UserModel();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        switch ($request->path()) {
            case 'userLogin':
                $res = $this->empty_check(['account', 'password'], $request);
                if ($res['status'])
                    return response($res['message'], 400);
                
                $account = $request->input('account');
                $password = $request->input('password');

                if (!$this->id_card($account))
                    return response("證件格式錯誤", 400);

                $res = $this->userModel->getUserAccount($account);

                if (count($res) == 0)
                    return response('無此帳號', 404);
                if ($password == $res[0]->password) {
                    $id = $res[0]->id;
                    $token = $this->genToken($id, $account);
                    return response($token, 200);
                } else {
                    return response('密碼錯誤', 400);
                }

            case 'staffLogin':
                $res = $this->empty_check(['account', 'password'], $request);
                if ($res['status'])
                    return response($res['message'], 400);

                $account = $request->input('account');
                $password = $request->input('password');

                if ($this->verify($account, 'staff'))
                    return response("帳號格式錯誤", 400);

                $res = $this->userModel->getUserAccount($account);

                if (count($res) == 0)
                    return response('無此帳號', 404);
                if ($password == $res[0]->password) {
                    $id = $res[0]->id;
                    $token = $this->genToken($id, $account);
                    return response($token, 200);
                } else {
                    return response('密碼錯誤', 400);
                }

            case 'adminLogin':
                $res = $this->empty_check(['account', 'password'], $request);
                if ($res['status'])
                    return response($res['message'], 400);

                $account = $request->input('account');
                $password = $request->input('password');

                if ($this->verify($account, 'admin'))
                    return response("帳號格式錯誤", 400);

                $res = $this->userModel->getUserAccount($account);

                if (count($res) == 0)
                    return response('無此帳號', 404);
                if ($password == $res[0]->password) {
                    $id = $res[0]->id;
                    $token = $this->genToken($id, $account);
                    return response($token, 200);
                } else {
                    return response('密碼錯誤', 400);
                }

            case 'newUser':
                $res = $this->empty_check(['account', 'password'], $request);
                if ($res['status'])
                    return response($res['message'], 400);
                    
                $account = $request->input("account");
                $password = $request->input("password");
                $name = $request->input("name");
                $phoneNumber = $request->input("phoneNumber");

                if (!$this->id_card($account))
                    return response("證件格式錯誤", 400);

                if (count($this->userModel->getUseraccount($account)) == 1)
                    return response('帳號重複', 202);

                $res = $this->userModel->newUser($account, $password);
                if ($res == 0)
                    return response("帳號新增失敗", 400);
                if (empty($name))
                    $name = NAN;
                if (empty($phoneNumber))
                    $phoneNumber = NAN;

                $user = $this->userModel->getUserAccount($account);
                if ($user == 0)
                    return response("資料庫查詢錯誤", 400);
                if ($this->userModel->newUserInfo($user[0]->id, $name, $phoneNumber) == 0)
                    return response("使用者資訊新增失敗", 400);
                if ($this->userModel->newUserRole($user[0]->id) == 0)
                    return response("使用者Role新增失敗", 400);

                return response('新增成功', 200);

            default:
                if ($this->checkToken($request)) {
                    return $next($request);
                } else {
                    return response('無效Token', 401);
                }
        }
    }
    public function checkToken($request)
    {
        $jwtToken = $request->header('jwtToken');
        $secret_key = "YOUR_SECRET_KEY";
        try {
            $payload = JWT::decode($jwtToken, new Key($secret_key, 'HS256'));
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            echo '<br>';
            return false;
        }
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

    public function verify($account, $key)
    {
        for ($i = 0; $i < strlen($key); $i++) {
            if ($key[$i] != $account[$i])
                return true;
        }
        return false;
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