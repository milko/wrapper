<?php

/**
 * ISO2Database.php
 *
 * This file contains the script to write the ISO data to a database, usage:
 *
 * <code>
 * php -f ISO2Database.php <connection URI> <json directory> <po directory>
 * </code>
 *
 * <ul>
 * 	<li><b>connection URI</b>: Connection URI.
 * 	<li><b>json directory</b>: Path to the directory containing the Json files. In the
 * 		debian {@link https://pkg-isocodes.alioth.debian.org} repository it is the data
 * 		directory.
 *  <li><b>po directory</b>: Path to the directory containing the PO files; the script
 *		expects that directory to contain a list of directories named <tt>iso_XXX</tt> where
 *      XXX stands for the standard. In the debian
 * 		{@link https://pkg-isocodes.alioth.debian.org} repository it is the root folder.
 * </ul>
 *
 * The database will contain the following collections:
 *
 * <ul>
 * 	<li><b>schema</b>: List of schemas, <tt>_id</tt> will be the schema name.
 * 	<li><b>ISO_XXX</b>: Where XXX stands for the standard, will contain the ISO data.
 * </ul>
 *
 * To write to a MongoDB database provide an URI in the form <tt>mongodb://...</tt>, any
 * other protocol will write to an ArangoDB database.
 *
 * <em>Note that the script will erase the above collections in the provided database.</em>
 *
 * @example
 * <code>
 * // Load the ISO ArangoDB database.
 * php -f "batch/ISO2Database.php" "tcp://localhost:8529/ISO" "data/JSON" "data/PO"
 *
 * // Load the ISO MongoDB database.
 * php -f "batch/ISO2Database.php" "mongodb://localhost:27017/ISO" "data/JSON" "data/PO"
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

/*=======================================================================================
 *																						*
 *											MAIN										*
 *																						*
 *======================================================================================*/

//
// Check arguments.
//
if( $argc < 4 )
	exit( "Usage: php -f ISO2Database.php <connection URI> <json directory> <po directory>\n" );

//
// Get arguments.
//
$c = $argv[ 1 ];
$j = $argv[ 2 ];
$p = $argv[ 3 ];

//
// Instantiate class.
//
$iso = new ISOCodes( $j, $p );

//
// Open database connection.
//
if( substr( $c, 0, 7 ) == "mongodb" )
{
	$database = \Milko\wrapper\MongoDB\Server::NewConnection( $c );
	if( get_class( $database ) != "Milko\\wrapper\\MongoDB\\Database" )
		exit( "Invalid connection string: expecting a database reference.\n" );
}
else
{
	$database = \Milko\wrapper\ArangoDB\Server::NewConnection( $c );
	if( get_class( $database ) != "Milko\\wrapper\\ArangoDB\\Database" )
		exit( "Invalid connection string: expecting a database reference.\n" );
}

//
// Save code fields.
//
$codes = [
	ISOCodes::k639_2 => [ "alpha_2", "alpha_3" ],
	ISOCodes::k639_3 => [ "alpha_2", "alpha_3" ],
	ISOCodes::k639_5 => [ "alpha_3" ],
	ISOCodes::k3166_1 => [ "alpha_2", "alpha_3", "numeric" ],
	ISOCodes::k3166_2 => [ "code" ],
	ISOCodes::k3166_3 => [ "alpha_2", "alpha_3", "alpha_4", "numeric" ],
	ISOCodes::k4217 => [ "alpha_3", "numeric" ],
	ISOCodes::k15924 => [ "alpha_4", "numeric" ]
];

//
// Save collection indexes.
//
$keys = [];
foreach( $codes as $key => $val )
{
	foreach( $val as $field )
		$keys[ $key ][] = [ 'key' => [ $field => 1 ], "name" => "idx_" . $field ];
	$keys[ $key ][] = [ 'key' => [ 'synonym' => 1 ], "name" => "idx_synonym" ];
}

//
// Connect database.
//
$database->Connect();

//
// Reference and drop collections.
//
$col_schema = $database->Client( "schema", [] );
$col_schema->Drop();
$col_standards = [];
foreach( $iso->Standards() as $standard )
{
	$col_standards[ $standard ] = $database->Client( "ISO_$standard", [] );
	$col_standards[ $standard ]->Drop();
	$col_standards[ $standard ]->Connection()->createIndexes( $keys[ $standard ] );
}

//
// Load standards.
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
	$schema[ $col_schema->DocumentKey() ] = $standard;
	$schema[ "schema" ] = $schema[ '$schema' ];
	unset( $schema[ '$schema' ] );
	$col_schema->AddOne( $schema );

	//
	// Write data.
	//
	$iterator = $iso->getIterator( $standard );
	foreach( $iterator as $key => $data )
	{
		//
		// Build record.
		//
		$data[ $col_standards[ $standard ]->DocumentKey() ] = $key;

		//
		// Add synonyms.
		//
		$synonyms = [];
		foreach( $codes[ $standard ] as $code )
		{
			if( array_key_exists( $code, $data ) )
				$synonyms[] = $data[ $code ];
		}
		if( count( $synonyms ) )
			$data[ "synonym" ] = array_unique( $synonyms );

		//
		// Write record.
		//
		$col_standards[ $standard ]->AddOne( $data );
	}
}

echo( "\nDone!\n" );


?>
