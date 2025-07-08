<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
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
                $user = $this->findUser($data);
                if(!$user){
                    return Response()->json(['errors' => [
                        'email' => 'Account not exists'
                    ]], 400);
                }
                $secretKey = $this->generateSecretKey();
                $exp = time()+3600;
                $payload = [
                    'iss' => url("/api/"),
                    'exp' => $exp,
                    'user' => $user->id
                ];
                $jwtToken = JWT::encode($payload,$secretKey, 'HS256');
                return Response()->json(['token' => $jwtToken, 'exp' => $exp, 'secret' => $secretKey], 200);
            }
        } catch (ValidationException $exc) {
            return Response()->json(['errors' => $exc->errors()],400);
        }
    }
    protected function findUser(array $data) :User|bool{
        if(!array_key_exists('email', $data)){
            return false;
        }
        $data = User::query()->where('email','=', $data['email'])->with('Group')->first();
        if(!$data){
            return false;
        }
        return $data;
    }

    /**
     * Generate secret key for JWT Token
     * @return string
     */
    #[NoDiscard("Because this function generates key, you must consume this method")]
    private function generateSecretKey() :string {
        $strings = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $key = '';
        for($i = 0; $i < 32; $i++){
           $key .= $strings[mt_rand(0, strlen($strings)-1)];
        }
        return $key;
    }
}
