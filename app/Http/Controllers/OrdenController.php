<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Models\User;

use Illuminate\Http\Request;


class OrdenController extends BaseController
{

    public function token3(){

        $urlToken = env('URL_ADMIN_MAGENTO');

        $apiUser = 'magentoAdmin';
        $apiPass = 'vmt**2021';
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


    public function orden(){

        $token = $this->token3();

        $urlOrden = env('URL_ORDEN');

        $requestUrl = $urlOrden;
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
        $orden = json_decode($response, true );
        $orden = array($orden);
        //dd($orden);
        $arregloOrden = $this->obtenerDatosOrden($orden);

        return json_encode($arregloOrden, true);
    }

    function obtenerDatosOrden($orden){
        $arrrayRespuesta = array();

        $index = 0;

        foreach ($orden as $valor) {
            //dd($valor);
            $arregloProductosComprados = array();

            foreach ($valor['items'] as $valor2){
                //dd($valor2);
                $producto = [
                    'nombre_producto' => $valor2['name'],
                    'sku' => $valor2['sku'],
                    'precio' => $valor2['price'],
                    'cantidad_de_productos' => $valor2['qty_ordered']
                ];
                //dd($producto);
                $arregloProductosComprados[] = $producto;
            }

            $arregloNuevo = [
            'id' => $valor['entity_id'],
            'moneda' => $valor['base_currency_code'],
            'productos' =>  $arregloProductosComprados,
            'cantidad_productos_total' => $valor['total_qty_ordered'],
            'total' => $valor['base_grand_total'],
            'nombre_cliente' => $valor['customer_firstname'],
            'apellido_cliente' => $valor['customer_lastname'],
            'telefono' => $valor['billing_address']['telephone'],
            'email_cliente' => $valor['customer_email'],
            'ciudad' => $valor['billing_address']['city'],
            'direccion1' => $valor['billing_address']['street']['0']
            //'direccion2' => $valor['billing_address']['street']['1'],


            ];

            $arrrayRespuesta[] = $arregloNuevo;
            $index = $index + 1;
        }

        return  $arrrayRespuesta;
    }
}
