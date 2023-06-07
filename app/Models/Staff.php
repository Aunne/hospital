<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;


class Staff
{
    public function showUser($id)
    {
        $sql = "select * from user where id = :id ";
        $response = DB::select($sql, ['id' => $id]);
        return $response;
    }
}