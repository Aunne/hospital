<?php
namespace App\Http\Controllers;

use App\Models\Admin as AdminModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class Admin extends Controller
{
    protected $adminmodel;
    public function __construct()
    {
        $this->adminmodel = new AdminModel();
    }


}