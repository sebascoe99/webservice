<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function consultarBlog(){
        $arregloDeBlog = array();

        $orden = new OrdenController();
        $tokenAdmnistrador = $orden->token3();
        //dd($tokenAdmnistrador);

        $urlConsulta = env('URL_CONSULTAR_BLOG');
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

        foreach($response as $r){

            $descripcion_corta = "";

            if(array_key_exists('short_description', $r)){
                if( !($r['short_description'] == "" || $r['short_description'] == " " || $r['short_description'] == null) ){
                    $descripcion_corta = $r['short_description'];
                }
            }

            if(array_key_exists('post_content', $r)){
                if( !($r['post_content'] == "" || $r['post_content'] == " " || $r['post_content'] == null) ){
                    $contenido = $r['post_content'];
                }
            }

            if(array_key_exists('image', $r)){
                if( !($r['image'] == "" || $r['image'] == " " || $r['image'] == null) ){
                    $imagen = $r['image'];
                }
            }

            $arregloBlog = [
                'id' => $r['id'],
                'nombre' => $r['name'],
                'description_corta' => $descripcion_corta,
                'contenido' => $contenido,
                'imagen' => env('URL_IMAGE_BLOG') . $imagen
            ];

            $arregloDeBlog[] = $arregloBlog;
            $arregloBlog = array();
        }

        $arregloConsolidado[] = array ("data" => $arregloDeBlog);

        return json_encode($arregloConsolidado);

    }
}
