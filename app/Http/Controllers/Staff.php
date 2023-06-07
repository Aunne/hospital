<?php
namespace App\Http\Controllers;

use App\Models\Staff as StaffModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Staff extends Controller
{
    protected $staffmodel;
    public function __construct()
    {
        $this->staffmodel = new StaffModel();
    }


}