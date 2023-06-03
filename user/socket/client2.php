<?php
error_reporting(E_ALL);
set_time_limit(0);
ignore_user_abort(true);

$address = '211.254.156.189'; // example server address
$port = 10000; // example port

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($sock, $address, $port);
socket_listen($sock);sock

$clients = array($);

while(true) {
    $read = $clients;

    if (socket_select($read, $write = NULL, $except = NULL, 0) < 1) {
        continue;
    }
    if (in_array($sock, $read)) {
         $clients[] = $newsock = socket_accept($sock);

         $key = array_search($sock, $read);
         unset($read[$key]);
    }

    foreach ($read as $read_sock) {
        $data = @socket_read($read_sock, 1024);

         if ($data === false) {
            // remove client for $clients array
            $key = array_search($read_sock, $clients);
            unset($clients[$key]);
            echo "client disconnected.\n";
            // continue to the next client to read from, if any
            continue;
        }

        $data = trim($data);

        // check if there is any data after trimming off the spaces
        if (!empty($data)) {
            // send this to all the clients in the $clients array (except the first one, which is a listening socket)
            foreach ($clients as $send_sock) {
                // if its the listening sock or the client that we got the message from, go to the next one in the list
                if ($send_sock == $sock || $send_sock == $read_sock) {
                    continue;
                }

                $fp = fopen('socket_communication.txt', 'a');
                fwrite($fp, date("Y-m-d H:i:s")." - DATA ".$data."\n");
                fclose($fp);

                $value = unpack('H*', "1");
                $response = base_convert($value[1], 16, 2);

                socket_write($send_sock, $response, 1);

            } // end of broadcast foreach
        }
    }
}

echo "Closing sockets...";
socket_close($sock);
?>