<?php

namespace App\Http\Controllers;

use Automattic\WooCommerce\Client;
use App\Http\Controllers;
//use Vendor;
class ProductoNuevoOferta extends Controller
{
    public function consultarProductosNuevos()
    {
        $MagentoController = new MagentoController();

        $token = $MagentoController->token3();
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => env('URL_TOKEN_CATEGORIA'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_HTTPHEADER => $headers
        ));

        $response = curl_exec( $curl);
        curl_close( $curl );

        $arregloCategoria = json_decode($response, TRUE);
        //dd($arregloCategoria);
        $arregloDeCategoria = array();
        foreach ($arregloCategoria['children_data'] as $categoriaItem) {
            //dd($categoriaItem);
            $name = $categoriaItem['name'];

            if ($name == 'NUEVOS' || $name == 'nuevos' || $name == 'OFERTA' || $name == 'oferta')
            {
                $arregloNuevo = [
                    'id_categoria' => $categoriaItem["id"],
                    'nombre_categoria' => $categoriaItem["name"],
                ];

                $arregloDeCategoria[] = $arregloNuevo;
            }

        }

        //dd($arregloDeCategoria);
        $productos = new ProductoPorCategoriaController();
        $arregloProductos = $productos->producto($token);
        //dd($arregloProductos);

        foreach ($arregloDeCategoria as $categoria) {
            $id_categoria = $categoria['id_categoria'];
            $nombre_categoria = $categoria['nombre_categoria'];

            $arreglo = array();

            foreach ($arregloProductos['items'] as $valor) {


                if(array_key_exists('category_links', $valor['extension_attributes'])){

                    foreach($valor['extension_attributes']['category_links'] as $categoria2){

                        $id_categoriaProducto = (int)$categoria2['category_id'];
                        //dd($id_categoriaProducto);

                        if($id_categoriaProducto == $id_categoria){
                            //dd($valor);
                            $arregloProducto = $productos->armarProducto($valor, $token);
                            $arreglo[] = $arregloProducto;
                            $arregloProducto = array();
                        }

                    }

                }
            }//foreach producto

            $arregloCategoriaxPro = [
                'nombre_categoria' => $nombre_categoria,
                'id_categoria' => $id_categoria,
                'Productos' => $arreglo
            ];

            $arregloConsolidado[] = $arregloCategoriaxPro;
            //$arregloConsolidado
        }//foreach categorias Oferta y Nuevo

        return $arregloConsolidado;


    }


    public function consultarProductosOferta(){

        $MagentoController = new MagentoController();
        $token = $MagentoController->token3();

        $productos = new ProductoPorCategoriaController();
        $arregloProductos = $productos->producto($token);


        foreach ($arregloProductos['items'] as $valor) {

            foreach($valor['custom_attributes'] as $valor2){

                if($valor2['attribute_code'] == "special_price"){
                    $id_categoriaProducto = 0;
                    $arreglo = array();
                    $arregloCategoriaxPro = array();

                    $arregloProducto = $productos->armarProducto($valor, $token);
                    $arreglo[] = $arregloProducto;
                    $arregloProducto = array();

                    if(sizeof($valor['extension_attributes']['category_links']) >= 1){
                        $id_categoriaProducto = $valor['extension_attributes']['category_links']['0']['category_id'];
                    }

                    $arregloCategoriaxPro = [
                        'id_categoria' => $id_categoriaProducto,
                        'Productos' => $arreglo
                    ];

                    $arregloConsolidado[] = $arregloCategoriaxPro;
                }
            }
        }//foreach producto

        return $arregloConsolidado;

    }


}
