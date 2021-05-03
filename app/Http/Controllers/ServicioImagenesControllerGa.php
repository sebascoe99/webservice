<?php

namespace App\Http\Controllers;

use App\Http\Controllers;

use App\Http\Controllers\db;

use App\Http\Controllers\ServicioImagenesController;

use Illuminate\Http\Request;

use Session;



class ServicioImagenesControllerGa extends Controller
{
    public function implementarFormularioGarantia(Request $request)
    {
            $consulta = new db();

            $contenido = $_POST['contenido'];
            $titulo = $_POST['titulo'];
            //$aniogarantia = (int)$_POST['aniogarantia'];

                if(isset($contenido) && isset($titulo)){

                    $conn = $consulta->conexionBase();

                        if(isset($conn)){

                            $query = "CALL InsertarGarantia('$titulo', '$contenido');";
                            $result = mysqli_query($conn, $query);

                            if(!$result){
                                return $message = "Query failed";

                            }

                            $message = 'Archivo guardado con exito';

                        }else{
                        $message = 'Conexion error!';
                        }

                }else{
                    $message = 'Debe ingresar todos los campos';
                }


            return json_encode($message);
            //$c->llamarVistaMagento();
    }

    public function implementarModalEditar(Request $request)
    {
            $consulta = new db();

            $idGarantia = $_POST['idGarantia'];
            $titulo = $_POST['titulo'];
            $bodyEditar = $_POST['bodyEditar'];

            //return json_encode($idGarantia. $bodyEditar. $anios);
                $conn = $consulta->conexionBase();

                    if(isset($conn)){

                        $query = "CALL EditarGarantia('$idGarantia', '$titulo', '$bodyEditar');";
                        $result = mysqli_query($conn, $query);

                        if(!$result){
                            return $message = "Query failed";

                        }
                        $message = 'Archivo guardado con exito';

                    }else{
                    $message = 'Conexion error!';
                    }

            return json_encode($message);
    }

}




