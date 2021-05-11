<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;

class InicioSesionController extends Controller
{
    public function enviarInicioSesion(Request $request)
    {
        $arregloNuevo = array();
        $username=   $request->username;
        $password= $request->password;

        //$respuestaValidacion = array ("code" =>"", "message" => "", "data"=>""  );
        if(!isset($username) || trim($username) == "" && !isset($password) || trim($password) == ""){
            $arregloError = [
                'existeError' => "true",
                'mensaje' => "Error: username y password es vacio o nulo",
            ];

            $arregloConsolidado = array ("data" => $arregloNuevo, "informacion" => $arregloError);
            return json_encode( $arregloConsolidado );
        }
        else if(!isset($username) || trim($username) == ""){
            $arregloError = [
                'existeError' => "true",
                'mensaje' => "Error: username es vacio o nulo",
            ];

            $arregloConsolidado = array ("data" => $arregloNuevo, "informacion" => $arregloError);
            return json_encode( $arregloConsolidado );
        }
        else if(!isset($password) || trim($password) == ""){
            $arregloError = [
                'existeError' => "true",
                'mensaje' => "Error: password es vacio o nulo",
            ];

            $arregloConsolidado = array ("data" => $arregloNuevo, "informacion" => $arregloError);
            return json_encode( $arregloConsolidado );
        }

        $postRequest = array("username" => $username, "password" => $password);
        $data_string = json_encode($postRequest);

        $urlToken = env('URL_TOKEN_MAGENTO');

        $key = "";
        try{
            $ch = curl_init($urlToken);
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

                $arregloError = [
                    'existeError' => "true",
                    'mensaje' => $token->message,
                ];
                $arregloConsolidado = array ("data" => $arregloNuevo, "informacion" => $arregloError);
                return json_encode( $arregloConsolidado );

            }else{
                $key = $token;
            }

        }catch(Exception $e){
            echo 'Error: '.$e->getMessage();
        }

        //return $key;

        $arreglo = $this->obtenerInformacionCiente();
        //$arregloInfo = json_decode($arreglo, true);
        //dd($arreglo);

        if(sizeof($arreglo) >= 1 || $arreglo == null){

            $arregloNoError = [
                'existeError' => "false",
                'mensaje' => "",
            ];

            foreach($arreglo as $info){

                foreach($info as $info2){
                    //$username = json_decode($username);
                    //dd($info2);
                    $email = $info2['email'];
                    //dd($email);
                    if($email == $username){

                        $id_cliente = $info2['id'];
                        $nombre = $info2['firstname'];
                        $apellido = $info2['lastname'];
                        $email = $info2['email'];
                        $region_code = "";
                        $region= "";

                        //dd()

                        if(sizeof($info2['addresses']) >= 1){
                            //dd($info2['addresses']);
                            if(array_key_exists('region', $info2['addresses']['0'])){
                                //dd($info2['addresses']['0']);
                                $region_code = $info2['addresses']['0']['region']['region_code'];
                                $region = $info2['addresses']['0']['region']['region'];
                            }
                        }


                        $arregloNuevo = [
                            'token' => $key,
                            'id_cliente' => $id_cliente,
                            'email' => $email,
                            'nombre' => $nombre,
                            'apellido' => $apellido,
                            'region_code' => $region_code,
                            'region' => $region,
                        ];

                        //return json_encode($arregloNuevo);
                        $arregloConsolidado = array ("data" => $arregloNuevo, "informacion" => $arregloNoError);
                        return json_encode($arregloConsolidado);

                    }
                }


            }

        }else{
            $arregloError = [
                'existeError' => "true",
                'mensaje' => "Error: No se pudo realizar la consulta",
            ];
            $arregloConsolidado = array ("data" => $arregloNuevo, "informacion" => $arregloError);
            return json_encode( $arregloConsolidado );
        }

    }


    public function obtenerInformacionCiente(){
        $aut = new OrdenController();
        $tokenAdmin= $aut->token3();

        $urlInfo = env('URL_INFO_CLIENTE');

        $requestUrl = $urlInfo;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$tokenAdmin
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

        $info = json_decode($response, true);

        return  $info;

    }

}
