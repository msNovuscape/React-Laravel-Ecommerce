<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AmbassadorController extends Controller
{
    public function index(){
        $ambassadors = User::ambassador()->get();
        return $ambassadors;
    }
}
