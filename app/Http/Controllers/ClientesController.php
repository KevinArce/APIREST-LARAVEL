<?php
namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ClientesController extends Controller
{
    public function index(){
        $json = [
            "detalle" => "no encontrado"
        ];

        return json_encode($json,true);
    }

    public function store(Request $request){
        $datos = [
            "nombre" => $request->input("nombre"),
            "apellido" => $request->input("apellido"),
            "email" => $request->input("email"),
        ];

        if(!empty($datos)){

            $validar = Validator::make($datos,[
                "nombre" => "required|string|max:255",
                "apellido" => "required|string|max:255",
                "email" => "required|string|max:255|unique:clientes"
            ]);
            
            if($validar->fails()){
                $errors = $validar->errors();
                $json = [
                    "status" => 404,
                    "dettale" => "error de validación",
                    "descripción" => $errors
                ];
    
                return json_encode($json,true);
            }else{
                $id_cliente = Hash::make($datos["nombre"],$datos["apellido"],$datos["email"]);
                $llave_secreta = Hash::make($datos["nombre"],$datos["apellido"],$datos["email"],["rounds" => 15]);
                $cliente = new Clientes();
                $cliente->nombre = $datos["nombre"];
                $cliente->apellido = $datos["apellido"];
                $cliente->email = $datos["email"];
                $cliente->id_cliente =  str_replace("$","a",$id_cliente);
                $cliente->llave_secreta =  str_replace("$","e",$llave_secreta);
    
                $cliente->save();
                $json = [
                    "status" => 200,
                    "detalle" => "registro exitoso, anote sus credenciales y guardelas",
                    "credenciales" => [
                        "id_cliente" => $id_cliente,
                        "llave_secreta" => $llave_secreta
                    ]
                ];
    
            }
        }else{
            $json = [
            "status" => 404,
            "detalle" => "el registro no puedo ser ingresado"
            ];
        }
        
        return json_encode($json,true);

    }

}
