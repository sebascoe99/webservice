<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function cosultaSlider()
    {

        $consulta = new db();

        if(!isset($conn)){
            $conn = $consulta->conexionBaseWordpress();
        }

        $query = "SELECT params FROM wpdb.wp_revslider_slides where slider_id = 1";
            //$query = "SELECT id_tipo_archivo, tipo FROM tipo_archivo WHERE ambiente = 'wordpress'";
        $result = mysqli_query($conn, $query);
        $row_cnt = $result->num_rows;

        if(!($row_cnt == 0)){

            //$result = json_decode(json_encode($result));
            //dd(json_encode($result));
            $arrrayRespuesta = array();

            foreach($result as $valor){

                $array = array();
                $imagen = " ";
                $link = " ";

                //$arrrayValor = $valor;
                $array = json_decode($valor['params'], true);


                if(!(array_key_exists('publish', $array))){


                    if(array_key_exists('bg', $array)){
                        if(array_key_exists('image', $array['bg'])){
                            $imagen = ($array['bg']['image']);
                        }
                    }

                    if(array_key_exists('seo', $array)){
                        if(array_key_exists('set', $array['seo'])){
                            if($array['seo']['set'] == "true"){
                                $link = $array['seo']['link'];
                            }
                        }
                    }


                    $arregloNuevo=[
                        'image' => $imagen,
                        'linkDestino'   =>  $link
                    ];

                    $arrrayRespuesta[] = $arregloNuevo;

                }

            }

            //$arrrayRespuesta = array ("slider" => $arrrayRespuesta);

        }

        $response = $arrrayRespuesta;

        return $response;
    }


    public function cosultaSlider2(){
        $arraySlider = array();
        $arraySlider = $this->cosultaSlider();

        $arraySlider = array ("slider" => $arraySlider);

        $response = json_encode($arraySlider);

        return $response;

    }
}
