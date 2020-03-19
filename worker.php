<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('task_queue', false, true, false, false); // creating the channel and the connection again. This is so that if one is ran before the other, the connection is always made

echo " [*] Waiting for messages. To exit press CTRL+C\n"; // When the receiver is started, it will display this message and keep running

$callback = function ($msg) { // Creating a callable function, which displays the message that was consumed/received
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']); // Sending an ack(acknowledgement) along with the message to ensure that no messages that are being processed are lost, and if it fails, it will retry another time
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('task_queue', '', false, false, false, false, $callback); // creating a consumer channel from which this receiver will get messages from

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();