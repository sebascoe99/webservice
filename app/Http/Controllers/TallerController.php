<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;

class TallerController extends Controller
{

    public function cosultaTaller()
    {
        $consulta = new db();

        $conn = $consulta->conexionBaseWordpress();

        $query = "SELECT wp_posts.post_content FROM wp_posts LEFT JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id) LEFT JOIN wp_term_taxonomy ON (wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id) WHERE wp_posts.post_title like '%Talleres%' and wp_posts.post_status = 'publish' GROUP BY wp_posts.ID";
            //$query = "SELECT id_tipo_archivo, tipo FROM tipo_archivo WHERE ambiente = 'wordpress'";
        $result = mysqli_query($conn, $query);
        $row_cnt = $result->num_rows;

        if(!($row_cnt == 0)){

            $ciudad = "";
            $negocio = "";
            $nombre = "";
            $telefono ="";
            $direccion = "";
            $correo = "";

            foreach($result as $valor){
                //dd($valor);
                $array = explode("\r\n", $valor['post_content']);
                //dd($array);

                foreach($array as $valor2){

                    //var_dump(strlen($valor2));

                    if (strlen($valor2) >= 10) {


                        if (strpos($valor2, 'title') == true) {
                            $ciudad = $valor2;
                        }
                        //dd($ciudad);
                        if (strpos($valor2, 'span') == true) {
                            $negocio = $valor2;
                        }

                        //dd(strpos($valor2, "nombre"));
                        if (strpos($valor2, "NOMBRE") == true || strpos($valor2, "nombre") == true) {
                            $nombre = $valor2;
                        }

                        if (strpos($valor2, 'TELÉFONO') == true || strpos($valor2, 'teléfono') == true) {
                            $telefono = $valor2;
                        }

                        if (strpos($valor2, 'DIRECCIÓN') == true || strpos($valor2, 'dirección') == true) {
                            $direccion = $valor2;
                        }


                        if (strpos($valor2, 'CORREO') == true || strpos($valor2, 'correo') == true) {
                            //dd($correo);
                            //var_dump("entro 1");
                            $correo = $valor2;

                            $arregloNuevo=[
                                'ciudad' => $ciudad,
                                'negocio'   =>  $negocio,
                                'nombre' => $nombre,
                                'telefono'   =>  $telefono,
                                'direccion'   =>  $direccion,
                                'correo'   =>  $correo,
                            ];

                            $arregloFinal[] = $arregloNuevo;
                            $arregloNuevo = null;

                            $ciudad ="";
                            $negocio = "";
                            $nombre = "";
                            $telefono ="";
                            $direccion ="";
                            $correo ="";

                        }

                    }else if((strlen($valor2) <= 10) && (strlen($direccion) >= 1)){
                        //var_dump("entro");
                        $correo = "";

                        $arregloNuevo=[
                            'ciudad' => $ciudad,
                            'negocio'   =>  $negocio,
                            'nombre' => $nombre,
                            'telefono'   =>  $telefono,
                            'direccion'   =>  $direccion,
                            'correo'   =>  $correo,
                        ];

                        $arregloFinal[] = $arregloNuevo;
                        $arregloNuevo = null;

                        $ciudad ="";
                        $negocio = "";
                        $nombre = "";
                        $telefono ="";
                        $direccion ="";
                        $correo ="";
                    }

                }//forHijo

            }//forPadre

            return json_encode($arregloFinal);
        }
    }
}
