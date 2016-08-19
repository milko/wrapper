<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Include test classes.
//
require_once(dirname(__DIR__) . "/tests/TestClientServerClass.php");
require_once(dirname(__DIR__) . "/tests/TestClientClass.php");

//
// Reference classes.
//
use Milko\wrapper\ClientServer;
use Milko\wrapper\Client;

//
// Instantiate server.
//
$server = new test_ClientServer(
	'protocol://user:password@host:80?key=val#frag'
);

//
// Create and register client.
//
$client = $server->Client( "Directory", [] );

//
// Create empty client.
//
$client = new test_Client();
print_r($client);

?>