<?php

/**
 * WB2Json.php
 *
 * This file contains the script to write the JSON files from the World Bank API using the
 * SMART standards, usage:
 *
 * <code>
 * php -f WB2Json.php <json output directory>
 * </code>
 *
 * <ul>
 * 	<li><b>json directory</b>: Path to the output directory for JSON files.
 * </ul>
 *
 * The list of data elements will be the following:
 *
 * <ul>
 * 	<li>Data sources (http://api.worldbank.org/sources)
 * 	<li>Topics (http://api.worldbank.org/topics)
 * 	<li>Indicators (http://api.worldbank.org/indicators)
 * 	<li>Income Levels (http://api.worldbank.org/incomeLevels)
 * 	<li>Lending Types (http://api.worldbank.org/lendingTypes)
 * 	<li>Countries (http://api.worldbank.org/countries)
 * 	<li>
 * </ul>
 *
 * <em>All files will be overwritten.</em>
 */

/**
 * Include local definitions.
 */
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Constants.
//
define( "kId", "_id" );
define( "kKey", "_key" );
define( "kFrom", "_from" );
define( "kTo", "_to" );
define( "kPredicate", "predicate" );
define( "kNid", "nid" );
define( "kLid", "lid" );
define( "kGid", "gid" );
define( "kSynonym", "synonym" );
define( "kDeploy", "deploy" );
define( "kLabel", "label" );
define( "kDefinition", "definition" );
define( "kDescription", "description" );
define( "kNote", "note" );
define( "kExample", "example" );
define( "kDomain", "domain" );
define( "kCategory", "category" );
define( "kReference", "reference" );
define( "kBranches", "branches" );
define( "kStdDataSource", "source" );
define( "kStdTopic", "topic" );
define( "kStdIndicator", "indicator" );
define( "kStdIncome", "income" );
define( "kStdLending", "lending" );
define( "kStdCountry", "country" );
define( "kStdLink", "http://api.worldbank.org/" );
define( "kLangNS", "ISO:639-3:" );
define( "kDesSrc", "WB:source" );
define( "kDesOrg", "WB:org" );
define( "kDesTop", "WB:topic" );

//
// World Bank languages.
//
$languages = [ "es" => "spa", "fr" => "fra", "ar" => "ara", "zh" => "zho" ];

//
// World Bank links.
//
$standards = [
	kStdDataSource => "sources",
	kStdTopic => "topics",
	kStdIndicator => "indicators",
	kStdIncome => "incomeLevels",
	kStdLending => "lendingTypes",
	kStdCountry => "countries"
];


/*=======================================================================================
 *																						*
 *											MAIN										*
 *																						*
 *======================================================================================*/

//
// Check arguments.
//
if( $argc < 2 )
	exit( "Usage: php -f WB2Json.php <output json directory>\n" );

//
// Get arguments.
//
$j = $argv[ 1 ];

//
// Check output directory.
//
$directory = new SplFileInfo( $j );
if( ! $directory->isDir() )
	exit( "Output directory is not a directory.\n" );
elseif( ! $directory->isWritable() )
	exit( "Output directory is not writable.\n" );

//
// Handle standards.
//
DataSources( $directory, $standards, $languages );
Topics( $directory, $standards, $languages );
Indicators( $directory, $standards, $languages );

echo( "\nDone!\n" );



/*=======================================================================================
 *																						*
 *									STANDARDS HANDLERS	  			  					*
 *																						*
 *======================================================================================*/



/*===================================================================================
 *	Data Sources																	*
 *==================================================================================*/

/**
 * <h4>Handle World Bank data sources.</h4><p />
 *
 * This method will generate the WB:source terms and schema.
 *
 * @param SplFileInfo			$theDirectory	 	Output directory.
 * @param array					$theStandards	 	List of standards.
 * @param array					$theLanguages	 	List of languages.
 */
function DataSources( SplFileInfo	$theDirectory,
					  $theStandards,
					  $theLanguages)
{
	//
	// Init local storage.
	//
	$standard = kStdDataSource;
	$link = $theStandards[ kStdDataSource ];
	$term_file = "TERMS_WB_" . kStdDataSource;
	$edge_file = "SCHEMAS_WB_" . kStdDataSource;
	$namespace = "WB:" . kStdDataSource;

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Init loop local storage.
	//
	$page = 1;
	$lines = 100;
	$terms = [];
	$edges = [];

	//
	// Load base records.
	//
	$language = kLangNS . "eng";
	do
	{
		//
		// Make request.
		//
		$request = kStdLink . "/$link?page=$page&per_page=$lines&format=json";
		$input = json_decode( file_get_contents( $request ), true );

		//
		// Iterate records.
		//
		foreach( $input[ 1 ] as $record )
		{
			//
			// Create term.
			//
			$code = (string) $record[ "id" ];
			$terms[ $code ] = [];
			$cur = & $terms[ $code ];

			//
			// Load term.
			//
			$key = "$namespace:$code";
			$cur[ kId ] = "TERMS/$key";
			$cur[ kKey ] = $key;
			$cur[ kNid ] = "TERMS/$namespace";
			$cur[ kLid ] = $code;
			$cur[ kGid ] = $key;

			//
			// Load label.
			//
			$cur[ kLabel ] = [ $language => $record[ "name" ] ];

			//
			// Load definition.
			//
			if( $record[ "description" ] != "" )
				$cur[ kDefinition ] = [ $language => $record[ "description" ] ];

			//
			// Add link.
			//
			if( $record[ "url" ] != "" )
				$cur[ kReference ] = $record[ "url" ];

		} // Iterating records.

		//
		// Next page.
		//
		$page++;

	} while( count( $input[ 1 ] ) );

	//
	// Add other languages.
	//
	foreach( $theLanguages as $lng2 => $lng3 )
	{
		//
		// Set language.
		//
		$language = kLangNS . $lng3;

		//
		// Iterate records.
		//
		$page = 1;
		do
		{
			//
			// Make request.
			//
			$request = kStdLink . "$lng2/$link?page=$page&per_page=$lines&format=json";
			$input = json_decode( file_get_contents( $request ), true );

			//
			// Iterate records.
			//
			foreach( $input[ 1 ] as $record )
			{
				//
				// Select record.
				//
				$code = (string) $record[ "id" ];
				$current = & $terms[ $code ];

				//
				// Load label.
				//
				if( $record[ "name" ] != "" )
					$current[ kLabel ][ $language ] = $record[ "name" ];

				//
				// Load definition.
				//
				if( $record[ "description" ] != "" )
					$current[ kDefinition ][ $language ] = $record[ "description" ];

			} // Iterating records.

			//
			// Next page.
			//
			$page++;

		} while( count( $input[ 1 ] ) );

	} // Iterating languages.

	//
	// Build edges.
	//
	foreach( $terms as $term )
	{
		//
		// Init edge.
		//
		$edge = [];

		//
		// Build edge.
		//
		$from = $term[ kId ];
		$to = $term[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ kPredicate ] = $predicate;
		$edge[ kBranches ] = [ $to ];

		//
		// Add edge.
		//
		$edges[] = $edge;

	} // Iterating terms.

	//
	// Write TERMS JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_WB_$standard.json";
	$data = json_encode( array_values( $terms ), JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_WB_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

} // DataSources.


/*===================================================================================
 *	Topics																			*
 *==================================================================================*/

/**
 * <h4>Handle World Bank topics.</h4><p />
 *
 * This method will generate the WB:topic terms and schema.
 *
 * @param SplFileInfo			$theDirectory	 	Output directory.
 * @param array					$theStandards	 	List of standards.
 * @param array					$theLanguages	 	List of languages.
 */
function Topics( SplFileInfo	$theDirectory,
				 $theStandards,
				 $theLanguages)
{
	//
	// Init local storage.
	//
	$standard = kStdTopic;
	$link = $theStandards[ kStdTopic ];
	$term_file = "TERMS_WB_" . kStdTopic;
	$edge_file = "SCHEMAS_WB_" . kStdTopic;
	$namespace = "WB:" . kStdTopic;

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Init loop local storage.
	//
	$page = 1;
	$lines = 100;
	$terms = [];
	$edges = [];

	//
	// Load base records.
	//
	$language = kLangNS . "eng";
	do
	{
		//
		// Make request.
		//
		$request = kStdLink . "/$link?page=$page&per_page=$lines&format=json";
		$input = json_decode( file_get_contents( $request ), true );

		//
		// Iterate records.
		//
		foreach( $input[ 1 ] as $record )
		{
			//
			// Create term.
			//
			$code = (string) $record[ "id" ];
			$terms[ $code ] = [];
			$cur = & $terms[ $code ];

			//
			// Load term.
			//
			$key = "$namespace:$code";
			$cur[ kId ] = "TERMS/$key";
			$cur[ kKey ] = $key;
			$cur[ kNid ] = "TERMS/$namespace";
			$cur[ kLid ] = $code;
			$cur[ kGid ] = $key;

			//
			// Load label.
			//
			$cur[ kLabel ] = [ $language => $record[ "value" ] ];

			//
			// Load definition.
			//
			if( $record[ "sourceNote" ] != "" )
				$cur[ kDefinition ] = [ $language => $record[ "sourceNote" ] ];

		} // Iterating records.

		//
		// Next page.
		//
		$page++;

	} while( count( $input[ 1 ] ) );

	//
	// Add other languages.
	//
	foreach( $theLanguages as $lng2 => $lng3 )
	{
		//
		// Set language.
		//
		$language = kLangNS . $lng3;

		//
		// Iterate records.
		//
		$page = 1;
		do
		{
			//
			// Make request.
			//
			$request = kStdLink . "$lng2/$link?page=$page&per_page=$lines&format=json";
			$input = json_decode( file_get_contents( $request ), true );

			//
			// Iterate records.
			//
			foreach( $input[ 1 ] as $record )
			{
				//
				// Select record.
				//
				$code = (string) $record[ "id" ];
				$current = & $terms[ $code ];

				//
				// Load label.
				//
				if( $record[ "value" ] != "" )
					$current[ kLabel ][ $language ] = $record[ "value" ];

				//
				// Load definition.
				//
				if( $record[ "sourceNote" ] != "" )
					$current[ kDefinition ][ $language ] = $record[ "sourceNote" ];

			} // Iterating records.

			//
			// Next page.
			//
			$page++;

		} while( count( $input[ 1 ] ) );

	} // Iterating languages.

	//
	// Build edges.
	//
	foreach( $terms as $term )
	{
		//
		// Init edge.
		//
		$edge = [];

		//
		// Build edge.
		//
		$from = $term[ kId ];
		$to = $term[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ kPredicate ] = $predicate;
		$edge[ kBranches ] = [ $to ];

		//
		// Add edge.
		//
		$edges[] = $edge;

	} // Iterating terms.

	//
	// Write TERMS JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_WB_$standard.json";
	$data = json_encode( array_values( $terms ), JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_WB_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

} // Topics.


/*===================================================================================
 *	Indicators																		*
 *==================================================================================*/

/**
 * <h4>Handle World Bank indicators.</h4><p />
 *
 * This method will generate the WB:indicator terms and schema.
 *
 * @param SplFileInfo			$theDirectory	 	Output directory.
 * @param array					$theStandards	 	List of standards.
 * @param array					$theLanguages	 	List of languages.
 */
function Indicators( SplFileInfo	$theDirectory,
									$theStandards,
									$theLanguages)
{
	//
	// Init local storage.
	//
	$standard = kStdIndicator;
	$link = $theStandards[ kStdIndicator ];
	$term_file = "TERMS_WB_" . kStdIndicator;
	$edge_file = "SCHEMAS_WB_" . kStdIndicator;
	$namespace = "WB:" . kStdIndicator;

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Init loop local storage.
	//
	$page = 1;
	$lines = 800;
	$terms = [];
	$edges = [];

	//
	// Load base records.
	//
	echo( "Base records    en ");
	$language = kLangNS . "eng";
	do
	{
		//
		// Make request.
		//
		$request = kStdLink . "/$link?page=$page&per_page=$lines&format=json";
		$input = json_decode( file_get_contents( $request ), true );

		//
		// Iterate records.
		//
		foreach( $input[ 1 ] as $record )
		{
			//
			// Create term.
			//
			$code = (string) $record[ "id" ];
			$terms[ $code ] = [];
			$cur = & $terms[ $code ];

			//
			// Load term.
			//
			$key = "$namespace:$code";
			$cur[ kId ] = "TERMS/$key";
			$cur[ kKey ] = $key;
			$cur[ kNid ] = "TERMS/$namespace";
			$cur[ kLid ] = $code;
			$cur[ kGid ] = $key;

			//
			// Load label.
			//
			$cur[ kLabel ] = [ $language => $record[ "name" ] ];

			//
			// Load definition.
			//
			if( $record[ "sourceNote" ] != "" )
				$cur[ kDefinition ] = [ $language => $record[ "sourceNote" ] ];

			//
			// Load organisation.
			//
			if( $record[ "sourceOrganization" ] != "" )
				$cur[ kDesOrg ] = [ $language => $record[ "sourceOrganization" ] ];

			//
			// Load source.
			//
			$cur[ kDesSrc ] = "WB:" . kStdDataSource . ":" . $record[ "source" ][ "id" ];

			//
			// Load topics.
			//
			if( count( $record[ "topics" ] ) )
			{
				$tmp = [];
				foreach( $record[ "topics" ] as $topic )
				{
					if( array_key_exists( "id", $topic ) )
						$tmp[] = "WB:" . kStdTopic . ":" . $topic[ "id" ];
				}
				if( count( $tmp ) )
					$cur[ kDesTop ] = $tmp;

			} // Has topics.

		} // Iterating records.

		//
		// Next page.
		//
		$page++;
		echo(".");

	} while( count( $input[ 1 ] ) );

	//
	// Add other languages.
	//
	echo( "\nOther languages:");
	foreach( $theLanguages as $lng2 => $lng3 )
	{
		//
		// Set language.
		//
		echo( "\n                $lng2");
		$language = kLangNS . $lng3;

		//
		// Iterate records.
		//
		$page = 1;
		do
		{
			//
			// Make request.
			//
			$request = kStdLink . "$lng2/$link?page=$page&per_page=$lines&format=json";
			$input = json_decode( file_get_contents( $request ), true );

			//
			// Iterate records.
			//
			foreach( $input[ 1 ] as $record )
			{
				//
				// Select record.
				//
				$code = (string) $record[ "id" ];
				$current = & $terms[ $code ];

				//
				// Load label.
				//
				if( $record[ "name" ] != "" )
					$current[ kLabel ][ $language ] = $record[ "name" ];

				//
				// Load definition.
				//
				if( $record[ "sourceNote" ] != "" )
					$current[ kDefinition ][ $language ] = $record[ "sourceNote" ];

				//
				// Load organisation.
				//
				if( $record[ "sourceOrganization" ] != "" )
					$cur[ kDesOrg ][ $language ] = $record[ "sourceOrganization" ];

			} // Iterating records.

			//
			// Next page.
			//
			$page++;
			echo(".");

		} while( count( $input[ 1 ] ) );

	} // Iterating languages.

	//
	// Build edges.
	//
	foreach( $terms as $term )
	{
		//
		// Init edge.
		//
		$edge = [];

		//
		// Build edge.
		//
		$from = $term[ kId ];
		$to = $term[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ kPredicate ] = $predicate;
		$edge[ kBranches ] = [ $to ];

		//
		// Add edge.
		//
		$edges[] = $edge;

	} // Iterating terms.

	//
	// Write TERMS JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_WB_$standard.json";
	$data = json_encode( array_values( $terms ), JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_WB_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

} // Indicators.



?>
