<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference class.
//
use Milko\wrapper\ArangoDB\Server;

//
// Instantiate server.
//
$server = new Server( 'tcp://milko:xC%yUmGwaXbE3$Z8@127.0.0.1:8529' );
//$database = $server->Client( "UnitTest_Tests", [] );
//$collection = $database->Client( "Collection_Tests", [] );
//
//
// Connect collection.
//
$server->Connect();

//// Insert record to create collection.
//$collection->Connection()->insertOne( [ "name" => "test" ] );

// Get databases list.
$result = $server->Clients();
echo( "Databases: " ); print_r( $result );

//// Get collections list.
//$result = $database->Clients();
//echo( "Collections: " ); print_r( $result );

?>