<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;

class GravityFormsController extends Controller
{
    public function enviarGravityForm(Request $request)
    {
        $nombre=   $request->nombre;
        $apellido= $request->apellido;
        $celular=  $request->celular;
        $correo=   $request->correo;
        $mensaje=  $request->mensaje;

        $respuestaValidacion = array ("code" =>"", "message" => "", "data"=>""  );

        if(!isset($nombre) || trim($nombre) == ""){
            $respuestaValidacion["code"]    = "01";
            $respuestaValidacion["message"] = "Error nombre es vacio o nulo";
            return json_encode( $respuestaValidacion );
        }
        else if(!isset($apellido) || trim($apellido) == ""){
            $respuestaValidacion["code"]    = "04";
            $respuestaValidacion["message"] = "Error apellido es vacio o nulo";
            return json_encode( $respuestaValidacion );
        }
        else if(!isset($celular) || trim($celular) == ""){
            $respuestaValidacion["code"]    = "02";
            $respuestaValidacion["message"] = "Error celular es vacio o nulo";
            return json_encode( $respuestaValidacion );
        }
        else if(!isset($correo) || trim($correo) == ""){
            $respuestaValidacion["code"]    = "03";
            $respuestaValidacion["message"] = "Error correo es vacio o nulo";
            return json_encode( $respuestaValidacion );
        }
        else if(!isset($mensaje) || trim($mensaje) == ""){
            $respuestaValidacion["code"]    = "05";
            $respuestaValidacion["message"] = "Error mensaje es vacio o nulo";
            return json_encode( $respuestaValidacion );
        }
        /*validar datos de entrada */
        //
        $postRequest = array(
            'form_id' => "3",
            '1' => $nombre,
            '4' => $apellido,
            '2' => $celular,
            '3' => $correo,
            '5' => $mensaje
        );

        $aut= new AutorizacionWooController();

        $headers= $aut->headerGravity();
        //return json_encode($headers);

        $curl = curl_init();
        $urlGravity = env('URL_GRAVITY_FORM');

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
        return $response;



    }



    public function enviarGravityForm2(Request $request)
    {
        $nombre=   $request->nombre;
        $apellido= $request->apellido;
        $correo=   $request->correo;

        $respuestaValidacion = array ("code" =>"", "message" => "", "data"=>""  );

        if(!isset($nombre) || trim($nombre) == ""){
            $respuestaValidacion["code"]    = "01";
            $respuestaValidacion["message"] = "Error nombre es vacio o nulo";
            return json_encode( $respuestaValidacion );
        }
        else if(!isset($apellido) || trim($apellido) == ""){
            $respuestaValidacion["code"]    = "04";
            $respuestaValidacion["message"] = "Error apellido es vacio o nulo";
            return json_encode( $respuestaValidacion );
        }
        else if(!isset($correo) || trim($correo) == ""){
            $respuestaValidacion["code"]    = "03";
            $respuestaValidacion["message"] = "Error correo es vacio o nulo";
            return json_encode( $respuestaValidacion );
        }
        /*validar datos de entrada */
        //
        $postRequest = array(
            'form_id' => "1",
            '1' => $nombre,
            '4' => $apellido,
            '3' => $correo,
        );

        $aut= new AutorizacionWooController();

        $headers= $aut->headerGravity();
        //return json_encode($headers);

        $curl = curl_init();
        $urlGravity = env('URL_GRAVITY_FORM');

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
        // $response2 = json_encode($response);
        //dd($response);
        // $productall = json_encode($response, TRUE);
        //$product= $this->consultaProdAtributos($response);
        //enviar a formato json
        $response = json_decode( $response, TRUE );
        return $response;


    }
}
