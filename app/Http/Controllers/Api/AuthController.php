<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Validation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;



class AuthController extends Controller
{
    public function login(Request $request){

        $credenciales = $request->only(['email','password']);
        try{
            if(!$token= JWTAuth::attempt($credenciales)){

                return response()->json([
                    'success' => false,
                    'msg' => 'Credenciales invalidas'
                ]);
            }
        }catch(JWTException $e){
            return response()->json(['error' => 'no se creo el token'], 500);
        }
       

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => Auth::user()
            ]);
    }

    public function autenticacionUsuario(){
        try{
            if(!$user = JWTAuth::parseToken()->authenticate()){
                return response()->json(['usuario no encontrado'],404);
            }
        }catch (TokenExpiredException $e) {
            return response()->json(['token_expired'],$e);
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e);
        } catch (JWTException $e) {
                return response()->json(['token_absent'], $e);
        }

        return response()->json(compact('user'));
    }

    public function register(Request  $request){
        Log::info($request); 
        $validar = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if($validar->fails()){
            return response()->json($validar->errors()->toJson(),400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);

    }

    public function logout(Request $request){
        try{
            JWTAuth::invalidate(JWTAuth::parseToken($request->token));
            return  response()->json([
                'success' => true,
                'msg' => 'logout success',
            ]);
        }catch(Exception $e){
            return  response()->json([
                'success' => false,
                'msg' => ''.$e,
            ]);
        }
    }
}
