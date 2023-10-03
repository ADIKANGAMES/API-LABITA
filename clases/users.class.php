<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class users extends conexion{

    private $table = "muser";
    private $userid = "";
    private $name = "";
    private $email = "";
    private $password = "";
    private $role = "";
    private $status = "";
    private $token = "";
    
    public function listaUsers($pagina = 1){
        $inicio = 0;
        $cantidad = 100;
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $query = "SELECT id,name,email FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function obtenerUsers($id){
        $query = "SELECT * FROM " . $this->table ." WHERE id = '$id'";
        return parent::obtenerDatos($query);
    }

    public function post($json) {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);
    
        if (!isset($datos['token'])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                if (!isset($datos['name']) || !isset($datos['email']) || !isset($datos['role'])) {
                    return $_respuestas->error_400();
                } else {
                    $this->name = $datos["name"];
                    $this->email = $datos["email"];
    
                    // Verificar si el email ya existe en la base de datos
                    if ($this->emailExistente($this->email)) {
                        return $_respuestas->error_401("El email ya existe en la base de datos.");
                    }
    
                    if (isset($datos['password'])) {
                        $this->password = $datos["password"];
                    }
                    $this->role = $datos["role"];
                    if (isset($datos['status'])) {
                        $this->status = $datos["status"];
                    }
                    $resp = $this->insertarUsers();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "userId" => $resp
                        );
                        return $respuesta;
                    } else {
                        return $_respuestas->error_500();
                    }
                }
            } else {
                return $_respuestas->error_401("El token que envió es inválido o ha caducado");
            }
        }
    }
    
    public function insertarUsers(){
        $query = "INSERT INTO " . $this->table . " (name,email,password,role,status) VALUES ('".$this->name."','".$this->email."',SHA2('".$this->password."',256),'".$this->role."','".$this->status."')";
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
                    $this->userid = $datos["id"];
                    if(isset($datos['name'])){ $this->name = $datos["name"]; }
                    if(isset($datos['email'])){ 
                        // Verificar si el nuevo correo electrónico ya existe en la base de datos
                        if ($this->emailExistente($datos['email'], $this->userid)) {
                            return $_respuestas->error_401("El nuevo correo electrónico ya existe en la base de datos.");
                        }
                        $this->email = $datos["email"]; 
                    }
                    if(isset($datos['password'])){ $this->password = $datos["password"]; }
                    if(isset($datos['role'])){ $this->role = $datos["role"]; }
                    if(isset($datos['status'])){ $this->status = $datos["status"]; }
                    $resp = $this->modificarUsers();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "userId" => $this->userid
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }
            }else{
                return $_respuestas->error_401("El token que envió es inválido o ha caducado");
            }
        }
    }
    
    public function modificarUsers(){
        $query = "UPDATE " . $this->table . " SET name = '".$this->name."', email = '".$this->email."', password = SHA2('".$this->password."',256), role = '".$this->role."', status = '".$this->status."' WHERE id = '".$this->userid."'";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }

    public function emailExistente($email, $excludeUserId = null) {
        $query = "SELECT COUNT(*) AS count FROM " . $this->table . " WHERE email = '" . $email . "'";
        if ($excludeUserId) {
            $query .= " AND id != '$excludeUserId'";
        }
        $resultado = parent::obtenerDatos($query);
    
        if ($resultado && isset($resultado[0]['count']) && $resultado[0]['count'] > 0) {
            return true;
        }
    
        return false;
    }

    public function putUpdatePassword($json) {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);
    
        if (!isset($datos['token'])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                if (!isset($datos['id']) || !isset($datos['before_password']) || !isset($datos['new_password'])) {
                    return $_respuestas->error_400();
                } else {
                    $this->userid = $datos["id"];
                    $this->password = $datos["before_password"];
                    $newPassword = $datos["new_password"];
    
                    if ($this->password === $newPassword) {
                        return $_respuestas->error_401("La nueva contraseña debe ser diferente de la contraseña anterior.");
                    }
                    
                    $hashedBeforePassword = hash('sha256', $this->password);
                    $query = "SELECT id FROM " . $this->table . " WHERE id = '" . $this->userid . "' AND password = '" . $hashedBeforePassword . "'";
                    $user = parent::nonQuery($query);
                    
                    if (!$user) {
                        return $_respuestas->error_401("La contraseña anterior no coincide.");
                    }
                    
                    $hashedNewPassword = hash('sha256', $newPassword);
                    $query = "UPDATE " . $this->table . " SET password = '" . $hashedNewPassword . "' WHERE id = '" . $this->userid . "'";
                    $resp = parent::nonQuery($query);
    
                    if ($resp >= 1) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "userId" => $this->userid
                        );
                        return $respuesta;
                    } else {
                        return $_respuestas->error_500();
                    }
                }
            } else {
                return $_respuestas->error_401("El token que envió es inválido o ha caducado");
            }
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
                    $this->userid = $datos["id"];
                    $resp = $this->eliminarUsers();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "userId" => $this->userid
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

    private function eliminarUsers(){
        $query = "DELETE FROM " . $this->table . " WHERE id = '".$this->userid."'";
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

    public function buscarUsuarios($buscar) {
        $query = "SELECT * FROM " . $this->table . " WHERE name LIKE '%" . $buscar . "%' OR email LIKE '%" . $buscar . "%'";
        $resultado = parent::obtenerDatos($query);
        return $resultado;
    }
}
?>