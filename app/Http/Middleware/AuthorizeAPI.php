<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if we have authorization header and secret key of JWT token
        if($request->hasHeader("Authorization") && $request->hasHeader("Authorization-Pass")
         && $request->hasHeader("Token-Pass")){
            $headerData = explode(" ", $request->header("Authorization"));
            // check if authorization is bearer!
            if((stripos($headerData[0], "Bearer") !== false) && array_key_exists(1, $headerData)){
                $jwtToken = $headerData[1];
                $secretKey = $request->header("Authorization-Pass");
                $tokenPass = $request->header("Token-Pass");
                // let's check if jwt token is valid
                try {
                    (array) $token = JWT::decode($jwtToken, new Key($secretKey, "HS256"));
                    if(array_key_exists('user', $token)) {
                        $member = $this->findUser($token['user']);
                        if(password_verify($tokenPass, $member->token_password)){
                            $request->attributes->set("userId", $member->id);
                            $request->attributes->set('isValidated', true);
                            return $next($request);
                        }
                        return response()->json(['errors' => [
                            'unauthorized_token' => 'This token is not signed!'
                        ]], 403);
                    } else {
                        return response()->json(['errors' => [
                            'malformed_token' => 'The token is not valid!'
                        ]], 403);
                    }
                } catch( \InvalidArgumentException $argumentException) {
                    Log::error("{$argumentException->getMessage()} on line {$argumentException->getLine()}");
                    return Response()->json(['errors' => [
                        'invalid_argument_exception' => 'Invalid argument exception'
                    ]], 500);
                } catch (\DomainException $domainException) {
                    Log::error($domainException);
                    return Response()->json(['errors' => [
                        'domain_exception' => 'JWT Token is invalid or malformed'
                    ]], 403);
                } catch (\UnexpectedValueException $unexpectedValueException) {
                    Log::error($unexpectedValueException);
                    return response()->json(['errors' => [
                        'unexpected_value' => "Invalid JWT Token"
                    ]], 403);
                } catch(ExpiredException $expiredException) {
                    Log::error($expiredException);
                    return response()->json(['errors' => [
                        'token_expired' => 'JWT Token is expired'
                    ]], 403);
                }
            }
            return response("Access denied", 403);
        }
        return response("Access denied", 403);
    }

    /**
     * Find authenticated user
     * @param int $userId
     * @return User|null
     */
    private function findUser(int $userId) :User|null {
        return User::query()->find($userId) ?? null;
    }
}
