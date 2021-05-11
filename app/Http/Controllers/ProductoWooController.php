<?php

namespace App\Http\Controllers;

use Automattic\WooCommerce\Client;
use App\Http\Controllers;
//use Vendor;
class ProductoWooController extends Controller
{
    public function consultaProductos()
    {
        $aut= new AutorizacionWooController();
        $headers= $aut->header();
        $curl = curl_init();
        //dd($headers);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://local.ec/wp-json/wc/v3/products?per_page=100',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_HTTPHEADER => $headers
        ));

        $response = curl_exec( $curl) ;
        curl_close( $curl );
        // $response2 = json_encode($response);
        //dd($response);
        // $productall = json_encode($response, TRUE);
        //$product= $this->consultaProdAtributos($response);
        //enviar a formato json
        $product = json_decode( $response );
        return $product;
    }

    public function consultaProdAtributos(){

        $response=$this->consultaProductos();
        //dd($response);
        $array=json_decode( json_encode( $response ), true );
        //dd($array);

        $respuesta=array();
        $regex = '/https?\:\/\/[^\",]+/i';
        $index= 0;
        $imagen= " ";
        foreach ($array as $ite) {
            //dd($ite);
            //$tamañoArregloImagen = (count($ite['images']));
            $tamañoArregloImagen = (int)(sizeof($ite['images']));
            //dd($tamañoArregloImagen);

            if($tamañoArregloImagen >= 1){
                //dd("entro en el if");
                $imagenFinal = array();
                    //dd($ite);
                foreach($ite['images'] as $ite2){
                    //dd($ite2);
                    $imagen = $ite2['src'];
                    //dd($imagen);
                    $imagenFinal[] = array("img" =>
                                            $imagen);
                    $imagen = "";
                }

                //dd($imagenFinal[])
            }else{
                //dd("entro aca");
                //$imagenFinal[] = array();
                //$imagenFinal[] = null;
                $imagenFinal = $imagenFinal = array();
            }

            //return json_encode($ite['description']);
            if($ite['description'] == "" || $ite['description'] ==  null){
                $url = "";
                $descripcion = "";
            }
            else{
                //dd($ite['description']);
                preg_match_all($regex,$ite['description'],$url);
                if(array_key_exists('0', $url)){
                    if(array_key_exists('0', $url['0'])){
                        $url = $url['0']['0'];
                    }else{
                        $url = "";
                    }
                }else{
                    $url = "";
                }

                $descripcion = $ite['description'];
                //dd($url);
            }

            if($ite['short_description'] == ""){
                $short_descripcion = "";
            }
            else{
                $short_descripcion = $ite['short_description'];
                //dd($url);
            }
            //preg_match_all($regex,$ite['description'],$url);

            $sku = $ite['sku'];

                $productoAtributos=[
                    'Id_categoria' => $ite['categories']['0']['id'],
                    'Nombre_categoria' => $ite['categories']['0']['name'],
                    'Id_producto' => $ite['id'],
                    'Nombre_producto' => $ite['name'],
                    'precio' => $ite['price'],
                    'imagen' => $imagenFinal,
                    'documento' => $url,
                    'sku' => $sku,
                    'short_descripcion' => $short_descripcion,
                    'descripcion' => $descripcion,
                    'destacado' => $ite['featured']
                 ];

            $respuesta[$index]= $productoAtributos;
            $index=$index+1;
        }

        return $respuesta;
    }

}
