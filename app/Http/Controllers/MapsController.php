<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MapsController extends Controller
{
    public function cosultaMaps()
    {
        $consulta = new db();

        if(!isset($conn)){
            $conn = $consulta->conexionBaseWordpress();
        }

        $query = "SELECT m.object_name, m.json_object, m.deleted FROM wpdb.wp_hmapsprem_default_storage_table m where m.deleted = 0";
            //$query = "SELECT id_tipo_archivo, tipo FROM tipo_archivo WHERE ambiente = 'wordpress'";
        $result = mysqli_query($conn, $query);
        $row_cnt = $result->num_rows;

        if(!($row_cnt == 0)){

            //$result = json_decode(json_encode($result));
            $array = array();
            $arrrayRespuesta = array();
            $arrrayConsolidado = array();

            //dd($result);
            foreach($result as $valor){

                if(array_key_exists('json_object', $valor)){
                    $nombreCategoria = $valor['object_name'];
                }
                //dd($nombreCategoria);
                $array = json_decode($valor['json_object'], true);

                $arregloCoordenadas = array();
                $latitud  = " ";
                $title = " ";
                $contentido = " ";

                if(array_key_exists('map_markers', $array)){
                        //dd($valor2);
                    foreach($array['map_markers'] as $valor2){
                            $latitud = $valor2['latlng'];
                            $title = $valor2['title'];
                            $contentido = $valor2['info_window_content'];

                            $arregloNuevo=[
                                'title' => $title,
                                'latitud'   =>  $latitud,
                                'contentido' => $contentido
                            ];

                            $arregloCoordenadas[] = $arregloNuevo;
                            $arregloNuevo = null;
                    }
                }


                $arregloNuevo2=[
                    'provincia' => $nombreCategoria,
                    'coordenas' => $arregloCoordenadas,
                ];


                $arrrayRespuesta[] = $arregloNuevo2;
                //$arrrayRespuesta = array ($nombreCategoria => $arrrayRespuesta)
            }

        }

        $response = json_encode($arrrayRespuesta);

        return $response;
    }


}
