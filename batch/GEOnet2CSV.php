<?php

/**
 * GEOnet2CSV.php
 *
 * This file contains the script to read the GEOnet individual country files and generate a
 * single CSV file, usage:
 *
 * <code>
 * php -f GEOnet2CSV.php <input directory> <output CSV file path>
 * </code>
 *
 * <ul>
 * 	<li><b>input directory</b>: Path to the input directory containing the individual files.
 * 	<li><b>CSV file path</b>: Path to the output CSV file, it will be overwritten.
 * </ul>
 *
 * @example
 * <code>
 * // Use the ISO ArangoDB database.
 * php -f "batch/GEOnet2CSV.php" "~/Desktop/GEOnet_files/" "~/Desktop/out.csv"
 * </code>
 */

/**
 * Include local definitions.
 */
require_once(dirname(__DIR__) . "/includes.local.php");


/*=======================================================================================
 *																						*
 *											MAIN										*
 *																						*
 *======================================================================================*/

//
// Check arguments.
//
if( $argc < 3 )
	exit( "Usage: php -f GEOnet2CSV.php <Path to the input files directory> <Path to the output CSV file>\n" );

//
// Get arguments.
//
$d = $argv[ 1 ];
$f = $argv[ 2 ];

//
// Instantiate file system objects.
//
$input = new SplFileInfo( $d );
$output = new SplFileInfo( $f );

//
// Check input directory.
//
if( ! $input->isReadable() )
	exit( "Input directory is not readable.\n" );

//
// Check output directory.
//
if( ! $output->getPathInfo()->isWritable() )
	exit( "Output directory is not writable.\n" );

//
// Open output file.
//
$csv = $output->openFile( 'w' );

//
// Mark output header not done.
//
$has_header = false;

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

		//
		// Iterate file contents.
		//
		$done_header = false;
		foreach( $txt as $row )
		{
			//
			// Get line.
			//
			$line = $row[ 0 ];

			//
			// Get header.
			//
			if( ! $done_header )
			{
				//
				// Copy header to output file.
				//
				if( ! $has_header )
				{
					$csv->fwrite( "country\t$line\r\n" );
					$has_header = TRUE;
				}

				$done_header = TRUE;
				continue;														// =>
			}

			//
			// Write row.
			//
			$csv->fwrite( strtoupper( $country ) . "\t$line\r\n" );
		}
	}
}

echo( "\nDone!\n" );



?>
