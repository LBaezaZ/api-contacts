<?php

namespace App\Http\Controllers;

use App\Contacts;
use APP\Helpers\JwtAuth;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function addContact(Request $request){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth();
        $user = $jwtAuth->checkToken($token, true);
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
                'email' => 'required|email|unique:contacts',
                'address' => 'required'
            ]);

            if($validation->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El contacto no se ha creado',
                    'errors' => $validation->errors()
                );
            }else{
                $contact = new Contacts();
                $contact->name = $params_array['name'];
                $contact->email = $params_array['email'];
                $contact->address = $params_array['address'];
                $contact->user_id = $user->id;
                $contact->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El contacto se ha creado exitosamente',
                );
            }
        }
        return response()->json($data, $data['code']);
    }

    public function update(Request $request,$id){
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $contact = Contacts::find($id);
        $validation = \Validator::make($params_array, [
            'name' => 'required|alpha',
            'email' => 'required|email',
            'address' => 'required'
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
            unset($params_array['user_id']);
            Contacts::where('id',$contact->id)->update($params_array);
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El contacto se ha actualizado',
                'user' => $contact,
                'changes' => $params_array
            );
        }
    return response()->json($data, $data['code']);
    }

    public function detail($id){
        $contact = Contacts::find($id);
        if (is_object($contact)){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Se ha encontrado el contacto',
                'user' => $contact,
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El contacto no se ha encontrado',
            );
        }
        return response()->json($data, $data['code']);
    }

    public function list(Request $request){
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth();
        $user = $jwtAuth->checkToken($token, true);
        $contacts = Contacts::where('user_id',$user->id)->get();
        if (is_object($contacts)){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Se han encontrado los contactos',
                'contacts' => $contacts,
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se han encontrado contactos',
            );
        }
        return response()->json($data, $data['code']);
    }
    
    public function delete(Request $request, $id){
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth();
        $user = $jwtAuth->checkToken($token, true);
        $contact = Contacts::where('id',$id)->where('user_id',$user->id)->delete();
        if ($contact){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Se ha eliminado el contacto',
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se ha encontrado el contacto',
            );
        }
        return response()->json($data, $data['code']);
    }
}
