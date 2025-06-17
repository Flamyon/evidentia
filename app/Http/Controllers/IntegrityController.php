<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use App\Models\Proof;
use App\Models\User;
use Illuminate\Http\Request;

class IntegrityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkroles:LECTURE');
    }

    public function integrity()
    {
        
        $evidences = Evidence::all();
        $proofs = Proof::all();

        return view('integrity.list',
            ['evidences' => $evidences, 'proofs' => $proofs]);
    }
}
