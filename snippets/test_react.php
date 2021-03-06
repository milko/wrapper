<?php

require_once(dirname(__DIR__) . "/vendor/autoload.php");

$app = function ($request, $response) {
	$response->writeHead(200, array('Content-Type' => 'text/plain'));
	$result = "Method: " . $request->getMethod() . "\n";
	$result .= ("Path: " . $request->getPath() . "\n" );
	$result .= ("Query: " . json_encode($request->getQuery()) . "\n" );
	$result .= ("Headers: " . json_encode($request->getHeaders()) . "\n" );
	$response->end($result);
};

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);
$http = new React\Http\Server($socket, $loop);

$http->on('request', $app);
echo "Server running at http://127.0.0.1:1337\n";

$socket->listen(1337);
$loop->run();

?>