<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class User
{
    public function showUser($id)
    {
        $sql = "select * from user where id = :id ";
        $response = DB::select($sql, ['id' => $id]);
        return $response;
    }
    public function getUserAccount($account)
    {
        $sql = "select * from user where account = :account ";
        $response = DB::select($sql, ['account' => $account]);
        return $response;
    }
    public function newUser($account, $password)
    {
        $sql = "insert into user ( account, password) values ( :account, :password)";
        $response = DB::insert($sql, ['account' => $account, 'password' => $password]);
        return $response;
    }

    public function newUserInfo($userID, $name, $phoneNumber)
    {
        $sql = "insert into userInfo ( userID, name, phoneNumber) values ( :userID, :name, :phoneNumber)";
        $response = DB::insert($sql, ['userID' => $userID, 'name' => $name, 'phoneNumber' => $phoneNumber]);
        return $response;
    }

    public function newUserRole($userID)
    {
        $sql = "insert into user_role ( user_id, role_id) values ( :userID, :role_id)";
        $response = DB::insert($sql, ['userID' => $userID, 'role_id' => 1]);
        return $response;
    }

    public function updateUser($id, $account, $password)
    {
        $sql = "update user set account=:account, password=:password where id=:id";
        $response = DB::update($sql, ['id' => $id, 'account' => $account, 'password' => $password]);

        return $response;
    }

    public function removeUser($id)
    {
        $sql = "delete from user where id=:id";
        $response = DB::delete($sql, ['id' => $id]);
        return $response;
    }

    public function showUserRoles($userId)
    {
        $sql = "select * from user_role where user_id = :userId ";
        $response = DB::select($sql, ['user_id'=> $userId]);
        return $response;
    }
}