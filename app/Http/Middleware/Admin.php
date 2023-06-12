<?php

namespace App\Http\Middleware;

use Closure;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use App\Models\Role as RoleModel;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $roleModel;
    protected $actionModel;


    public function __construct()
    {
        $this->roleModel = new roleModel();

    }
    public function handle($request, Closure $next)
    {
        $jwt = (array) JWT::decode(
            $request->header('jwtToken'),
            new Key('YOUR_SECRET_KEY', 'HS256')
        );
        $userId = $jwt['data']->id;
        $userRoles = $this->convert_array(
            $this->roleModel->showUserRoles($userId),
            "name"
        );
        $actionRoles = $this->convert_array(
            $this->roleModel->showActionRoles($request->path()),
            "name"
        );
        $r = array_intersect($userRoles, $actionRoles);

        if (count($r) != 0)
            return $next($request);
        else
            return response('權限不足', 403);
    }

    public function convert_array($obj, $key)
    {
        $arr = array();
        for ($i = 0; $i < count($obj); $i++) {
            $arr[$i] = $obj[$i]->$key;
        }

        return $arr;
    }
}