<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class sensor extends conexion{

    private $table = "msensor";
    private $id = "";
    private $name = "";
    private $description = "";
    private $id_sensor_type = "";
    private $id_zone = "";
    private $token = "";
    
    public function listaSensor($pagina = 1){
        $inicio = 0;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id,name,description,id_sensor_type,id_zone FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function obtenerSensor($id){
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
                if(!isset($datos['name']) || !isset($datos['description'])){
                    return $_respuestas->error_400();
                }else{
                    $this->name = $datos["name"];
                    $this->description = $datos["description"];
                    if(isset($datos['id_sensor_type'])){ $this->id_sensor_type = $datos["id_sensor_type"]; }
                    if(isset($datos['id_zone'])){ $this->id_zone = $datos["id_zone"]; }
                    $resp = $this->insertarSensor();
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

    public function insertarSensor(){
        $query = "INSERT INTO " . $this->table . " (name,description,id_sensor_type,id_zone) VALUES ('".$this->name."','".$this->description."','".$this->id_sensor_type."','".$this->id_zone."')";
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
                    if(isset($datos['name'])){ $this->name = $datos["name"]; }
                    if(isset($datos['description'])){ $this->description = $datos["description"]; }
                    if(isset($datos['id_sensor_type'])){ $this->id_sensor_type = $datos["id_sensor_type"]; }
                    if(isset($datos['id_zone'])){ $this->id_zone = $datos["id_zone"]; }
                    $resp = $this->modificarSensor();
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

    public function modificarSensor(){
        $query = "UPDATE " . $this->table . " SET name = '".$this->name."', description = '".$this->description."', id_sensor_type = '".$this->id_sensor_type."', id_zone = '".$this->id_zone."' WHERE id = '".$this->id."'";
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
                    $resp = $this->eliminarSensor();
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

    private function eliminarSensor(){
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