<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/users.class.php';

$_respuestas = new respuestas;
$_users = new users;

if($_SERVER['REQUEST_METHOD'] == "GET"){
    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaUsers = $_users->listaUsers($pagina);
        header("content-type: applications/json");
        echo json_encode($listaUsers);
        http_response_code(200);
    }else if(isset($_GET["id"])){
        $userId = $_GET["id"];
        $datosUsers = $_users->obtenerUsers($userId);
        header("content-type: applications/json");
        echo json_encode($datosUsers);
        http_response_code(200);
    }elseif(isset($_GET['buscar'])){
        $buscar = $_GET['buscar'];
        $usuariosEncontrados = $_users->buscarUsuarios($buscar);
        header("content-type: applications/json");
        echo json_encode($usuariosEncontrados);
        http_response_code(200);
    }
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //recibimos los datos enviados 
    $postbody = file_get_contents("php://input");
    //enviamos los datos al manejador
    $datosArray = $_users->post($postbody);
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
    $postbody = file_get_contents("php://input");
    $datos = json_decode($postbody, true);
    $updatePassword = isset($datos['update_password']) ? $datos['update_password'] : false;
    if ($updatePassword) {
        $datosArray = $_users->putUpdatePassword($postbody);
    } else {
        $datosArray = $_users->put($postbody);
    }
    header('content-type: application/json');
    if (isset($datosArray["result"]["error_id"])) {
        $responsecode = $datosArray["result"]["error_id"];
        http_response_code($responsecode);
    } else {
        http_response_code(200);
    }
    echo json_encode($datosArray);
}else if($_SERVER['REQUEST_METHOD'] == "DELETE"){
    //recibimos los datos enviados 
    $postbody = file_get_contents("php://input");
     //enviamos datos al manejador
    $datosArray = $_users->delete($postbody);
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