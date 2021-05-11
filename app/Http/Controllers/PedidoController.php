<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function obtenerIdCarrito($token)
    {
        //$token = $request->token;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer' . " " . $token
        );


        $curl = curl_init();
        $urlCarrito = env('URL_ACTIVAR_CARRITO');

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlCarrito,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_HTTPHEADER => $headers
        ));

        $response = curl_exec( $curl) ;
        curl_close( $curl );


        $response = json_decode( $response, TRUE );

        //$response[] = $id;

        return $response;

    }



    public function agregarProductos(Request $request)
    {
        $datos = $request->datos;
        $token = $request->token;
        $id = (int)$this->obtenerIdCarrito($token);
        //$datos = (json_encode($datos, TRUE));


        foreach($datos as $d){
            //dd($d);
            //dd($d['sku']);
            $response;

            $sku = $d['sku'];
            $qty = $d['cantidad'];

            $postRequest = array(
                'cart_item' => array(
                'quote_id' => $id,
                'sku' => $sku,
                'qty' => $qty)
            );


            $headers = array(
                'Content-Type: application/json',
                'Authorization: Bearer' . " " . $token
            );

            $curl = curl_init();
            $urlGravity = env('URL_AGREGAR_PRODUCTO');

            curl_setopt_array($curl, array(
                CURLOPT_URL => $urlGravity,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_SSL_VERIFYPEER=> false,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => json_encode($postRequest)
            ));

            $response = curl_exec( $curl) ;
            curl_close( $curl );

            $response = json_decode( $response, TRUE );

            $arregloResponse[] = $response;
        }

        return $arregloResponse;

    }

    public function verProductosAgregados(Request $request){
        //$a = $request->hasHeader("token"); VALIDAR SI LA VARIABLE EXISTE (TRUE O FALSE)
        $arregloDeItem = array();
        $token = $request->header("token");
        //dd($token);
        $id = (int)$this->obtenerIdCarrito($token);

        $orden = new OrdenController();
        $tokenAdmnistrador = $orden->token3();
        //dd($tokenAdmnistrador);

        $urlConsulta = env('URL_VER_PRODUCTOS_AGG') . $id . "/items";
        $urlConsulta = trim($urlConsulta);
        //return ($urlConsulta);

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer' . " " . $tokenAdmnistrador
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlConsulta,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec( $curl) ;
        curl_close( $curl );

        $response = json_decode( $response, TRUE );
        ///dd($response);

        foreach($response as $r){
            //dd($r);

            $arregloItem = [
                'item_id' => $r['item_id'],
                'nombre' => $r['name'],
                'sku' => $r['sku'],
                'cantidad' => $r['qty'],
                'precio' => $r['price'],
                'quote_id' => $r['quote_id']
            ];

            $arregloDeItem[] = $arregloItem;
            $arregloItem = array();
        }

        $arregloConsolidado[] = array ("data" => $arregloDeItem);
        
        return json_encode($arregloConsolidado, TRUE);

    }
}
