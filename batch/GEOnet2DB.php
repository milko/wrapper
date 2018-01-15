<?php

/**
 * GEOnet2DB.php
 *
 * This file contains the script to read the GEOnet individual country files and store the
 * data in a database, usage:
 *
 * <code>
 * php -f GEOnet2DB.php <input directory> <connection URI>
 * </code>
 *
 * <ul>
 * 	<li><b>input directory</b>: Path to the input directory containing the individual files.
 * 	<li><b>connection URI</b>: Connection URI.
 * </ul>
 *
 * @example
 * <code>
 * // Load the ISO ArangoDB database.
 * php -f "batch/GEOnet2DB.php" "~/Desktop/GEOnet_files/" "tcp://localhost:8529/NGA"
 *
 * // Load the ISO MongoDB database.
 * php -f "batch/ISO2Database.php" "~/Desktop/GEOnet_files/" "mongodb://localhost:27017/NGA"
 * </code>
 */

/**
 * Include local definitions.
 */
require_once(dirname(__DIR__) . "/includes.local.php");

/**
 * Globals.
 */
define( "kCollection", "table" );


/*=======================================================================================
 *																						*
 *											MAIN										*
 *																						*
 *======================================================================================*/

//
// Check arguments.
//
if( $argc < 3 )
	exit( "Usage: php -f GEOnet2DB.php <Path to the input files directory> <Connection URI>\n" );

//
// Check input directory.
//
$input = new SplFileInfo( $argv[ 1 ] );
if( ! $input->isReadable() )
	exit( "Input directory is not readable.\n" );

//
// Open database connection.
//
if( substr( $argv[ 2 ], 0, 7 ) == "mongodb" )
{
	$database = \Milko\wrapper\MongoDB\Server::NewConnection( $argv[ 2 ] );
	if( get_class( $database ) != "Milko\\wrapper\\MongoDB\\Collection" )
		exit( "Invalid connection string: expecting a database reference.\n" );
}
else
{
	$database = \Milko\wrapper\ArangoDB\Server::NewConnection( $argv[ 2 ] );
	if( get_class( $database ) != "Milko\\wrapper\\ArangoDB\\Collection" )
		exit( "Invalid connection string: expecting a database reference.\n" );
}

//
// Connect database and drop collection.
//
$database->Connect();
$collection = $database->Client( kCollection, [] );
$collection->Drop();

//
// Iterate .
//
$iterator = new DirectoryIterator( $input->getRealPath() );
foreach( $iterator as $file )
{
	//
	// Consider only text files.
	//
	if( ($extension = $file->getExtension()) == "txt" )
	{
		//
		// Save file base name.
		//
		$country = $file->getBasename( ".txt" );

		//
		// Inform.
		//
		echo( "$country\n" );

		//
		// Open input file.
		//
		$txt = new SplFileObject( $file->getRealPath() );
		$txt->setFlags( SplFileObject::READ_CSV );
		$txt->setCsvControl( "\t" );

		//
		// Iterate file contents.
		//
		$header = [];
		foreach( $txt as $row )
		{
			//
			// Handle header.
			//
			if( ! count( $header ) )
			{
				$header = $row;
				continue;														// =>
			}

			//
			// Handle row size.
			//
			$more = count( $header ) - count( $row );

			while($more--)
				$row[] = NULL;

			//
			// Parse row.
			//
			$record = array_combine( $header, $row );
			foreach( $record as $key => $value ){
				switch( $key ) {
					case "UFI":
					case "UNI":
						$record[ $key ] = (int) $value;
						break;

					case "LAT":
					case "LONG":
						$record[ $key ] = (double) $value;
						break;

					default:
						$record[ $key ] = trim( (string) $value );
						if( $record[ $key ] == "" )
							unset( $record[ $key ] );
						break;
				}
			}

			//
			// Add country.
			//
			if(! array_key_exists( "country", $record ) )
				$record[ "country" ] = strtoupper( $country );

			//
			// Save record.
			//
			$collection->AddOne( $record );
		}
	}
}

echo( "\nDone!\n" );



?>
