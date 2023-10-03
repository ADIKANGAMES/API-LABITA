<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class userstoken extends conexion{

    private $table = "users_token";

    public function listaUserstoken($pagina = 1){
        $inicio = 0;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT tokenId,userId,token FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function obtenerUserstoken($id){
        $query = "SELECT * FROM " . $this->table ." WHERE userId = '$id'";
        return parent::obtenerDatos($query);
    }
}
?>