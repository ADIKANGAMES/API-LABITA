<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/sensor_category.class.php';

$_respuestas = new respuestas;
$_sensor_category = new sensor_category;

if($_SERVER['REQUEST_METHOD'] == "GET"){
    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaSensor_category = $_sensor_category->listaSensor_category($pagina);
        header("content-type: applications/json");
        echo json_encode($listaSensor_category);
        http_response_code(200);
    }else if(isset($_GET["id"])){
        $id = $_GET["id"];
        $datosSensor_category = $_sensor_category->obtenerSensor_category($id);
        header("content-type: applications/json");
        echo json_encode($datosSensor_category);
        http_response_code(200);
    }
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibimos los datos enviados 
    $postbody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_sensor_category->post($postbody);
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
    $datosArray = $_sensor_category->put($postbody);
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
    $datosArray = $_sensor_category->delete($postbody);
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