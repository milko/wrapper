<?php

/**
 * Include local definitions.
 */
require_once(dirname(__DIR__) . "/includes.local.php");

/**
 * Reference classes.
 */
use Milko\wrapper\MongoDB\Server;

//
// Get server.
//
echo( "Server:" );
$connection = Server::NewConnection( "mongodb://localhost:27017" );
var_dump( get_class( $connection ) );

//
// Get database.
//
echo( "Database:" );
$connection = Server::NewConnection( "mongodb://localhost:27017/UnitTests" );
var_dump( get_class( $connection ) );

//
// Get collection.
//
echo( "Database:" );
$connection = Server::NewConnection( "mongodb://localhost:27017/UnitTests/Collection" );
var_dump( get_class( $connection ) );

?>
