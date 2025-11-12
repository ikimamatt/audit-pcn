<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterData\MasterAksesUser;

class MasterAksesUserController extends Controller
{
    public function index()
    {
        $data = MasterAksesUser::all();
        return view('master-data.akses-user.index', compact('data'));
    }
} 