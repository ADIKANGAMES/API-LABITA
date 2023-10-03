<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class auth extends conexion{
    private $userid = "";
    public function login($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        if(!isset($datos['email']) || !isset($datos['password'])){
            //error con los campos
            return $_respuestas->error_400();
        }else{
            //todo esta bien
            $usuario = $datos['email'];
            $password = $datos['password'];
            $password = parent::encriptar($password);
            $datos = $this->obtenerDatosUsuario($usuario);
            if($datos){
                //verificar si la contraseña es igual
                if($password == $datos[0]['password']){
                    if($datos[0]['status'] == "true"){
                        //crear el token
                        $verificar = $this->insertarToken($datos[0]['id']);
                        if($verificar){
                            //si se guardo
                            $result = $_respuestas->response;
                            $result ["result"] = array(
                                "token" => $verificar,
                                "user" => $datos
                            );
                            return $result;
                        }else{
                            //error al guardar
                            return $_respuestas->error_500("Error interno, No hemos podido guardar");
                        }
                    }else{
                        //el usuario esta inactivo
                        return $_respuestas->error_200("El usuario esta inactivo");
                    }
                }else{
                    //la contraseña no igual
                    return $_respuestas->error_200("El password es invalido");
                }
            }else{
                //no existe el usuario
                return $_respuestas->error_200("El usuario $usuario no existe");
            }

        }
    }

    public function deleteToken($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);
        if(!isset($datos['userId'])){
            return $_respuestas->error_400();
        } else {
            $this->userid = $datos["userId"];
            $resp = $this->eliminarToken(); // Corrige el nombre del método aquí
            if($resp){
                $respuesta = $_respuestas->response;
                $respuesta["result"] = array(
                    "userId" => $this->userid
                );
                return $respuesta;
            } else {
                return $_respuestas->error_500();
            }
        }
    }


    private function eliminarToken(){
        $query = "DELETE FROM users_token WHERE userId = '".$this->userid."'";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }

    private function obtenerDatosUsuario($email){
        $query = "SELECT id,name,password,role,status FROM muser WHERE email = '$email'";
        $datos = parent::obtenerDatos($query);
        if(isset($datos[0]['id'])){
            return $datos;
        }else{
            return 0;
        }
    }

    private function insertarToken($userId){
        $val = true;
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
        $date = date("Y-m-d H:i");
        $estado = "Activo";
        $query = "INSERT INTO users_token (userId, token, status, date) VALUES ('$userId', '$token', '$estado', '$date')";
        $verifica = parent::nonQuery($query);
        if($verifica){
            return $token;
        }else{
            return 0;
        }
    }

}
