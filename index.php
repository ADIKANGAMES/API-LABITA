<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API - Pruebas</title>
    <link rel="stylesheet" href="assets/estilo.css" type="text/css">
</head>

<body>

    <div class="container">
        <h1>Api de pruebas</h1>
        <div class="divbody">
            <h3>Auth - login</h3>
            <code>
                POST /auth
                <br>
                {
                <br>
                "email" :"", -> REQUERIDO
                <br>
                "password": "" -> REQUERIDO
                <br>
                }

            </code>
        </div>
        <div class="divbody">
            <h3>Usuarios</h3>
            <code>
                GET /users?page=$numeroPagina
                <br>
                GET /users?id=$userId
            </code>

            <code>
                POST /users
                <br>
                {
                <br>
                "name" : "", -> REQUERIDO
                <br>
                "email" : "", -> REQUERIDO
                <br>
                "password" :"",
                <br>
                "role":"", -> REQUERIDO
                <br>
                "status" : "",
                <br>
                "token" : "" -> REQUERIDO
                <br>
                }

            </code>
            <code>
                PUT /users
                <br>
                {
                <br>
                "name" : "",
                <br>
                "email" : "",
                <br>
                "password":"",
                <br>
                "role" :"",
                <br>
                "status" : "",
                <br>
                "token" : "" , -> REQUERIDO
                <br>
                "userId" : "" -> REQUERIDO
                <br>
                }

            </code>
            <code>
                DELETE /users
                <br>
                {
                <br>
                "token" : "", -> REQUERIDO
                <br>
                "userId" : "" -> REQUERIDO
                <br>
                }

            </code>
        </div>


    </div>

</body>

</html>