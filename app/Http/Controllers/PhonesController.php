<?php

namespace App\Http\Controllers;
use App\Phones;
use Illuminate\Http\Request;

class PhonesController extends Controller
{
    public function addPhone(Request $request){
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
                'phone' => 'required|min:10|max:10',
            ]);

            if($validation->fails()){
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'El telefono de contacto no se ha creado',
                    'errors' => $validation->errors()
                );
            }else{
                $phone = new Phones();
                $phone->phone = $params_array['phone'];
                $phone->contact_id = $params_array['contact_id'];
                $phone->save();
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El telefono de contacto se ha registrado exitosamente',
                );
            }
        }
        return response()->json($data, $data['code']);
    }

    public function update(Request $request,$id){
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $phone = Phones::find($id);
        $validation = \Validator::make($params_array, [
            'phone' => 'required|min:10|max:10'
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
            unset($params_array['contact_id']);
            Phones::where('id',$phone->id)->update($params_array);
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'El telefono de contacto se ha actualizado',
                'phone' => $phone,
                'changes' => $params_array
            );
        }
    return response()->json($data, $data['code']);
    }

    public function detail($id){
        $phone = Phones::find($id);
        if (is_object($phone)){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Se ha encontrado el telefono de contacto',
                'phone' => $phone,
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El telefono de contacto no se ha encontrado',
            );
        }
        return response()->json($data, $data['code']);
    }

    public function list(Request $request, $id){
        $phones = Phones::where('contact_id',$id)->get();
        if (is_object($phones)){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Se han encontrado los telefonos de contacto',
                'phones' => $phones,
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se han encontrado los telefonos contactos',
            );
        }
        return response()->json($data, $data['code']);
    }
    
    public function delete(Request $request, $id){
        $contact = Phones::where('id',$id)->delete();
        if ($contact){
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Se ha eliminado el telefono de contacto',
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se ha encontrado el telefono de contacto',
            );
        }
        return response()->json($data, $data['code']);
    }
}
