<?php

namespace App\Http\Controllers;
use PDO;

class db
{
    public function conexionBase()
    {
            //$conn = mysqli_connect(env('DB_HOST').':'.env('DB_PORT'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
            $conn = mysqli_connect('localhost:3308', 'root', '', 'partehome');


            if(!isset($conn)){
                echo "Error al conectarse a la base";
                return 0;
            }

            return $conn;
    }

    public function conexionBaseWordpress()
    {
            //$conn = mysqli_connect(env('DB_HOST').':'.env('DB_PORT'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'));
            //$pdo = new PDO('mysql:host=local.ec;dbname=wpdb;charset=utf8', 'wpViamatica', 'viamatica2020');
            $conn = mysqli_connect('local.ec:3306', 'wpViamatica', 'viamatica2020', 'wpdb');

            if (!$conn) {
                return json_encode ("Error: No se pudo conectar a MySQL." . PHP_EOL);
                return json_encode ("errno de depuración: " . mysqli_connect_errno() . PHP_EOL);
                return json_encode ("error de depuración: " . mysqli_connect_error() . PHP_EOL);
                exit;
            }

            return $conn;
    }
}
