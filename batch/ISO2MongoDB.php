<?php

/**
 * ISO2MongoDB.php
 *
 * This file contains the script to write the ISO data to a MongoDB database, usage:
 *
 * <code>
 * php -f ISO2MongoDB.php <database name> <json directory> <po directory>
 * </code>
 *
 * <ul>
 * 	<li><b>server uri</b>: Server connection URI.
 * 	<li><b>database name</b>: Name of database.
 * 	<li><b>json directory</b>: Path to the directory containing the Json files.
 *  <li><b>po directory</b>: Path to the directory containing the PO files; the script
 *		expects that directory to contain a list of directories named <tt>iso_XXX</tt> where
 * 		XXX stands for the standard.
 * </ul>
 *
 * The database will contain the following collections:
 *
 * <ul>
 * 	<li><b>schema</b>: List of schemas, <tt>_id</tt> will be the schema name.
 * 	<li><b>ISO_XXX</b>: Where XXX stands for the standard, will contain the ISO data.
 * </ul>
 *
 * <em>Note that the script will overwrite the above collections in the provided
 * database.</em>
 *
 * @example
 * <code>
 * php -f batch/ISO2MongoDB.php mongodb://localhost:27017 ISO data/JSON data/PO
 * </code>
 */

/**
 * Include local definitions.
 */
require_once(dirname(__DIR__) . "/includes.local.php");

/**
 * Reference classes.
 */
use Milko\utils\ISOCodes;
use Milko\wrapper\MongoDB\Server;

/*=======================================================================================
 *																						*
 *											MAIN										*
 *																						*
 *======================================================================================*/

//
// Check arguments.
//
if( $argc < 5 )
	exit( "Usage: php -f ISO2MongoDB.php <connection> <database name> <json directory> <po directory>\n" );

//
// Get arguments.
//
$c = $argv[ 1 ];
$d = $argv[ 2 ];
$j = $argv[ 3 ];
$p = $argv[ 4 ];

//
// Instantiate class.
//
$iso = new ISOCodes( $j, $p );

//
// Open server and database connections.
//
$server = new Server( $c );
$database = $server->Client( $d, [] );

//
// Reference and drop schema.
//
$col_schema = $database->Client( "schema", [] );
$col_schema->Drop();

//
// Reference and drop standards.
//
$col_standards = [];
foreach( $iso->Standards() as $standard )
{
	$col_standards[ $standard ] = $database->Client( $standard, [] );
	$col_standards[ $standard ]->Drop();
}

//
// Load schemas.
//
$iterator = $iso->getIterator();
foreach( $iterator as $standard => $schema )
{
	//
	// Inform.
	//
	echo( "==> ISO $standard\n" );

	//
	// Write schema.
	//
	$schema[ "_id" ] = $standard;
	$schema[ "schema" ] = $schema[ '$schema' ];
	unset( $schema[ '$schema' ] );
	$col_schema->AddOne( $schema );

	//
	// Write data.
	//
	$iterator = $iso->getIterator( $standard );
	foreach( $iterator as $key => $data )
	{
		$data[ '_id' ] = $key;
		$col_standards[ $standard ]->AddOne( $data );
	}
}

echo( "\nDone!\n" );


?>
