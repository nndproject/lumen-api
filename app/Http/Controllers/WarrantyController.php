<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function checkWarranty($id)
    {
       
        return response()->json(['data' => $id]);
    }
}
