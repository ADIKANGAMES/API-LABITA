<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class programming_ventilation extends conexion{

    private $table = "mprogramming_ventilation";
    private $id = "";
    private $temperature = "";
    private $token = "";
    
    public function listaProgramming_ventilation($pagina = 1){
        $inicio = 0;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id,temperature FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function obtenerProgramming_ventilation($id){
        $query = "SELECT * FROM " . $this->table ." WHERE id = '$id'";
        return parent::obtenerDatos($query);
    }

    public function post($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['temperature'])){
                    return $_respuestas->error_400();
                }else{
                    $this->temperature = $datos["temperature"];
                    $resp = $this->insertarProgramming_ventilation();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $resp
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }
            }else{
                return $_respuestas->error_401("El token que envio es invalido o ha caducado");
            }
        }
    }

    public function insertarProgramming_ventilation(){
        $query = "INSERT INTO " . $this->table . " (temperature) VALUES ('".$this->temperature."')";
        $resp = parent::nonQueryId($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }
    
    public function put($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['id'])){
                    return $_respuestas->error_400();
                }else{
                    $this->id = $datos["id"];
                    if(isset($datos['temperature'])){ $this->temperature = $datos["temperature"]; }
                    $resp = $this->modificarProgramming_ventilation();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $this->id
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }
            }else{
                return $_respuestas->error_401("El token que envio es invalido o ha caducado");
            }
        }
    }

    public function modificarProgramming_ventilation(){
        $query = "UPDATE " . $this->table . " SET temperature = '".$this->temperature."' WHERE id = '".$this->id."'";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }

    public function delete($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if($arrayToken){
                if(!isset($datos['id'])){
                    return $_respuestas->error_400();
                }else{
                    $this->id = $datos["id"];
                    $resp = $this->eliminarProgramming_ventilation();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $this->id
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }
            }else{
                return $_respuestas->error_401("El token que envio es invalido o ha caducado");
            }
        }
    }

    private function eliminarProgramming_ventilation(){
        $query = "DELETE FROM " . $this->table . " WHERE id = '".$this->id."'";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }

    private function buscarToken(){
        $query = "SELECT tokenId,userId,status FROM users_token WHERE token = '".$this->token."' AND status = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }

    private function actualizarToken($tokenId){
        $date = date("Y-m-d H:i");
        $query = "UPDATE users_token SET date = '$date' WHERE tokenId = '$tokenId'";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }
}
?>