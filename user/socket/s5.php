#!/usr/bin/php -q
<?php
error_reporting(E_ALL);


function strigToBinary($string)
{
    $characters = str_split($string);
    $binary = [];
    foreach ($characters as $character) {
        $data = unpack('H*', $character);
        $binary[] = base_convert($data[1], 16, 2);
    }
    return implode(' ', $binary);
}
 
function binaryToString($binary)
{
    $binaries = explode(' ', $binary);
    $string = null;
    foreach ($binaries as $binary) {
        $string .= pack('H*', dechex(bindec($binary)));
    }
    return $string;
}

// function binToStr($input)
// {
//     if (!is_string($input))
//         return false;
//     return pack('H*', base_convert($input, 2, 16));
// }

function binToStr($input)
{
    if (!is_string($input))
        return false;
    $chunks = str_split($input,8);
    $ret = '';
    foreach ($chunks as $chunk)
    {
        $ret .= chr(bindec($chunk));
    }
    return $ret;
}


/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

$address = '192.168.0.2';
$port = 20480;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
}

do {
    if (($msgsock = socket_accept($sock)) === false) {
        echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
        break;
    }
    /* Send instructions. */
    $msg = "\nWelcome to the PHP Test Server. \n" .
        "To quit, type 'quit'. To shut down the server type 'shutdown'.\n";
    socket_write($msgsock, $msg, strlen($msg));

    do {
        if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
            echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)) . "\n";
            break 2;
        }
        if (!$buf = trim($buf)) {
            continue;
        }
        if ($buf == 'quit') {
            break;
        }
        if ($buf == 'shutdown') {
            socket_close($msgsock);
            break 2;
        }
        $talkback = "PHP: You said '$buf'.\n";
        socket_write($msgsock, $talkback, strlen($talkback));
        // ---------------------------------
        echo "$buf\n";

        $dataSize = unpack( 'V', fread( $buf, 2 ) );
        echo $dataSize;

        // var_dump(unpack('C*', $buf));
        // echo binToStr($buf);
        // echo unpack('C*', $buf);
        echo "\n---------------------------------".date("H:i:s")."\n";
        // ---------------------------------
    } while (true);
    socket_close($msgsock);
} while (true);

socket_close($sock);
?>