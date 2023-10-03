<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class graph extends conexion{

    private $table = "mgraph";
    private $id = "";
    private $id_sensor = "";
    private $time = "";
    private $value = "";
    private $token = "";
    
    public function listaGraph($pagina = 1){
        $inicio = 0;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id_sensor,time,value FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function obtenerGraph($id){
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
                if(!isset($datos['id_sensor']) || !isset($datos['time']) || !isset($datos['value'])){
                    return $_respuestas->error_400();
                }else{
                    $this->id_sensor = $datos["id_sensor"];
                    $this->time = $datos["time"];
                    $this->value = $datos["value"];
                    $resp = $this->insertarGraph();
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

    public function insertarGraph(){
        $query = "INSERT INTO " . $this->table . " (id_sensor,time,value) VALUES ('".$this->id_sensor."','".$this->time."','".$this->value."')";
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
                    if(isset($datos['id_sensor'])){ $this->id_sensor = $datos["id_sensor"]; }
                    if(isset($datos['time'])){ $this->time = $datos["time"]; }
                    if(isset($datos['value'])){ $this->value = $datos["value"]; }
                    $resp = $this->modificarGraph();
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

    public function modificarGraph(){
        $query = "UPDATE " . $this->table . " SET id_sensor = '".$this->id_sensor."', time = '".$this->time."', value = '".$this->value."'  WHERE id = '".$this->id."'";
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
                    $resp = $this->eliminarGraph();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id_sensor" => $this->id_sensor
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

    private function eliminarGraph(){
        $query = "DELETE FROM " . $this->table . " WHERE id_sensor = '".$this->id_sensor."'";
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