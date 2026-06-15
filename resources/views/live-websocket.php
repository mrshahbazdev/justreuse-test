<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;

    require base_path() . '/vendor/autoload.php';

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Chat()
            )
        ),
        8090
    );

    $server->run();