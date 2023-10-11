<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

class ventilation extends conexion
{

    private $table = "mventilation";
    private $id = "";
    private $date_init = "";
    private $date_end = "";
    private $hour_init = "";
    private $hour_end = "";
    private $sensors = "";
    private $token = "";

    public function listaVentilation($pagina = 1)
    {
        $inicio = 0;
        $cantidad = 100;
        if ($pagina > 1) {
            $inicio = ($cantidad * ($pagina - 1)) + 1;
            $cantidad = $cantidad * $pagina;
        }
        $fecha_actual = date("Y-m-d");



        $query = "SELECT id,date_init,date_end,hour_init,hour_end,sensors FROM " . $this->table . ";";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }

    public function obtenerVentilation($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = '$id'";
        return parent::obtenerDatos($query);
    }

    public function post($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);
        if (!isset($datos['token'])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                if (!isset($datos['date_init']) || !isset($datos['date_end']) || !isset($datos['hour_init']) || !isset($datos['hour_end']) || !isset($datos['sensors'])) {
                    return $_respuestas->error_400();
                } else {
                    $this->date_init = $datos["date_init"];
                    $this->date_end = $datos["date_end"];
                    $this->hour_init = $datos["hour_init"];
                    $this->hour_end = $datos["hour_end"];
                    $this->sensors = $datos["sensors"];
                    $resp = $this->insertarVentilation();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $resp
                        );
                        return $respuesta;
                    } else {
                        return $_respuestas->error_500();
                    }
                }
            } else {
                return $_respuestas->error_401("El token que envio es invalido o ha caducado");
            }
        }
    }

    public function insertarVentilation()
    {
        $query = "INSERT INTO " . $this->table . " (date_init,date_end,hour_init,hour_end,sensors) 
        VALUES ('" . $this->date_init . "','" . $this->date_end . "','" . $this->hour_init . "','" . $this->hour_end . "','" . $this->sensors . "');";
        $resp = parent::nonQueryId($query);
        return $resp;
        if ($resp) {
            return $resp;
        } else {
            return 0;
        }
    }

    public function put($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);
        if (!isset($datos['token'])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                if (!isset($datos['id'])) {
                    return $_respuestas->error_400();
                } else {
                    $this->id = $datos["id"];
                    if (isset($datos['date_init'])) {
                        $this->date_init = $datos["date_init"];
                    }
                    if (isset($datos['date_end'])) {
                        $this->date_end = $datos["date_end"];
                    }
                    if (isset($datos['hour_init'])) {
                        $this->hour_init = $datos["hour_init"];
                    }
                    if (isset($datos['hour_end'])) {
                        $this->hour_end = $datos["hour_end"];
                    }
                    if (isset($datos['sensors'])) {
                        $this->sensors = $datos["sensors"];
                    }
                    $resp = $this->modificarVentilation();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $this->id
                        );
                        return $respuesta;
                    } else {
                        return $_respuestas->error_500();
                    }
                }
            } else {
                return $_respuestas->error_401("El token que envio es invalido o ha caducado");
            }
        }
    }

    public function modificarVentilation()
    {
        $query = "UPDATE " . $this->table . " SET date_init = '" . $this->date_init . "', date_end = '" . $this->date_end . "', hour_init = '" . $this->hour_init . "', hour_end = '" . $this->hour_end . "', sensors = '" . $this->sensors . "' WHERE id = '" . $this->id . "';";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }

    public function delete($json)
    {
        $_respuestas = new respuestas;
        $datos = json_decode($json, true);
        if (!isset($datos['token'])) {
            return $_respuestas->error_401();
        } else {
            $this->token = $datos['token'];
            $arrayToken = $this->buscarToken();
            if ($arrayToken) {
                if (!isset($datos['id'])) {
                    return $_respuestas->error_400();
                } else {
                    $this->id = $datos["id"];
                    $resp = $this->eliminarVentilation();
                    if ($resp) {
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $this->id
                        );
                        return $respuesta;
                    } else {
                        return $_respuestas->error_500();
                    }
                }
            } else {
                return $_respuestas->error_401("El token que envio es invalido o ha caducado");
            }
        }
    }

    private function eliminarVentilation()
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = '" . $this->id . "'";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }

    private function buscarToken()
    {
        $query = "SELECT tokenId,userId,status FROM users_token WHERE token = '" . $this->token . "' AND status = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if ($resp) {
            return $resp;
        } else {
            return 0;
        }
    }

    private function actualizarToken($tokenId)
    {
        $date = date("Y-m-d H:i");
        $query = "UPDATE users_token SET date = '$date' WHERE tokenId = '$tokenId'";
        $resp = parent::nonQuery($query);
        if ($resp >= 1) {
            return $resp;
        } else {
            return 0;
        }
    }

    public function buscarVentilation($buscar) {
        $query = "SELECT * FROM " . $this->table . " WHERE date_init LIKE '%" . $buscar . "%' OR date_end LIKE '%" . $buscar . "%'";
        $resultado = parent::obtenerDatos($query);
        return $resultado;
    }
}
?>