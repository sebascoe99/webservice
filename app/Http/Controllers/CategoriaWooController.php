<?php

namespace App\Http\Controllers;

use Automattic\WooCommerce\Client;
use App\Http\Controllers;
//use Vendor;
class CategoriaWooController extends Controller
{
    public function consultaCategoria()
    {
        $curl = curl_init();
        $aut= new AutorizacionWooController();
        $headers= $aut->header();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://local.ec/wp-json/wc/v3/products/categories?per_page=100',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_HTTPHEADER => $headers
        ));

        $response = curl_exec( $curl) ;
        curl_close( $curl );

        $categorias = json_decode($response, true);
        //dd($categorias);
        //a partir de aqui
        $arrrayRespuesta = array();

        if (is_array($categorias))
        {
            //dd($categorias);
            foreach ($categorias as $valor) {

                //return json_encode(count($valor['image']));
                if(isset($valor['image'])){
                    $tamañoArregloImagen = (sizeof($valor['image']));

                    if($tamañoArregloImagen >=1){
                        $imagen = $valor['image']['src'];
                    }else{
                        $imagen = " ";
                    }

                }else{
                    $imagen = " ";
                }

                //dd($valor);

                $arregloNuevo = [
                'categoryName' => $valor['name'],
                'categoryId' => $valor['id'],
                'numero_productos' => $valor['count'],
                'imagen' => $imagen
                ];

                $arrrayRespuesta[] = $arregloNuevo;
            }
        //$arrrayRespuesta = json_decode($arrrayRespuesta, true);
            $arrrayRespuesta = array("categorias" => $arrrayRespuesta);
            return  $arrrayRespuesta;
        }
    }


    public function consultaCategoria2(){
        $response= new CategoriaWooController();
        $datosCateg=$response->consultaCategoria();
        $datosCateg = ($datosCateg['categorias']);
        return $datosCateg;
    }

    public function obtenerCategoriaxId($id_categoria){
        $curl = curl_init();
        $aut= new AutorizacionWooController();
        $headers= $aut->header();
        //return ('https://local.ec/wp-json/wc/v3/products/categories/'. $id_categoria);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://local.ec/wp-json/wc/v3/products/categories/'. $id_categoria,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_HTTPHEADER => $headers
        ));

        $response = curl_exec( $curl) ;

        $categorias = json_decode($response, true);
        //return($categorias);

        $arregloNuevo = array();

        if (is_array($categorias))
        {

            $nombreCategoria = $categorias['name'];
            $imagen = " ";

            if(count($categorias['image']) >=1 ){
                $imagen = $categorias['image']['src'];
            }


            $arregloNuevo = [
            'nombreCategoria' => $nombreCategoria,
            'imagen' => $imagen
            ];

            //return $arregloNuevo;

        }
        return $arregloNuevo;
        //return $nombreCategoria;
}



public function categoriaxProducto(){
        $response2 = new ProductoWooController();
        $response= new CategoriaWooController();
        $datosCateg=$response->consultaCategoria();

        $array=json_decode( json_encode( $datosCateg ), true );
        //dd($array);
        //$index=0;

        $arrayDatos=$response2->consultaProdAtributos();
        //dd($arrayDatos);
        if (is_array($array)){

            foreach($array as $ite){
                foreach($ite as $iteNuevo){
                        //dd($iteNuevo);
                    //$arrayDatos=$response2->consultaProductos();
                    $datosProd=json_decode( json_encode( $arrayDatos ), true );
                    //dd($datosProd);
                    $idCategoria = $iteNuevo['categoryId'];
                    $imagenCategoria = $iteNuevo['imagen'];

                    //dd($idCategoria);
                    if (is_array($datosProd))
                    {

                        $arregloDeProductoxCategoria = array();

                        foreach($datosProd as $ite2){
                            $idCategoriaDelProducto = $ite2['Id_categoria'];
                            //dd($idCategoriaDelProducto);

                            if ($idCategoria == $idCategoriaDelProducto)
                            {
                                    $arregloNuevo = null;

									//$textoReducido = explode('<a class="pdfProduct"', $ite2['descripcion']);
									$textoReducido = explode('<h4><strong>Ficha del producto', $ite2['descripcion']);
                                    $arregloNuevo = [
                                        'Nombre_producto' => $ite2["Nombre_producto"],
                                        'Id_producto' => $ite2["Id_producto"],
                                        'sku' => $ite2['sku'],
                                        'Id_categoria' => $ite2["Id_categoria"],
                                        'precio' => $ite2["precio"],
                                        'imagen' => $ite2["imagen"],
                                        'documento' => $ite2['documento'],
                                        'descripcion' =>  $textoReducido[0] . "</p>"
                                        ];
                                    //$nombreProducto = $ite2["Nombre_producto"];
                                    $arregloDeProductoxCategoria[] = $arregloNuevo;
                                    //$indexAux= $indexAux + 1;
                            }

                        }

                    }else{
                        echo "no es array";
                    }
                //dd($arregloDeProductoxCategoria);

                    $nombreCategoria = $iteNuevo['categoryName'];

                    //$arregloConsolidado[$nombreCategoria] = $arregloDeProductoxCategoria;
                    $arregloConsolidado[] = [
                        "NombreCategoria" => $nombreCategoria,
                        "IdCategoria" => $idCategoria,
                        "ImagenCategoria" => $imagenCategoria,
                        "Productos" => $arregloDeProductoxCategoria
                    ];
                    //$index= $index + 1;
                    //$arregloDeProductoxCategoria = null;
                    $nombreCategoria = " ";
                }
        }
        }else{
            echo "no es array";
        }

        $arregloConsolidado1 = $arregloConsolidado;
        return $arregloConsolidado1;
    }

}
