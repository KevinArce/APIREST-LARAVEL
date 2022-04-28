<?php
namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Cursos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CursosController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->head("Autorization");
        $clientes = Clientes::all([]);

        $json = [];

        foreach ($clientes as $key => $value) {
            if ("Basic" . base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) == $token) {
                if (isset($_GET["page"])) {
                    $cursos = DB::table("cursos")
                        ->join("clientes", "cursos.id_creador", "=", "clientes_id")
                        ->select("cursos.id", "cursos.titulo", "cursos.descripcion", "cursos.instructor", "cursos.imagen", "cursos.id_creador", "clientes.nombres", "clientes.apellido")
                        ->paginate(25);
                } else {
                    $cursos = DB::table("cursos")
                        ->join("clientes", "cursos.id_creador", "=", "clientes_id")
                        ->select("cursos.id", "cursos.titulo", "cursos.descripcion", "cursos.instructor", "cursos.imagen", "cursos.id_creador", "clientes.nombres", "clientes.apellido")
                        ->get();
                }

                if (!empty($cursos)) {
                    $json = [
                        "status" => 200,
                        "total_registros" => count($cursos),
                        "detalle" => $cursos
                    ];

                    return json_encode($json, true);
                } else {
                    $json = [
                        "status" => 200,
                        "total_registros" => 0,
                        "detalle" => "no hay registro"
                    ];

                    return json_encode($json, true);
                }
            } else {
                $json = [
                    "status" => 404,
                    "detalle" => "no estas autorizado"
                ];

                return json_encode($json, true);
            }
        }
    }

    public function store(Request $request)
    {
        $token = $request->head("Autorization");
        $clientes = Clientes::all();

        $json = [];

        foreach ($clientes as $cliente) {
            if ("Basic" . base64_encode($cliente["id_cliente"] . ":" . $cliente["llave_secreta"]) == $token) {
                $datos = [
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio"),
                ];

                if (!empty($datos)) {
                    $validar = Validator::make($datos, [
                        "titulo" => "required|string|max:255|unique:cursos",
                        "descripcion" => "required|string|max:255|unique:cursos",
                        "instructor" => "required|string|max:255",
                        "imagen" => "required|string|max:255|unique:cursos",
                        "precio" => "required|numeric",
                    ]);
                }

                if ($validar->fails()) {
                    $errors = $validar->errors();
                    $json = [
                        "status" => 404,
                        "dettale" => "error de validación",
                        "descripción" => $errors
                    ];

                    return json_encode($json, true);
                } else {
                    $cursos = new Cursos();
                    $cursos->titulo = $datos["titulo"];
                    $cursos->descripcion = $datos["descripcion"];
                    $cursos->instructor = $datos["instructor"];
                    $cursos->imagen = $datos["imagen"];
                    $cursos->precio = $datos["precio"];
                    $cursos->id_creador = $datos["id"];
                    $cursos->save();

                    $json = [
                        "status" => 200,
                        "detalle" => "Su curso ha sido guardado",
                    ];

                    return json_encode($json, true);
                }
            } else {
                $json = [
                    "status" => 404,
                    "detalle" => "el registro no puedo ser ingresado"
                ];
                return json_encode($json, true);
            }
        }
    }


    public function show($id, Request $request)
    {
        $token = $request->head("Autorization");
        $clientes = Clientes::all();

        $json = [];

        foreach ($clientes as $key => $value) {
            if ("Basic" . base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) == $token) {
                $curso = Cursos::where("id", $id)->get();

                if (!empty($curso)) {
                    $json = [
                        "status" => 200,
                        "detalle" => $curso,
                        "descripcion" => "Este es el curso solicitado"
                    ];
                } else {
                    $json = [
                        "status" => 200,
                        "detalle" => "No hay ningun registro de ese curso"
                    ];
                }
            } else {
                $json = [
                    "status" => 404,
                    "detalle" => "No esta autorizado para recibir los registros"
                ];
            }
        }

        return json_encode($json, true);
    }

    public function update($id, Request $request)
    {
        $token = $request->head("Autorization");
        $clientes = Clientes::all();

        $json = [];

        foreach ($clientes as $value) {
            if ("Basic" . base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) == $token) {

                $datos = [
                    "titulo" => $request->input("titulo"),
                    "descripcion" => $request->input("descripcion"),
                    "instructor" => $request->input("instructor"),
                    "imagen" => $request->input("imagen"),
                    "precio" => $request->input("precio"),
                ];

                if (!empty($datos)) {
                    $validar = Validator::make($datos, [
                        "titulo" => "required|string|max:255|unique:cursos",
                        "descripcion" => "required|string|max:255|unique:cursos",
                        "instructor" => "required|string|max:255",
                        "imagen" => "required|string|max:255|unique:cursos",
                        "precio" => "required|numeric",
                    ]);
                }

                if ($validar->fails()) {
                    $errors = $validar->errors();
                    $json = [
                        "status" => 404,
                        "detalle" => $errors
                    ];

                    return json_encode($json, true);
                } else {
                    $traer_curso = Cursos::where("id", $id)->get();

                    if ($value["id"] === $traer_curso[0]["id_creador"]) {
                        $datos = [
                            "titulo" => $datos["titulo"],
                            "descripcion" => $datos["descripcion"],
                            "instructor" => $datos["instructor"],
                            "imagen" => $datos["imagen"],
                            "precio" => $datos["precio"],
                        ];
                    }

                    $json = [
                        "status" => 200,

                    ];
                }
            } else {
                $json = [
                    "status" => 404,
                    "detalle" => "el registro no puedo ser ingresado"
                ];
                return json_encode($json, true);
            }
        }
    }

    public function destroy($id, Request $request){
        $token = $request->head("Autorization");
        $clientes = Clientes::all();

        $json = [];

        foreach ($clientes as $key => $value) {
            if ("Basic" . base64_encode($value["id_cliente"] . ":" . $value["llave_secreta"]) == $token) {
                $validar = Cursos::where("id",$id)->get();
                if(!empty($validar)){
                    if($value["id"] === $validar[0]["id_creador"]){
                        $curso = Cursos::where("id",$id)->delete();

                        $json = [
                            "status" => 200,
                            "detalle" => "Se ha borrado su curso con exito"
                        ];

                        return json_encode($json,true);
                    }else{
                        $json = [
                            "status" => 404,
                            "detalle" => "No esta autorizado para cambiar los datos"
                        ];

                        return json_encode($json,true);
                        
                    }
                }else{
                    $json = [
                        "status" => 404,
                        "detalle" => "No se ha encontrado el curso"
                    ];

                    return json_encode($json,true);
                }
            }
        }    
        
    }
}
