<?php

namespace App\Http\Controllers;

use App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;

use Illuminate\Http\Request;


class ProductoController extends BaseController
{

    public function producto(){
        //$MagentoController = new OrdenController();
        $MagentoController = new MagentoController();

        $token = $MagentoController->token3();

        $urlProducto = env('URL_PRODUCTO_MAGENTO');

        $requestUrl = $urlProducto;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $requestUrl,
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

        $product = json_decode($response, TRUE);

        $arregloConsolidado = $this->obtenerDatosProducto($product['items'], $token);

        return  $arregloConsolidado;
    }

    function obtenerDatosProducto($arrayProducto, $token){
        $arregloNoError = [
            'existeError' => "false",
            'mensaje' => "",
        ];
        //$arregloFinal[] = array();

        foreach ($arrayProducto as $valor) {
            //dd($valor);

            if(array_key_exists('category_links', $valor['extension_attributes'])){

                //dd(sizeof($valor['extension_attributes']['category_links']));
                //compara si el producto tiene mas de 2 categorias
                if(sizeof($valor['extension_attributes']['category_links']) >=2){

                    foreach($valor['extension_attributes']['category_links'] as $categoria){
                        //dd($categoria);
                        //itera la primer id de la categoria del producto
                        $id_categoria = $categoria['category_id'];
                        //dd($valor);
                        $arregloProducto = $this->armarProducto($valor, $token, $id_categoria);
                        //dd($arregloProducto);
                        $arregloFinal[] = $arregloProducto;
                        //$id_categoria = 0;

                    }

                }else if(sizeof($valor['extension_attributes']['category_links']) == 1 ){

                    //dd($valor);
                    $id_categoria = $valor['extension_attributes']['category_links']['0']['category_id'];

                    $arregloProducto = $this->armarProducto($valor, $token, $id_categoria);
                    //dd($arregloProducto);
                    $arregloFinal[] = $arregloProducto;

                }else{
                    $id_categoria = 0;
                    //dd($valor);
                    $arregloProducto = $this->armarProducto($valor, $token, $id_categoria);
                    //dd($arregloProducto);
                    $arregloFinal[] = $arregloProducto;
                }

            }else{
                    $id_categoria = 0;
                    //dd($valor);
                    $arregloProducto = $this->armarProducto($valor, $token, $id_categoria);
                    //dd($arregloProducto);
                    $arregloFinal[] = $arregloProducto;
            }


        }
        //return  $arregloFinal;
        $arregloFinal = array ("productos" => $arregloFinal, "informacion" => $arregloNoError);
        //array_push($arregloFinal, $arregloNoError);

        return  json_encode($arregloFinal);
    }


    public function armarProducto($valor, $token, $id_categoria){
        //dd($valor);
        $descripcion = " ";
        $destacado = "No";
        $precioPromocion = " ";
        //dd($valor);
        //saber el producto pertenece a destacado
        foreach($valor['custom_attributes'] as $valor2){

            if($valor2['attribute_code'] == "sw_featured"){
                if($valor2['value'] == 1){
                    $destacado = "Si";
                }
            }

            if($valor2['attribute_code'] == "description"){
                $descripcion = $valor2['value'];
            }

            if($valor2['attribute_code'] == "special_price"){
                $precioPromocion = $valor2['value'];
                $precioPromocion = bcdiv($precioPromocion, '1', 2);

            }
        }

        $sku = $valor['sku'];
        //dd($sku);
        if(isset($sku)){
            $stockArreglo[] = $this->obetenerStock($token, $sku);
            foreach ($stockArreglo as $valor3){
                $stock = $valor3['qty'];
            }

        }else{
            $sku = " ";
            $stock = "0";
        }

        $file = array();

        //dd($valor);
        if(array_key_exists('media_gallery_entries', $valor)){
            if(sizeof($valor['media_gallery_entries']) >= 1){

                foreach ($valor['media_gallery_entries'] as $image){
                    $file[] = env('URL_HOME_IMAGEN') . $image['file'];
                }
            }
        }

        $doc = " ";
        //$categoriaId = "default";


        if (array_key_exists('downloadable_product_links', $valor['extension_attributes'])) {
            $doc = env('URL_HOME_PDF') . $valor['extension_attributes']['downloadable_product_links']['0']['id'];
        }



        if ($id_categoria == 0) {

            $nombreCategoria = "Sin categorizar";
            $id_categoria = "0";

        }else{
            $nombreCategoria = $this->obtenerNombreCategoria($id_categoria, $token);
        }
    //Obtengo el nombre de la categoria
    //dd($valor);
        $precio = $valor['price'];
        settype($precio, 'string');

        $arregloNuevo = [
        'id_categoria' => $id_categoria,
        'nombreCategoria' => $nombreCategoria,
        'id_producto' => $valor['id'],
        'nombre_producto' => $valor['name'],//nombre
        'sku' => $sku,
        'stock' => $stock,//stock
        'destacado' => $destacado,
        'precioNormal' => $precio,//price
        'precioPromocion' => $precioPromocion,
        'descripcion' => $descripcion,
        'imagen' =>  $file,//$valor['media_gallery_entries']['0']['file'],//archivo
        'doc' => $doc//imagen
        ];

        return $arregloNuevo;
        //$index = $index + 1;
}


    public function obtenerNombreCategoria($categoriaId, $tokenActual){

        //Datos
        $arregloData['token']       =  $tokenActual;
        $arregloData['requestUrl'] = env('URL_TOKEN_CATEGORIA');

        //Consumo de API CATETGORIA
        $arregloCategoria = $this->consumoAPICategorias($arregloData);

        //dd($arregloCategoria);
        $nombreCategoria = "";
        foreach ($arregloCategoria['children_data'] as $categoriaItem) {

            if ($categoriaItem['id'] == $categoriaId)
            {
                $nombreCategoria = $categoriaItem["name"];
                break;
            }
            else if(array_key_exists('children_data', $categoriaItem)){

                if(sizeof($categoriaItem['children_data']) >=1){
                    foreach ($categoriaItem['children_data'] as $sub){

                        if ($sub['id'] == $categoriaId)
                        {
                            $nombreCategoria = $sub["name"];
                            break;
                        }
                    }

                }
            }
        }//fin for

        return $nombreCategoria;

    }


    public function consumoAPICategorias($array){
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $array['token']
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $array['requestUrl'],
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
        //dd($response);


        $categoria = json_decode($response, TRUE);

        return $categoria;//Todo el response

    }

    public function obetenerStock($tokenActual, $sku){
        $ordenController = new OrdenController();
        $token = $ordenController->token3();

        $urlStock = env('URL_OBTENER_STOCK') . $sku;
        $requestUrl = $urlStock;

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $requestUrl,
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

        $product = json_decode($response, TRUE);

        return $product;

    }




}
