<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/programming_ventilation.class.php';

$_respuestas = new respuestas;
$_programming_ventilation = new programming_ventilation;

if($_SERVER['REQUEST_METHOD'] == "GET"){
    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaProgramming_ventilation = $_programming_ventilation->listaProgramming_ventilation($pagina);
        header("content-type: applications/json");
        echo json_encode($listaProgramming_ventilation);
        http_response_code(200);
    }else if(isset($_GET["id"])){
        $id = $_GET["id"];
        $datosProgramming_ventilation = $_programming_ventilation->obtenerProgramming_ventilation($id);
        header("content-type: applications/json");
        echo json_encode($datosProgramming_ventilation);
        http_response_code(200);
    }
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibimos los datos enviados 
    $postbody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_programming_ventilation->post($postbody);
    //devolvemos una respuesta
    header('content-type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responsecode = $datosArray["result"]["error_id"];
        http_response_code($responsecode);
    }else{
        http_response_code(200);
    }
    echo json_encode($datosArray);
}else if($_SERVER['REQUEST_METHOD'] == "PUT"){
    //recibimos los datos enviados 
    $postbody = file_get_contents("php://input");
    //enviamos datos al manejador
    $datosArray = $_programming_ventilation->put($postbody);
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
    $datosArray = $_programming_ventilation->delete($postbody);
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