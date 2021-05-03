<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;

use Illuminate\Http\Request;


class MagentoController extends BaseController
{

    public function token3(){

        $urlToken = env('URL_TOKEN_MAGENTO');

        $apiUser = 'sebastian.coello1@gmail.com';
        $apiPass = '7hubTS7RH6LBK92';
        $apiUrl = $urlToken;
        $key;

        $data = array("username" => $apiUser, "password" => $apiPass);
        $data_string = json_encode($data);

        try{
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
            );
            $token = curl_exec($ch);
            $token = json_decode($token);

            if(isset($token->message)){
                echo $token->message;
            }else{
                $key = $token;
            }
        }catch(Exception $e){
            echo 'Error: '.$e->getMessage();
        }

        return $key;
    }


    public function categoria(){
        $arregloDeCategoria = array();
        //$arregloDeSub = array();

        $ca = new ProductoPorCategoriaController();
        $producto = new ProductoPorCategoriaController();

        $arregloNoError = [
            'existeError' => "false",
            'mensaje' => "",
        ];

        $token = $this->token3();

        $urlCategoria = env('URL_TOKEN_CATEGORIA');

        $requestUrl = $urlCategoria;
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
        //dd($response);


        $categoria = json_decode($response, TRUE);

        //$arregloProductos = $producto->producto($token);
        $arregloDeSub = array();

        if(is_array($categoria)){

            foreach($categoria['children_data'] as $cate){
                //dd($cate);
                $arregloCateg = array();

                $id_categoria = $cate['id'];
                $imagen = $ca->obtenerImagenCategoria($id_categoria, $token);

                $arregloCateg = [
                    'Nombre_SubCategoria' => $cate['name'],
                    'id_SubCategoria' => $id_categoria,
                    'activo' => $cate['is_active'],
                    'imagen' => $imagen,
                    'id_SubCategoria' => $cate['id'],
                ];

                $arregloDeCategoria[] = $arregloCateg;

            }

            $arregloConsolidado = array("categorias" => $arregloDeCategoria,
                                            "informacion" => $arregloNoError);

                return json_encode($arregloConsolidado, true);

        }

        $arregloError = [
            'existeError' => "true",
            'mensaje' => "Error: No se encontro ninguna categoria",
        ];

        $arregloConsolidado = array("categorias" => $arregloDeCategoria,
                                    "informacion" => $arregloError);

        return json_encode($arregloConsolidado, true);

    }


    function obtenerImagenCategoria($id){
        $token = $this->token3();
        $urlCategoria = env('URL_TOKEN_CATEGORIA') . "/" . $id;
        //dd($urlCategoria);

        $requestUrl = $urlCategoria;
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
        $response = json_decode($response, TRUE);
        //dd($response);

        foreach ($response['custom_attributes'] as $valor) {

            if($valor['attribute_code'] == "image"){
                $imagen = env('URL_STORE') . $valor['value'];
                break;
            }else{
                $imagen = " ";
            }
        }
        //dd($imagen);
        return $imagen;
    }

}
