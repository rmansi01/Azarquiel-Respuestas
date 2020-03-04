<?php
/**
 * Created by PhpStorm.
 * User: pacopulido
 * Date: 11/3/17
 * Time: 13:35
 */
require 'vendor/autoload.php';

$app = new \Slim\App();

$app->get('/',function() {
    echo 'Welcome to ApiPaco with Slim';
});

// Uri: http://localhost/foroslim/hola/paco?edad=21
// output: {"nombre":"paco","edad":"21"}
$app->get('/hola/{nombre}', function ($request, $response, $args){
    $nombre = $args['nombre'];
    $edad = $request->getParam('edad');
    //echo 'Hola '.$nombre.' tienes '.$edad.' años';

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(["nombre"=>$nombre,"edad"=>$edad]));
});
$app->run();
?>