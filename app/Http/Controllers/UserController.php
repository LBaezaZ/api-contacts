<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\User;
use Illuminate\Http\Request;
use function GuzzleHttp\json_decode;

class UserController extends Controller
{
    public function signUp(Request $request){
       //recoger dats enviados por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
      
        // validar datos
        if(empty($params_array)){
            $data = array(
                'status' => 'error',
                'code' => 406,
                'message' => 'Los campos no pueden estar vacios',
            );
        }else{
            $validation = \Validator::make($params_array, [
                'name' => 'required|alpha|min:3',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8'
            ]);

            if($validation->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validation->errors()
                );
            }else{
                $password = hash('sha256',$params_array['password']);

                $user = new User();
                $user->name = $params_array['name'];
                $user->email = $params_array['email'];
                $user->password = $password;
                $user->save();


                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado exitosamente',
                );
            }
        }
        return response()->json($data, $data['code']);
    }

    public function signIn(Request $request){
        $JwtAuth = new JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $validation = \Validator::make($params_array, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if($validation->fails()){
            $signup = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'No se ha podido iniciar sesion',
                'errors' => $validation->errors()
            );
        }else{
            $email = $params_array['email'];
            $password = hash('sha256',$params_array['password']);
            $signup = $JwtAuth->signup($email,$password);
            if(!empty($params_array['gettoken'])){
                $signup = $JwtAuth->signup($email,$password,true);
            }
        }
        return response()->json($signup, 200);
    }

    public function update(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $user = $jwtAuth->checkToken($token, true);
        $validation = \Validator::make($params_array, [
            'name' => 'required|alpha',
            'email' => 'required|email'
        ]);
        if($validation->fails()){
            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Los campos no son validos',
                'errors' => $validation->errors()
            );
        }else{
            unset($params_array['id']);
            unset($params_array['created_at']);
            unset($params_array['remenmber_token']);
            unset($params_array['password']);
            User::where('id',$user->id)->update($params_array);
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El usuario se ha actualizado',
                'user' => $user,
                'changes' => $params_array
            );
        }
    return response()->json($data, $data['code']);
    }

    public function detail($id){
        $user = User::find($id);
        if (is_object($user)){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Se ha encontrado al usuario',
                'user' => $user,
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha encontrado',
            );
        }
        return response()->json($data, $data['code']);
    }
}
