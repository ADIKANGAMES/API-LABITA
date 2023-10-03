<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class sensor_category extends conexion{

    private $table = "csensor_category";
    private $id = "";
    private $name = "";
    private $description = "";
    private $token = "";
    
    public function listaSensor_category($pagina = 1){
        $inicio = 0;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id,name,description FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function obtenerSensor_category($id){
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
                    $resp = $this->insertarSensor_category();
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

    public function insertarSensor_category(){
        $query = "INSERT INTO " . $this->table . " (name,description) VALUES ('".$this->name."','".$this->description."')";
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
                    $resp = $this->modificarSensor_category();
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

    public function modificarSensor_category(){
        $query = "UPDATE " . $this->table . " SET name = '".$this->name."', description = '".$this->description."'  WHERE id = '".$this->id."'";
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
                    $resp = $this->eliminarSensor_category();
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

    private function eliminarSensor_category(){
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