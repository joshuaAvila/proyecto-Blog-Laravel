<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Carbon\Carbon;
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
            
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if($validar->fails()){
            return response()->json($validar->errors()->toJson(),400);
        }
        try{
            $user = User::create([

                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token'),201);
        }catch(Exception $e){

            return response()->json([
                'success' => false,
                'message' => ''.$e
            ]);
    }
        

        

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

    public function saveUserInfo(Request $request){
        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->apellido = $request->apellido;
        $foto = '';
        // Verifica si el usuario proporciona una foto 
        if($request->foto != ''){
            // usar el espacio de nombre tiempo para evitar duplicaciones 
            $foto = time().'.jpg';
            // decode foto string and save to storage/profiles
            file_put_contents('storage/perfiles/'.$foto,base64_decode($request->foto));
            $user->foto = $foto;
        }

        
        $user->update();

        return response()->json([
            'success' => true,
            'foto' => $foto,
        ]);
    }
}
