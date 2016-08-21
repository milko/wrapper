<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Enable exception logging.
//
triagens\ArangoDb\Exception::enableLogging();

//
// Reference classes.
//
use Milko\wrapper\ArangoDB\Server;

//
// Instantiate server.
//
$server = new Server(
	'tcp://UnitTests:testuser@localhost:8529?createCollection=1'
);

//
// Create and register database.
//
$database = $server->Client( "UnitTests", [] );

//
// Create and register collection.
//
$collection = $database->Client( "UnitCollection", [] );

//exit;

//
// Write to the collection.
//
$collection->SetOne( [ "name" => "test" ] );

//exit;

////
//// Check identities.
////
//echo("\n\n" . '$server === $database->Server()' . "\n");
//var_dump( ($server === $database->Server()) );
//
//echo("\n" . '$database === $server[ "UnitTests" ]' . "\n");
//var_dump( ($database === $server[ "UnitTests" ]) );
//echo("\n" . '$database === $server->Client( "UnitTests" )' . "\n");
//var_dump( ($database === $server->Client( "UnitTests" )) );
//echo("\n" . '$database === $collection->Server()' . "\n");
//var_dump( ($database === $collection->Server()) );
//
//echo("\n" . '$collection === $database[ "UnitCollection" ]' . "\n");
//var_dump( ($collection === $database[ "UnitCollection" ]) );
//echo("\n" . '$collection === $database->Client( "UnitCollection" )' . "\n");
//var_dump( ($collection === $database->Client( "UnitCollection" )) );
//echo("\n" . '$collection === $server[ "UnitTests" ][ "UnitCollection" ]' . "\n");
//var_dump( ($collection === $server[ "UnitTests" ][ "UnitCollection" ]) );

//exit;

//
// Drop database.
//
$database->Drop();

//sleep( 3 );

////
//// Check identities.
////
//echo("\n\n" . '$server === $database->Server()' . "\n");
//var_dump( ($server === $database->Server()) );
//
//echo("\n" . '$database === $server[ "UnitTests" ]' . "\n");
//var_dump( ($database === $server[ "UnitTests" ]) );
//echo("\n" . '$database === $server->Client( "UnitTests" )' . "\n");
//var_dump( ($database === $server->Client( "UnitTests" )) );
//echo("\n" . '$database === $collection->Server()' . "\n");
//var_dump( ($database === $collection->Server()) );
//
//echo("\n" . '$collection === $database[ "UnitCollection" ]' . "\n");
//var_dump( ($collection === $database[ "UnitCollection" ]) );
//echo("\n" . '$collection === $database->Client( "UnitCollection" )' . "\n");
//var_dump( ($collection === $database->Client( "UnitCollection" )) );
//echo("\n" . '$collection === $server[ "UnitTests" ][ "UnitCollection" ]' . "\n");
//var_dump( ($collection === $server[ "UnitTests" ][ "UnitCollection" ]) );

////
//// Show objects.
////
//echo("\nServer:\n");
//print_r( $server );
//echo("\nDatabase:\n");
//print_r( $database );
//echo("\nCollection:\n");
//print_r( $collection );

//exit;

//
// Check collection connection.
//
//var_dump( $collection->Connection() );

//
// Connect database.
//
$database->Connect();

////
//// Check identities.
////
//echo("\n\n" . '$server === $database->Server()' . "\n");
//var_dump( ($server === $database->Server()) );
//
//echo("\n" . '$database === $server[ "UnitTests" ]' . "\n");
//var_dump( ($database === $server[ "UnitTests" ]) );
//echo("\n" . '$database === $server->Client( "UnitTests" )' . "\n");
//var_dump( ($database === $server->Client( "UnitTests" )) );
//echo("\n" . '$database === $collection->Server()' . "\n");
//var_dump( ($database === $collection->Server()) );
//
//echo("\n" . '$collection === $database[ "UnitCollection" ]' . "\n");
//var_dump( ($collection === $database[ "UnitCollection" ]) );
//echo("\n" . '$collection === $database->Client( "UnitCollection" )' . "\n");
//var_dump( ($collection === $database->Client( "UnitCollection" )) );
//echo("\n" . '$collection === $server[ "UnitTests" ][ "UnitCollection" ]' . "\n");
//var_dump( ($collection === $server[ "UnitTests" ][ "UnitCollection" ]) );

//
// Write to the collection.
//
$collection->SetOne( [ "name" => "test" ] );
//

?>