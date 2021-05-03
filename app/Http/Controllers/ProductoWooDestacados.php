<?php

namespace App\Http\Controllers;

use Automattic\WooCommerce\Client;
use App\Http\Controllers;
//use Vendor;
class ProductoWooDestacados extends Controller
{
    public function consultarProductos()
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

        $product = json_decode( $response );
        return $product;
    }

    public function consultaProdDestacado(){
        $response2 = new ProductoWooController();
        $arrayDatos=$response2->consultaProdAtributos();

        $datosProd=json_decode( json_encode( $arrayDatos ), true );

        if (is_array($datosProd))
            {
                $arregloDeProductoxCategoria = array();
                $index = 0;
                foreach($datosProd as $ite2){

                    if ($ite2['destacado'] == true)
                    {
                            $arregloNuevo = null;
							
							$textoReducido = explode('<h4><strong>Ficha del producto', $ite2['descripcion']);

                            $arregloNuevo = [
                                'Id_producto' => $ite2["Id_producto"],
                                'Nombre_producto' => $ite2["Nombre_producto"],
                                'sku' => $ite2['sku'],
                                'Nombre_categoria' => $ite2['Nombre_categoria'],
                                'Id_categoria' => $ite2["Id_categoria"],
                                'precio' => $ite2["precio"],
                                'imagen' => $ite2["imagen"],
                                'documento' => $ite2['documento'],
                                'short_descripcion' => $ite2['short_descripcion'],
                                'descripcion' => $textoReducido[0] . "</p>"
                                ];

                            $respuesta[] = $arregloNuevo;


                            //$index=$index+1;
                            //$indexAux= $indexAux + 1;
                    }

                }

                //$response = array ("destacados" => $respuesta);
            }else{
                return $respuesta = "no es array";
            }

            return $respuesta;
    }


}
