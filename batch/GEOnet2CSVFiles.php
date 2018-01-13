<?php

/**
 * GEOnet2CSVFiles.php
 *
 * This file contains the script to add the country code to each GEOnet file, usage:
 *
 * <code>
 * php -f GEOnet2CSV.php <input directory> <output directory>
 * </code>
 *
 * <ul>
 * 	<li><b>input directory</b>: Path to the input directory containing the individual files.
 * 	<li><b>output directory</b>: Path to the output directory, Modified files will be written there.
 * </ul>
 *
 * @example
 * <code>
 * // Use the ISO ArangoDB database.
 * php -f "batch/GEOnet2CSV.php" "~/Desktop/GEOnet_files/" "~/Desktop/out/"
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
	exit( "Usage: php -f GEOnet2CSV.php <Path to the input files directory> <Path to the output directory>\n" );

//
// Get arguments.
//
$i = $argv[ 1 ];
$o = $argv[ 2 ];

//
// Instantiate file system objects.
//
$input = new SplFileInfo( $i );
$output = new SplFileInfo( $o );

//
// Check input directory.
//
if( ! $input->isReadable() )
	exit( "Input directory is not readable.\n" );

//
// Check output directory.
//
if( ! $output->isWritable() )
	exit( "Output directory is not writable.\n" );

//
// Iterate.
//
$lines = 0;
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
		// Open output file.
		//
		$new_file = new SplFileInfo( $output->getRealPath() . "/$country.txt" );
		$fp = $new_file->openFile( 'w' );

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
			// Write header ot data.
			//
			if( ! $done_header ) {
				$fp->fwrite( "country\t$line\r\n" );
				$done_header = true;
			}
			else {
				$fp->fwrite( strtoupper( $country ) . "\t$line\r\n" );
				$lines++;
			}
		}
	}
}
echo( "Written $lines lines.\n" );

echo( "\nDone!\n" );



?>
