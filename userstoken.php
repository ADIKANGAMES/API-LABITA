<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/userstoken.class.php';

$_respuestas = new respuestas;
$_userstoken = new userstoken;

if($_SERVER['REQUEST_METHOD'] == "GET"){
    if(isset($_GET["page"])){
        $pagina = $_GET["page"];
        $listaUsers = $_userstoken->listaUserstoken($pagina);
        header("content-type: applications/json");
        echo json_encode($listaUsers);
        http_response_code(200);
    }else if(isset($_GET["id"])){
        $userId = $_GET["id"];
        $datosUsers = $_userstoken->obtenerUserstoken($userId);
        header("content-type: applications/json");
        echo json_encode($datosUsers);
        http_response_code(200);
    }
}else{
    header('content-type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}
?>