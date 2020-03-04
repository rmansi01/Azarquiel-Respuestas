<?php
/**
 * Created by PhpStorm.
 * User: pacopulido
 * Date: 11/3/17
 * Time: 13:35
 */
require "vendor/autoload.php";
require "conexion/Conexion.php";

$app = new \Slim\App();

$conn = Conexion::getPDO();

$app->get('/',function(){
    echo '<h1>Welcome to Api Pregutame with Slim</h1>';
    echo "<table>";
    echo "<tr><td>get </td><td>/users</td><td>lista de usuarios</td></tr>";
    echo "<tr><td>get </td><td>/user/{telefono}</td><td>usuario con ese telefono</td></tr>";
    echo "<tr><td>get </td><td>/preguntas
    </td><td>lista de preguntas
    </td></tr>";
    echo "<tr><td>get </td><td>/preguntas/{pregunta}/comentarios</td><td>lista de comentarios de esa pregunta</td></tr>";
    echo "<tr><td>post</td><td>/user</td><td>add new usuario</td></tr>";
    echo "<tr><td>post</td><td>/pregunta</td><td>add new pregunta</td></tr>";
    echo "<tr><td>post</td><td>/pregunta/{pregunta}/comentario</td><td>add new comentario a ese pregunta</td></tr>";
    echo "<tr><td>post</td><td>/user/{telefono}/avatar</td><td>add/change avatar a ese usuario y a sus comentarios si los hubiese</td></tr>";
    echo "</table>";
});

$app->get('/users', function ($request, $response, $args) use ($conn){
    $ordenSql = "select * from usuario";
    $statement = $conn->prepare($ordenSql);
    $statement->execute();
    $salida = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;
    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(["users"=>$salida]));
});

$app->get('/user/{telefono}', function ($request, $response, $args) use ($conn){
    $telefono = $args['telefono'];
    $ordenSql = "select * from usuario where telefono=:telefono";
    $statement = $conn->prepare($ordenSql);
    $statement->bindParam(':telefono', $telefono, PDO::PARAM_INT);
    $statement->execute();
    $salida = $statement->fetch(PDO::FETCH_ASSOC);
    $statement = null;
    if ($salida != null) {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(["user"=>$salida]));
    } else {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json; charset=UTF8')
            ->write(json_encode(["user" => null], JSON_UNESCAPED_UNICODE));
    }
});

$app->post('/user', function ($request, $response, $args) use ($conn) {
    $telefono = $request->getParam('telefono');
    $nick = $request->getParam('nick');
    if (!isset($telefono) || !isset($nick)) {
        $body = $request->getBody();
        $jsonobj = json_decode($body);
        if ($jsonobj != null) {
            $telefono = $jsonobj->{'telefono'};
            $nick = $jsonobj->{'nick'};
        }
    }
    try {
        if (!isset($telefono) || !isset($nick)) {
            $salida = "No data";
        } else {
            $ordenSql = "insert into usuario(telefono,nick) values(:telefono,:nick)";
            $statement = $conn->prepare($ordenSql); // creación de la sentencia .
            $statement->bindParam(':telefono', $telefono, PDO::PARAM_STR);
            $statement->bindParam(':nick', $nick, PDO::PARAM_STR);
            $conn->beginTransaction();
            $statement->execute();
            $conn->commit();
            $salida = "Ok";
        }
    }catch (PDOException $e) {
        return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(["msg"=>"Violada Primary key..."]));
    } finally {$statement = null;}
    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(["msg"=>$salida]));
});

$app->get('/preguntas
', function ($request, $response, $args) use ($conn){
    $ordenSql = "select * from preguntas";
    $statement = $conn->prepare($ordenSql);
    $statement->execute();
    $salida = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;
    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(["preguntas
        "=>$salida]));
});

$app->post('/preguntas', function ($request, $response, $args) use ($conn) {
    
    $body = $request->getBody();
    $jsonobj = json_decode($body);
    if ($jsonobj != null) {
        $descripcion = $jsonobj->{'descripcion'};
        $telefono = $jsonobj->{'telefono'};
        $nick = $jsonobj->{'nick'};
        $avatar = $jsonobj->{'avatar'};
        $fecha = $jsonobj->{'fecha'};
        
    }
    
    if (!isset($descripcion) || !isset($telefono) || !isset($nick) || !isset($avatar) || !isset($fecha)) {
        $salida = "No data";
    } else {
        $ordenSql = "insert into pregunta(telefono,nick,avatar,_id,fecha,descripcion) values(:telefono,:nick,:avatar,:_id,:fecha,:descripcion)";
        $statement = $conn->prepare($ordenSql);
        $statement->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $statement->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $statement->bindParam(':nick', $nick, PDO::PARAM_STR);
        $statement->bindParam(':avatar', $avatar, PDO::PARAM_STR);
        $statement->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $statement->bindParam(':post', $post, PDO::PARAM_STR);


        $conn->beginTransaction();
        $statement->execute();
        $_id=$conn->lastInsertId();
        $conn->commit();
        $pregunta = ["_id"=>$_id,"telefono"=>$telefono,"nick"=>$nick,"avatar"=>$avatar,"fecha"=>$fecha,"descripcion"=>$descripcion];
    }
    $statement = null;
    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(["pregunta"=>$pregunta]));
});
$app->get('/preguntas/{pregunta}/comentarios', function ($request, $response, $args) use ($conn){
    $_id=$args['pregunta'];

    $ordenSql = "select telefono,nick, avatar, _id,DATE_FORMAT(fecha,'%Y-%m-%d %T') as fecha, post from comentario where _id=:_id order by fecha desc";
    $statement = $conn->prepare($ordenSql);
    $statement->bindParam(':_id', $_id, PDO::PARAM_INT);
    $statement->execute();
    $salida = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;
    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(["comentarios"=>$salida]));
});

$app->post('/preguntas/{pregunta}/comentario', function ($request, $response, $args) use ($conn) {

    $telefono = $request->getParam('telefono');
    $nick = $request->getParam('nick');
    $avatar = $request->getParam('avatar');
    $_id=$args['pregunta'];
    $fecha = $request->getParam('fecha');
    $post = $request->getParam('post');
    if (!isset($telefono) || !isset($nick) || !isset($avatar) || !isset($fecha) || !isset($post)){
        $body = $request->getBody();
        $jsonobj = json_decode($body);
        if ($jsonobj != null) {
            $telefono = $jsonobj->{'telefono'};
            $nick = $jsonobj->{'nick'};
            $avatar = $jsonobj->{'avatar'};
            $fecha = $jsonobj->{'fecha'};
            $post = $jsonobj->{'post'};
        }
    }
    try {
        if (!isset($telefono) || !isset($nick) || !isset($avatar) || !isset($fecha) || !isset($post)){
            $salida = "No data";
        } else {
            $ordenSql = "INSERT INTO comentario(telefono,nick,avatar,_id,fecha,post) values(:telefono,:nick,:avatar,:_id,:fecha,:post)";
            $statement = $conn->prepare($ordenSql);
            $statement->bindParam(':telefono', $telefono, PDO::PARAM_STR);
            $statement->bindParam(':nick', $nick, PDO::PARAM_STR);
            $statement->bindParam(':avatar', $avatar, PDO::PARAM_STR);
            $statement->bindParam(':_id', $_id, PDO::PARAM_INT);
            $statement->bindParam(':fecha', $fecha, PDO::PARAM_STR);
            $statement->bindParam(':post', $post, PDO::PARAM_STR);
            $conn->beginTransaction();
            $statement->execute();
            $conn->commit();
            $comentario = ["telefono"=>$telefono,"nick"=>$nick,"avatar"=>$avatar, "_id"=>$_id,"fecha"=>$fecha,"post"=>$post];
        }
    }catch (PDOException $e) {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(["msg"=>"Violada Primary key..."]));
    } finally {$statement = null;}
    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(["comentario"=>$comentario]));
});
$app->post('/user/{usuario}/avatar', function ($request, $response, $args)  use ($conn) {
    $body = $request->getBody();
    $jsonobj = json_decode($body);
    $id=$args['usuario'];
    //Si nos envian un fichero entrará aqui
    if (!empty($_FILES)) {
        $file = $_FILES["avatar"];
        $target_dir = "resources/avatar/";
        $target_file = $target_dir . $file["name"];
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $tmp_name=$file["tmp_name"];
        $nombrefile = $id."_"."avatar"."_".time().".".$imageFileType;
        $src = $target_dir.$nombrefile;
        if(!move_uploaded_file($tmp_name,$src)){
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(["msg"=>"No se ha podido subir el fichero"]));
        }
        //Si el archivo viene en cadena Base64 entrara aqui
    }else if(isset($jsonobj->{'avatar'})){
        $target_dir = "resources/avatar/";
        $nombrefile = $id."_"."avatar"."_".time().".png";
        $src= $target_dir.$nombrefile;
        if(!file_put_contents($src,base64_decode($jsonobj->{'avatar'}))){
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(["msg"=>"No se ha podido subir el fichero"]));
        }
    }
    else {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(["msg"=>"No se ha detectado ningun File"]));
    }
    $ordenSql = "UPDATE usuario set avatar=:file where telefono=:telefono";
    try {
        $statement = $conn->prepare($ordenSql);
        $statement->bindParam(":file", $nombrefile, PDO::PARAM_STR);
        $statement->bindParam(":telefono", $id, PDO::PARAM_STR);
        $conn->beginTransaction();
        $statement->execute();
        $conn->commit();
        $statement = null;
    } catch (Exception $e){
        echo $e->getMessage();
    }
    try {
        $ordenSql = "UPDATE comentario set avatar=:file where telefono=:telefono";
        $statement = $conn->prepare($ordenSql);
        $statement->bindParam(":file", $nombrefile, PDO::PARAM_STR);
        $statement->bindParam(":telefono", $id, PDO::PARAM_STR);
        $statement->execute();
        $conn->beginTransaction();
        $statement->execute();
        $conn->commit();
        $statement = null;
    } catch (Exception $e){
        echo $e->getMessage();
    }
    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(["msg"=>$nombrefile]));
});
$app->run();
$conn=null;

?>