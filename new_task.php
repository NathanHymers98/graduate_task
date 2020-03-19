<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest'); // making the AMQP Stream connection
$channel = $connection->channel();

$channel->queue_declare('task_queue', false, true, false, false); // making the channel/queue where the messages will be sent/received

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = "Hello World!";
}
$msg = new AMQPMessage( // The message to send
    $data,
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
);

$channel->basic_publish($msg, '', 'task_queue'); // publishing the message to the receiver

echo ' [x] Sent ', $data, "\n"; // The message that appears in the console after it has sent

$channel->close(); // Closing both the channel and the connection after the message has been sent
$connection->close();