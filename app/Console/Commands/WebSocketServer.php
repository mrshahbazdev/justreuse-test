<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\SecureServer;
use React\Socket\Server;
use App\Http\Livewire\WebSocket;
use App\Models\TblChat;

class WebSocketServer extends Command {

   
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*$loop   = Factory::create();
		$webSock = new Server('justreused.com:8444', $loop);
     
     $webSock = new SecureServer(
            new Server($webSock, $loop),//8090--local, 8443&8888 --online
            $loop,
            array(
                'local_cert'        => '/etc/letsencrypt/live/justreused.com/fullchain.pem', // path to your cert
                'local_pk'          => '/etc/letsencrypt/live/justreused.com/privkey.pem', // path to your server private key
                'allow_self_signed' => FALSE, // Allow self signed certs (should be false in production)
                'verify_peer' => FALSE
            )
        );*/

      
      

        /*$webSock = new SecureServer(
            new Server('0.0.0.0:8090', $loop),
            $loop,
            array(
                'local_cert'        => '/opt/ssl/chamberstock_com.crt', // path to your cert
                'local_pk'          => '/opt/ssl/server.key', // path to your server private key
                'allow_self_signed' => TRUE, // Allow self signed certs (should be false in production)
                'verify_peer' => FALSE
            )
        );*/

        // Ratchet magic
        /*$webServer = new IoServer(
            new HttpServer(
                new WsServer(
                    new WebSocket()
                )
            ),
            $webSock
        );

        $loop->run();*/
      
       $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                new WebSocket()
                )
            ),
            6001
            
        );

        $server->run();
      
    }

}
