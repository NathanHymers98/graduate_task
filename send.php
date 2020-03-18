<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest'); // making the AMQP Stream connection
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false); // making the channel/queue where the messages will be sent/received

$msg = new AMQPMessage('Hello World!'); // The message to send
$channel->basic_publish($msg, '', 'hello'); // publishing the message to the receiver

echo " [x] Sent 'Hello World!'\n"; // The message that appears in the console after it has sent

$channel->close(); // Closing both the channel and the connection after the message has been sent
$connection->close();