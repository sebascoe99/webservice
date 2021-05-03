<?php

namespace App\Http\Controllers;

use App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;

use Illuminate\Http\Request;


class ProductoPorCategoriaController extends BaseController
{

    public function productoPorCategoria(Request $request){

        $id = (int)$request->id;
        $MagentoController = new MagentoController();

        $token = $MagentoController->token3();
        $arregloData['token']       =  $token;
        $arregloData['requestUrl'] = env('URL_TOKEN_CATEGORIA');

        $arregloCategoria = $this->consumoAPICategorias($arregloData);
        //dd($arregloCategoria);

        $arregloDeCategoria = array();

        foreach ($arregloCategoria['children_data'] as $categoriaItem) {
            $id_categoria = (int)$categoriaItem["id"];

            if ($id == $id_categoria){
                $arregloSubCategoria = array();

                $imagen = $this->obtenerImagenCategoria($id_categoria, $token);

                if(array_key_exists('children_data', $categoriaItem)){
                    $arregloSubCategoria = $categoriaItem['children_data'];
                }

                $arregloNuevo = [
                    'id_categoria' => $id_categoria,
                    'nombre_categoria' => $categoriaItem["name"],
                    'imagen' => $imagen,
                    'subcategorias' => $arregloSubCategoria
                ];
                $arregloDeCategoria[] = $arregloNuevo;
            }
        }

        if(!(sizeof($arregloDeCategoria) >=1 )){
            $arregloConsolidado = array();

            $arregloError = [
                'existeError' => "true",
                'mensaje' => "Error: No se encontro ninguna categoria",
            ];

            $arregloConsolidado = array ("data" => $arregloConsolidado, "informacion" => $arregloError);
            return json_encode($arregloConsolidado);
        }

        //dd($arregloDeCategoria);
        $arregloProductos = $this->producto($token);
        //dd($arregloProductos);

        $arregloNoError = [
            'existeError' => "false",
            'mensaje' => "",
        ];

        foreach ($arregloDeCategoria as $categoria) {
            $arregloPro = array();
            $arregloProSub = array();
            $arregloProducto = array();
            $id_categoria = (int)$categoria['id_categoria'];
            $categoria_name = $categoria['nombre_categoria'];
            $imagen = $categoria['imagen'];

                foreach ($arregloProductos['items'] as $valor) {

                    if(array_key_exists('category_links', $valor['extension_attributes'])){

                        if(sizeof($valor['extension_attributes']['category_links']) >=2){

                            foreach($valor['extension_attributes']['category_links'] as $categoria2){

                                $id_categoriaProducto = $categoria2['category_id'];

                                if($id_categoriaProducto == $id_categoria){
                                    //dd($valor);
                                    $arregloProducto = $this->armarProducto($valor, $token);
                                    $arregloPro[] = $arregloProducto;
                                }
                                //Ver si algun producto pertenece a una subcategoria
                            }
                        }

                        else if(sizeof($valor['extension_attributes']['category_links']) == 1){

                            $id_categoriaProducto = $valor['extension_attributes']['category_links']['0']['category_id'];

                            if($id_categoriaProducto == $id_categoria){
                                //dd($valor);
                                $arregloProducto = $this->armarProducto($valor, $token);
                                $arregloPro[] = $arregloProducto;
                            }

                             //Ver si algun producto pertenece a una subcategoria

                        }

                    }//Cerrar if si tiene alguna categoria asociada

                }//terminacion del foreach hijo (Productos)

                $arregloDeSub = array();

                if(sizeof($categoria['subcategorias'])>=1){


                    foreach ($categoria['subcategorias'] as $sub) {
                        $arregloSub = array();
                        $arreglo = array();
                        $imagenSub = "";

                        $id_subcategoria = (int)$sub['id'];
                        $categoria_subname = $sub['name'];

                        foreach ($arregloProductos['items'] as $valor) {

                            if(array_key_exists('category_links', $valor['extension_attributes'])){

                                foreach($valor['extension_attributes']['category_links'] as $categoria2){

                                    $id_categoriaProducto = (int)$categoria2['category_id'];
                                    //dd($id_categoriaProducto);

                                    if($id_categoriaProducto == $id_subcategoria){
                                        //dd($valor);
                                        $arregloProductoSub = $this->armarProducto($valor, $token);
                                        $imagenSub = $this->obtenerImagenCategoria($id_subcategoria, $token);
                                        $arreglo[] = $arregloProductoSub;
                                        $arregloProductoSub = array();
                                    }
                                }

                            }
                        }

                        $arregloSub = [
                            'Nombre_SubCategoria' => $categoria_subname,
                            'id_SubCategoria' => $id_subcategoria,
                            'imagen' => $imagenSub,
                            'Productos' => $arreglo,
                        ];

                        $arregloDeSub[] = $arregloSub;
                    }

                }//Cerrar if si existe subcategorias


            $arregloFinal = [
                'subcategorias' => $arregloDeSub
            ];

            $arregloConsolidado[] = $arregloFinal;
            $arregloFinal = array();

        }//terminacion del foreach padre (Categorias)

        $arregloConsolidado[] = array ("informacion" => $arregloNoError);
        return json_encode($arregloConsolidado);

    }


    public function obtenerImagenCategoria($id, $token){
        $urlProducto = env('URL_TOKEN_CATEGORIA')."/".$id;

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

        $categoria = json_decode($response, TRUE);
        //dd($categoria);
        $imagen = "";

        foreach($categoria['custom_attributes'] as $cat){

            //dd($cat['attribute_code']);
            if($cat['attribute_code'] == "image"){
                $imagen =  env('URL_STORE') . $cat['value'];
            }

        }

        return $imagen;
    }

    public function armarProducto($valor, $token){

        $precioPromocion = " ";
        $descripcion = " ";
        $destacado = "No";
        //saber el producto pertenece a destacado
        //dd($valor);
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

        $file = " ";

        if(array_key_exists('media_gallery_entries', $valor)){
            if(array_key_exists('0', $valor['media_gallery_entries'])){
                if(array_key_exists('file', $valor['media_gallery_entries']['0'])){
                    $file = env('URL_HOME_IMAGEN') . $valor['media_gallery_entries']['0']['file'];
                }
            }
        }
        //dd($file);

        $doc = " ";
        $categoriaId = "default";

        if (array_key_exists('downloadable_product_links', $valor['extension_attributes'])) {
            $doc = env('URL_HOME_PDF') . $valor['extension_attributes']['downloadable_product_links']['0']['id'];
        }


        $precio = $valor['price'];
        settype($precio, 'string');

        $arregloNuevo = [
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
        //dd($categoria);
        return $categoria;

    }



    public function producto($token){
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

        return  $product;
    }


    public function consumoProductoxsku($sku){
        $arregloNuevo = array();

        $urlProducto = env('URL_PRODUCT') . $sku;
        //dd($urlProducto);
        $MagentoController = new MagentoController();
        $token = $MagentoController->token3();

        $requestUrl = $urlProducto;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
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

        if(sizeof($product["media_gallery_entries"]) >=1 ){
            $imagen = env('URL_IMAGE_MINIATURA') . $product["media_gallery_entries"]['0']['file'];
        }else{
            $imagen = " ";
        }

        $arregloProducto = [
            'nombre' => $product["name"],
            'precio' => $product["price"],
            'imagen' => $imagen
        ];
        //dd($product);
        return $arregloProducto;
    }


}
