<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use Illuminate\Http\Request;

class TallerController2 extends Controller
{

    public function cosultaTaller()
    {
        $replaceArray = [
            ['[vc_row]', '<vc_row>'],
            ['[vc_column width="1/2"]', '<vc_column width="1/2">'],
            ['[vc_tta_accordion', '<vc_tta_accordion'],
            ['[vc_separator]', ''],
            ['[/vc_column_text]', '</vc_column_text>'],
            ['][vc_column_text]', '><vc_column_text>'],
            ['[vc_column_text]', '<vc_column_text>'],
            ['[/vc_tta_section]', '</vc_tta_section>'],
            ['][vc_tta_section', '><vc_tta_section'],
            ['[vc_tta_section', '<vc_tta_section'],
            ['[/vc_tta_accordion]', '</vc_tta_accordion>'],
            ['[vc_single_image', '<vc_single_image'],
            ['[/vc_row]', '</vc_row>'],
            ['][/vc_column]', '></vc_column>'],
            ['[/vc_column]', '</vc_column>'],

            ['<vc_single_image image="3549" img_size="large" label="">', ''],

            ['<strong>', ''],
            ['</strong>', '|'],
        ];





        $consulta = new db();

        $conn = $consulta->conexionBaseWordpress();

        $query = "SELECT wp_posts.post_content FROM wp_posts LEFT JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id) LEFT JOIN wp_term_taxonomy ON (wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id) WHERE wp_posts.post_title like '%Talleres%' and wp_posts.post_status = 'publish' GROUP BY wp_posts.ID";
            //$query = "SELECT id_tipo_archivo, tipo FROM tipo_archivo WHERE ambiente = 'wordpress'";
        $result = mysqli_query($conn, $query);
        $row_cnt = $result->num_rows;

        foreach($result as $valor){

            $valor = $valor['post_content'];

            foreach($replaceArray as $item){
                $valor = str_replace($item[0], $item[1], $valor);
            }
        }
        //dd($array);
        //$array = json_decode($array);
        $leerXml = simplexml_load_string("<?xml version='1.0'  encoding='UTF-8'?>" . $valor);

        $eliminadorTildes = [
            ["á", "a"],
            ["é", "e"],
            ["í", "i"],
            ["ó", "o"],
            ["ú", "u"],
            ["Á", "A"],
            ["É", "E"],
            ["Í", "I"],
            ["Ó", "O"],
            ["Ú", "U"],
        ];

        $arrayElementos = [];
		$arrayGlobal = [];
        foreach($leerXml->vc_column[0]->vc_tta_accordion->vc_tta_section as $item){
            $atributos = $item->attributes();
            $itemArreglo = [];
            $itemArreglo["idWordPress"] = strval($atributos["tab_id"]);
            $itemArreglo["ciudad"] = strval($atributos["title"]);

            $subItemArray = [];

            foreach($item->vc_column_text as $subI){
                $subA = [];
				$subB = [];
				$subB["Ciudad"] = strval($atributos["title"]);
                foreach($subI->h4 as $elementos){
                    if(!(count($elementos)>0) && $elementos!=""){
                        $elem = explode("|", $elementos);
                        //echo $elementos;
                        //echo "<br/>";
                        $indiceAplicar = trim($elem[0]);
                        foreach($eliminadorTildes as $itemTilde){
                            $indiceAplicar = str_replace($itemTilde[0], $itemTilde[1], $indiceAplicar);
                        }
                        $subA[$indiceAplicar] = trim(str_replace(chr( 194 ) . chr( 160 ),"",$elem[1]));
						$subB[$indiceAplicar] = trim(str_replace(chr( 194 ) . chr( 160 ),"",$elem[1]));
                    }
                    if(count($elementos)>0){
                        $subA["negocio"] = strval($elementos->span);
						$subB["negocio"] = strval($elementos->span);
                    }
                }
                if(count($subI->h3)>0){
					$subA["negocio"] = strval($subI->h3->span);
					$subB["negocio"] = strval($subI->h3->span);
				}

                $subA["negocio"] = str_replace("|", "", $subA["negocio"]);
				$subB["negocio"] = str_replace("|", "", $subA["negocio"]);
				
				$subB["CORREO"] = $subB["CORREO"] ?? "";

                array_push($subItemArray, $subA);
				array_push($arrayGlobal, $subB);
            }
            $itemArreglo["elementos"] = $subItemArray;

            //echo $atributos["title"];
            //echo "<br/>";
            array_push($arrayElementos, $itemArreglo);
        }

        //return json_encode($arrayElementos);
		return json_encode($arrayGlobal);

    }
}
