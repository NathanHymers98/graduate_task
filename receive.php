<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest'); // just like in send, creating the connection
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false); // and creating the channel. This is so that if one is ran before the other, the connection is always made

echo " [*] Waiting for messages. To exit press CTRL+C\n"; // When the receiver is started, it will display this message and keep running

$callback = function ($msg) { // Creating a callable function, which displays the message that was consumed/received
    echo ' [x] Received ', $msg->body, "\n";
};

$channel->basic_consume('hello', '', false, true, false, false, $callback); // Creating the consumer channel from which this will receive messages from

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();