<?php
require_once 'clases/auth.class.php';
require_once 'clases/respuestas.class.php';

$_auth = new auth;
$_respuestas = new respuestas;

if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibir datos
    $postbody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_auth->login($postbody);
    //devolvemos una respuesta
    header('content-type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responsecode = $datosArray["result"]["error_id"];
        http_response_code($responsecode);
    }else{
        http_response_code(200);
    }
    echo json_encode($datosArray);
}else if($_SERVER['REQUEST_METHOD'] == "DELETE"){
    //recibimos los datos enviados 
    $postbody = file_get_contents("php://input");
     //enviamos datos al manejador
    $datosArray = $_auth->deleteToken($postbody);
     //devolvemos una respuesta
    header('content-type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responsecode = $datosArray["result"]["error_id"];
        http_response_code($responsecode);
    }else{
        http_response_code(200);
    }
    echo json_encode($datosArray);
}else{
    header('content-type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}

?>