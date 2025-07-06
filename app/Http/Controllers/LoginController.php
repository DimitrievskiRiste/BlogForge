<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request) {
        try {
            $data = $request->validate([
                "email" => "required|email",
                "password" => "required"
            ]);
            if(!empty($data['email']) && !empty($data['password'])) {

            }
        } catch (ValidationException $exc) {
            return Response()->json(['errors' => $exc->errors()],400);
        }
    }
}
