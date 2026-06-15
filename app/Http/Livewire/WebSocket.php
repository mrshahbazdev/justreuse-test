<?php

namespace App\Http\Livewire;

use App\Models\TblChat;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocket implements MessageComponentInterface
{

    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        echo 'Server Started';
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        echo 'Server Started';
        $this->clients->attach($conn);


        echo "New connection! ({$conn->resourceId})\n";
        $data['status_type'] = 'Online';
        foreach ($this->clients as $client) {
            $client->send(json_encode($data)); //here we are sending a status-message
        }
        //  $conn->close(); return;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf(
            'Connection %d sending message "%s" to %d other connection%s' . "\n",
            $from->resourceId,
            $msg,
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );
        $data = json_decode($msg, true);
        if ($numRecv == 0) {
            $data['status_type'] = 'Offline';
        } else {
            $data['status_type'] = 'Online';
        }
        foreach ($this->clients as $client) {
            if ($from == $client) {
                $data['from'] = 'Me';
            } else {
                $data['from'] = "others";
            }
            $client->send(json_encode($data));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $data['status_type'] = 'Offline';

        foreach ($this->clients as $client) {
            $client->send(json_encode($data));
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
