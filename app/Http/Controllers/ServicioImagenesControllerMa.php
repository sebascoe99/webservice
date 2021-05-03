<?php

namespace App\Http\Controllers;

use App\Http\Controllers;

use App\Http\Controllers\db;

use App\Http\Controllers\ServicioImagenesController;

use Illuminate\Http\Request;

use Session;



class ServicioImagenesControllerMa extends Controller
{
    public function implementarFormularioMagento(Request $request)
    {
                $consulta = new db();
                $response= new CategoriaWooController();

                $id_combo = (int)$_POST['id_combo2'];
                $contexto = (int)$_POST['contexto'];
                $id_categoria = 0;
                $texto_footer = $_POST['texto_footer'];
                //return json_encode($id_categoria);
                if($contexto == 1){
                    $contexto = "interno";
                }else{
                    $contexto = "externo";
                }

                $nombreCategoria = " ";
                $imagen = " ";

                $tipoImagen = $_FILES['imagen']['type'];


                if(($tipoImagen == "image/png") || ($tipoImagen == "image/jpg") || ($tipoImagen == "image/jpeg") || ($id_combo == 5)){

                    $dir_subida = env('URL_DISCO');
                    $carpeta = env('URL_CARPETA');

                    $nombre = $_FILES['imagen']['name'];
                    $guardado = $_FILES['imagen']['tmp_name'];
                    //dd($id_combo, $contexto, $vista, $link, $texto_footer);
                    $rutaCarpeta = $dir_subida.$carpeta.$id_combo;

                    //$carpeta = @scandir($rutaCarpeta);

                    if(!file_exists($rutaCarpeta)){//Si el archivo no existe

                        mkdir($rutaCarpeta, 0777, true);

                        if(file_exists($rutaCarpeta)){//Si el archivo se creo y existe

                            $carpetaEvaluar = @scandir($rutaCarpeta);
                            //
                            if(count($carpetaEvaluar) == 2){
                                //return json_encode("entro aca");
                                //return json_encode($message = 'Entro en el if del 2');

                                if(!file_exists($rutaCarpeta.'/'.$nombre)){//si el nombre del archivo no existe


                                    if(move_uploaded_file($guardado, $rutaCarpeta.'/'.$nombre)){//mueve el archivo de la ruta temporal a la que se asigno

                                        $conn = $consulta->conexionBase();
                                        if(isset($conn)){
                                            //$texto_footer = $texto_footer;
                                            //$imagen = $imagen;
                                            $ruta = $nombre;

                                            $query = "CALL InsertarDatos('$ruta', '$id_combo', '$contexto', '$id_categoria', '$nombreCategoria', '$imagen', '$texto_footer');";
                                            $result = mysqli_query($conn, $query);

                                            if(!$result){
                                                $message = "Query failed";
                                            }
                                        }
                                        $message = 'Archivo guardado con exito';
                                        //session(['mensaje' => '<sc language="javascript">alert("Archivo guardado con exito");</script>']);

                                    }else{
                                        $message =  'Error al guardar';
                                    }
                                }else{
                                    $message = 'Ya existe un archivo con este nombre!';
                                }
                            }

                            if(count($carpetaEvaluar) == 3){

                                if(!file_exists($rutaCarpeta.'/'.$nombre)){//si el nombre del archivo no existe


                                    $files = glob($rutaCarpeta.'/*'); //obtenemos todos los nombres de los ficheros
                                    //return json_encode($files);
                                        foreach($files as $file){
                                            if(is_file($file))
                                            unlink($file); //elimino el fichero
                                            //return json_encode($message = "Archivos eliminados");
                                        }


                                    if(move_uploaded_file($guardado, $rutaCarpeta.'/'.$nombre)){//mueve el archivo de la ruta temporal a la que se asigno
                                        $conn = $consulta->conexionBase();
                                        if(isset($conn)){
                                            $ruta = $nombre;

                                            $query = "CALL ActualizarDatos('$ruta', '$id_combo', '$contexto', '$id_categoria', '$nombreCategoria', '$imagen', '$texto_footer');";
                                            $result = mysqli_query($conn, $query);

                                            if(!$result){
                                                $message = "Query failed";
                                            }
                                        }


                                        $messages = 'Archivo renombrado con exito';
                                        //session(['mensaje' => '<sc language="javascript">alert("Archivo guardado con exito");</script>']);

                                    }else{
                                        $message =  'Error al guardar';
                                    }
                                }else{
                                    $message = 'Ya existe un archivo con este nombre!';
                                }
                            }
                        }else{
                            $message = 'No se pudo crear el archivo!';
                        }

                    }else{

                        $dir_subida = env('URL_DISCO');
                        $carpeta = env('URL_CARPETA');

                        $nombre = $_FILES['imagen']['name'];
                        $guardado = $_FILES['imagen']['tmp_name'];

                        //return json_encode($dir_subida.$carpeta.$id_combo);
                        $rutaCarpeta = $dir_subida.$carpeta.$id_combo;

                        //return json_encode($message = $rutaCarpeta);

                        if(file_exists($rutaCarpeta)){//Si el archivo se creo y existe
                            //$message =  'Error';

                            //return json_encode($message = "Entro en el if si existe la carpeta");

                            $carpetaEvaluar = @scandir($rutaCarpeta);

                            //return json_encode(count($carpeta));

                            if(count($carpetaEvaluar) == 2){


                                if(!file_exists($rutaCarpeta.'/'.$nombre)){//si el nombre del archivo no existe
                                    //return json_encode($message = "Entro en el if si el nombre existe");

                                    if(move_uploaded_file($guardado, $rutaCarpeta.'/'.$nombre)){//mueve el archivo de la ruta temporal a la que se asigno
                                        $conn = $consulta->conexionBase();
                                        if(isset($conn)){
                                            $ruta = $nombre;

                                             $query = "CALL InsertarDatos('$ruta', '$id_combo', '$contexto', '$id_categoria', '$nombreCategoria', '$imagen', '$texto_footer');";
                                            $result = mysqli_query($conn, $query);

                                            if(!$result){
                                                $message = "Query failed";
                                            }
                                        }
                                        $message = 'Archivo guardado con exito';
                                        //session(['mensaje' => '<sc language="javascript">alert("Archivo guardado con exito");</script>']);

                                    }else{
                                        $message =  'Error al guardar';
                                    }
                                }else{
                                    $message = 'Ya existe un archivo con este nombre!';
                                }
                            }

                            if(count($carpetaEvaluar) == 3 && !($id_combo == 1)){

                                //return json_encode($message = "Entro en count 3");

                                if(!file_exists($rutaCarpeta.'/'.$nombre)){//si el nombre del archivo no existe
                                    //return json_encode($message = "Entro hasta el si el nombre del archivi no existe");

                                    $files = glob($rutaCarpeta.'/*'); //obtenemos todos los nombres de los ficheros
                                    //return json_encode($files);
                                        foreach($files as $file){
                                            if(is_file($file))
                                            unlink($file); //elimino el fichero
                                            //return json_encode($message = "Archivos eliminados");
                                        }

                                    //return json_encode($message = "Antes de mover la imagen");

                                    //return json_encode($message = $rutaCarpeta);

                                    if(move_uploaded_file($guardado, $rutaCarpeta.'/'.$nombre)){

                                        //return json_encode($message = "Entro en el archivo de mover");
                                        $conn = $consulta->conexionBase();
                                        if(isset($conn)){

                                            $ruta = $nombre;

                                            $query = "CALL ActualizarDatos('$ruta', '$id_combo', '$contexto', '$id_categoria', '$nombreCategoria', '$imagen', '$texto_footer');";
                                            $result = mysqli_query($conn, $query);

                                            if(!$result){
                                                $message = "Query failed";
                                            }
                                        }


                                        $message = 'Archivo renombrado con exito';
                                        //session(['mensaje' => '<sc language="javascript">alert("Archivo guardado con exito");</script>']);

                                    }else{
                                        $message =  'Error al guardar';
                                    }
                                }else{
                                    $message = 'Ya existe un archivo con este nombre!';
                                }

                            }

                        }else{
                            $message = 'No se pudo crear el archivo!';
                        }

                    }
            }else{
                $message = 'Debe tener el formato adecuado (.png o .jpg)';
            }
            return json_encode($message);

    }


}




