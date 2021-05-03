<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ConsultasController extends Controller
{
    public function cosultaComboWordPress()
    {
        $consulta = new db();

        if(!isset($conn)){
            $conn = $consulta->conexionBase();
        }

        $query = "CALL CosultaComboWordPress('wordpress');";

        //$query = "SELECT id_tipo_archivo, tipo FROM tipo_archivo WHERE ambiente = 'wordpress'";
        $result = mysqli_query($conn, $query);

        while($row = mysqli_fetch_array($result)){


            $arregloNuevo=[
                'id'   =>  $row['id_tipo_archivo'],
                'tipo' => $row['tipo']
            ];

            $arregloGeneral[] = $arregloNuevo;
                //echo $row['id_tipo_archivo'];
                //echo $row['tipo']
        }

        $response = json_encode($arregloGeneral);
        return $response;
    }


    public function cosultaMagento()
    {

        $consulta = new db();

        if(!isset($conn)){
            $conn = $consulta->conexionBase();
        }

        $query = "CALL CosultaMagento('magento');";

        $result = mysqli_query($conn, $query);

        while($row = mysqli_fetch_array($result)){


            $arregloNuevo=[
                'id'   =>  $row['id_tipo_archivo'],
                'tipo' => $row['tipo']
            ];

            $arregloGeneral[] = $arregloNuevo;
                //echo $row['id_tipo_archivo'];
                //echo $row['tipo']
        }

        $response = json_encode($arregloGeneral);
        return $response;
    }


    public function cosultaGarantia()
    {
        $consulta = new db();

        if(!isset($conn)){
            $conn = $consulta->conexionBase();
        }

        $query = "CALL CosultaGarantia();";

        $result = mysqli_query($conn, $query);

        $row_cnt = $result->num_rows;

        if($row_cnt == 0){
            $arregloNuevo=[
                'id'   =>  "0",
                'titulo' =>  "0",
                'descripcion' => "0"
            ];
            //dd($arregloNuevo);
            $arregloGeneral[] = $arregloNuevo;
            //dd($arregloGeneral);

        }else{

            while($row = mysqli_fetch_array($result)){

                $arregloNuevo=[
                    'id'   =>  $row['id_garantia'],
                    'titulo' => $row['titulo'],
                    'descripcion' => $row['descripcion']
                ];

                $arregloGeneral[] = $arregloNuevo;
                    //echo $row['id_tipo_archivo'];
                    //echo $row['tipo']
            }

        }
        $response = array ("data" => $arregloGeneral);

        return  $response = json_encode($response,JSON_UNESCAPED_UNICODE);

        return $response;
    }



public function deleteGarantia(Request $request)
    {
        try{
            $consulta = new db();

            if(!isset($conn)){
            $conn = $consulta->conexionBase();
            }
            //return $id;
            $id= $request->id;
            //$id = $request->"id";
            $query = "CALL DeleteGarantia('$id');";


            $result = mysqli_query($conn, $query);

            $response ='Eliminado con exito';


        }catch(Exception $e){
            $response = 'ERROR';
        }


    return json_encode($response);

}

    public function consultarImagenes()
    {
       $slider = new SliderController();
       $arregloSlider = $slider->cosultaSlider();
       $consulta = new db();

        if(!isset($conn)){
            $conn = $consulta->conexionBase();
        }

        $query = "CALL ConsultaImagenes();";

        //$query = "SELECT id_tipo_archivo, tipo FROM tipo_archivo WHERE ambiente = 'wordpress'";
        $result = mysqli_query($conn, $query);

        //$arregloGeneral = array();
        //$arregloGeneral = $arregloSlider;

        while($row = mysqli_fetch_array($result)){

            $id = $row['id_tipo_archivo'];
           // $ruta = $this->obtenerRuta($id);
           //dd($row['nombre_archivo']);
           $archivo = str_replace('.', '-094430856064dia-', $row['nombre_archivo']);
            //dd($archivo);
           $arregloData=[
                'id' => $row['id_categoria'],
                'nombre_categoria' => $row['nombre_categoria'],
                'imagen_categoria' => $row['imagen_ruta'],
           ];

            $arregloNuevo=[
                'id_archivo'   =>  $row['id_archivo'],
                'ruta_archivo' =>  env('URL_DOMINIO') . env('URL_IMAGENES') . $id . '/' . $archivo,
                'id_tipo_archivo' => $row['id_tipo_archivo'],
                'contexto' => $row['contexto'],
                'data' => $arregloData,
                'texto_footer' => $row['texto_footer']
            ];

            $arregloGeneral[] = $arregloNuevo;
                //echo $row['id_tipo_archivo'];
                //echo $row['tipo']
        }

        $data = new ProductoWooDestacados();
        $destacados = array();
        $destacados = $data->consultaProdDestacado();
        //$destacados = json_encode($destacados);
        //dd($destacados);
        //$destacados = array ("destacados" => $destacados);
        //$data = array_merge($arregloGeneral, array("destacados"=>$destacados));

        //$arregloGeneral[] = $destacados;

        $response = json_encode(
            array ("slider"      => $arregloSlider,
                   "datos"      => $arregloGeneral,
                   "destacados" =>$destacados)
        );
        //$arregloGeneral[] = array ("datos" => $arregloGeneral);
        //$response = json_encode($arregloGeneral);
        return $response;

    }

    public function ConsultarArchivo($id_tipo_archivo, $nombre_archivo)
    {
        $nombre_archivo = str_replace('-094430856064dia-','.', $nombre_archivo);
        $nombre_archivo = str_replace('%20',' ', $nombre_archivo);
        $rutaCarpeta = env('URL_DISCO') . env('URL_CARPETA') . $id_tipo_archivo .'/'. $nombre_archivo;
        //dd($rutaCarpeta);
        try{
            if(file_exists($rutaCarpeta)){
                $response = new BinaryFileResponse($rutaCarpeta, 200 , []);
            }

        }catch(Exception $e){
            $response = 'ERROR';
        }

    //dd($response);
    return $response;
    }

    public function consultarImagenesMagento()
    {
        $arregloGeneral = array();

        $arregloNoError = [
            'existeError' => "false",
            'mensaje' => "",
        ];


       $consulta = new db();

        if(!isset($conn)){
            $conn = $consulta->conexionBase();
        }

        $query = "CALL ConsultaImagenesMa();";
        $result = mysqli_query($conn, $query);
        $row_cnt = $result->num_rows;

        if($row_cnt == 0){

            $arregloError = [
                'existeError' => "true",
                'mensaje' => "No se encontró ningun elemento",
            ];

            $arregloConsolidado = array ("data" => $arregloGeneral, "informacion" => $arregloError);
            return json_encode( $arregloConsolidado );
        }

        while($row = mysqli_fetch_array($result)){

            $id = $row['id_tipo_archivo'];
           // $ruta = $this->obtenerRuta($id);
           //dd($row['nombre_archivo']);
           $archivo = str_replace('.', '-094430856064dia-', $row['nombre_archivo']);
            //dd($archivo);

            $arregloNuevo=[
                'id_archivo'   =>  $row['id_archivo'],
                'ruta_archivo' =>  env('URL_DOMINIO') . env('URL_IMAGENES') . $id . '/' . $archivo,
                'id_tipo_archivo' => $row['id_tipo_archivo'],
                'contexto' => $row['contexto'],
                'link' => $row['texto_footer']
            ];

            $arregloGeneral[] = $arregloNuevo;
                //echo $row['id_tipo_archivo'];
                //echo $row['tipo']
        }
        $arregloConsolidado = array ("imagenes" => $arregloGeneral);

        $data = new ProductoNuevoOferta();
        $arregloProductosNuevos = $data->consultarProductosNuevos();
        //dd($arregloConsolidado);
        foreach($arregloProductosNuevos as $valor){

            $arregloConsolidado[$valor['nombre_categoria']] = $valor;
        }
        //$arregloProductosOferta = $data->consultarProductosOferta();
        //$arregloConsolidado['OFERTA'] = $arregloProductosOferta;

        $arregloConsolidado["informacion"] = $arregloNoError;

        return json_encode( $arregloConsolidado );

    }


    public function ConsultarArchivoMa($id_tipo_archivo, $nombre_archivo)
    {
        $nombre_archivo = str_replace('-094430856064dia-','.', $nombre_archivo);
        $nombre_archivo = str_replace('%20',' ', $nombre_archivo);
        $rutaCarpeta = env('URL_DISCO') . env('URL_CARPETA') . $id_tipo_archivo .'/'. $nombre_archivo;
        //dd($rutaCarpeta);
        try{
            if(file_exists($rutaCarpeta)){
                $response = new BinaryFileResponse($rutaCarpeta, 200 , []);
            }

        }catch(Exception $e){
            $response = 'ERROR';
        }

    //dd($response);
    return $response;
    }






    public function actualizarMarca(Request $request)
    {
        try{
            $consulta = new db();

            if(!isset($conn)){
            $conn = $consulta->conexionBase();
            }
            //return $id;
            $id= $request->idMarca;
            $descripcion= $request->bodyEditar;            //$id = $request->"id";
            $query = "CALL ActualizarMarcas('$id', '$descripcion');";


            $result = mysqli_query($conn, $query);

            $response ='Actualizado con exito';


        }catch(Exception $e){
            $response = 'ERROR';
        }


    return json_encode($response);
}



public function insertarMarca(Request $request)
    {
        try{
            $consulta = new db();

            if(!isset($conn)){
            $conn = $consulta->conexionBase();
            }
            //return $id;
            $descripcion= $request->contenido;          //$id = $request->"id";
            //dd($descripcion);
            $query = "CALL InsertarMarca('$descripcion');";


            $result = mysqli_query($conn, $query);

            //$response ='Insertado con exito';
            $response = 'Insertado con exito';

        }catch(Exception $e){
            $response = 'ERROR';
        }

    return json_encode($response);

}

public function cosultaMarca()
    {
        $consulta = new db();

        if(!isset($conn)){
            $conn = $consulta->conexionBase();
        }

        $query = "CALL CosultaMarca();";

        $result = mysqli_query($conn, $query);
        //dd($result);
        $row_cnt = $result->num_rows;

        //dd($row_cnt);

        if($row_cnt == 0){
            $arregloNuevo=[
                'id'   =>  "0",
                'descripcion' => "0"
            ];
            //dd($arregloNuevo);
            $arregloGeneral[] = $arregloNuevo;
            //dd($arregloGeneral);

        }else{
        //Inicio arreglo general

            while($row = mysqli_fetch_array($result)){

                $arregloNuevo=[
                    'id'   =>  $row['id'],
                    'descripcion' => $row['descrpcion']
                ];

                $arregloGeneral[] = $arregloNuevo;
                    //echo $row['id_tipo_archivo'];
                    //echo $row['tipo']
            }
        }
        //dd($arregloGeneral);
        //$response = json_encode($arregloGeneral[]);
        $response = json_encode(
                            array ("data" => $arregloGeneral)
                    );
        return $response;
    }

    public function eliminarMarca(Request $request)
    {
            try{
                $consulta = new db();

                if(!isset($conn)){
                $conn = $consulta->conexionBase();
                }
                //return $id;

                $id= $request->id;
                //$id = $request->"id";
                $query = "CALL EliminarMarca('$id');";

                $result = mysqli_query($conn, $query);

                $response ='Eliminado con exito';


            }catch(Exception $e){
                $response = 'ERROR';
            }

        return json_encode($response);

    }

}
