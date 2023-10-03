<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/graph.class.php';

$_respuestas = new respuestas;
$_graph = new graph;

if($_SERVER['REQUEST_METHOD'] == "GET"){
    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaGraph = $_graph->listaGraph($pagina);
        header("content-type: applications/json");
        echo json_encode($listaGraph);
        http_response_code(200);
    }else if(isset($_GET["id"])){
        $id = $_GET["id"];
        $datosGraph = $_graph->obtenerGraph($id);
        header("content-type: applications/json");
        echo json_encode($datosGraph);
        http_response_code(200);
    }
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibimos los datos enviados 
    $postbody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_graph->post($postbody);
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
    $datosArray = $_graph->put($postbody);
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
    $datosArray = $_graph->delete($postbody);
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