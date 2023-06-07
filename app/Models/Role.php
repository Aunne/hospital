<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;

class Role
{
    public function showUserRoles($userId)
    {
        $sql = "
        select role.name 
        from user_role, role 
        where user_id=:userId and role.id=user_role.role_id";
        $response = DB::select($sql, [":userId" => $userId]);
        return $response;
    }

    public function showActionRoles($actionName)
    {
        $sql = "
        SELECT role.name 
        FROM action,role_action, role 
        WHERE action.id = role_action.action_id
        AND role_action.role_id = role.id
        AND action.actionName = :actionName";
        $response = DB::select($sql, ['actionName' => $actionName]);
        return $response;
    }
}