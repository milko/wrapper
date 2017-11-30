<?php

/**
 * WB2Json.php
 *
 * This file contains the script to write the JSON files from the World Bank API using the
 * SMART standards, usage:
 *
 * <code>
 * php -f WB2Json.php <connection URI> <json output directory>
 * </code>
 *
 * <ul>
 * 	<li><b>connection URI</b>: Connection URI.
 * 	<li><b>json directory</b>: Path to the output directory for JSON files.
 * </ul>
 *
 * The database connection is required to have the list of standard descriptors, it
 * expects a collection named "DESCRIPTORS".
 *
 * The list of data elements will be the following:
 *
 * <ul>
 * 	<li>Data sources (http://api.worldbank.org/v2/sources)
 * 	<li>Topics (http://api.worldbank.org/v2/topics)
 * 	<li>Indicators (http://api.worldbank.org/v2/indicators)
 * 	<li>Income Levels (http://api.worldbank.org/v2/incomeLevels)
 * 	<li>Lending Types (http://api.worldbank.org/v2/lendingTypes)
 * 	<li>Countries (http://api.worldbank.org/v2/countries)
 * 	<li>
 * </ul>
 *
 * <em>All files will be overwritten.</em>
 *
 * @example
 * <code>
 * // Use the ISO ArangoDB database.
 * php -f "batch/WB2Json.php" "tcp://localhost:8529/ISO" "data/JSON"
 *
 * // Load the ISO MongoDB database.
 * php -f "batch/WB2Json.php" "mongodb://localhost:27017/ISO" "data/JSON"
 * </code>
 */

/**
 * Include local definitions.
 */
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Defaults.
//
define( "kLangNS", "ISO:639:3:" );					// Language namespace prefix.
define( "kAvail", "STD:avail:" );					// Availability namespace prefix.
define( "kDesSrc", "WB:source" );

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
define( "kSymbol", "symbol" );
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
define( "kStdRegion", "region" );
define( "kStdAdminRegion", "admin" );
define( "kStdLink", "http://api.worldbank.org/v2/" );
define( "kDesCountry", "WB:country" );
define( "kDesRegion", "WB:region" );
define( "kDesAdminRegion", "WB:admin" );
define( "kDesShape", "shape" );
define( "kDesCapital", "WB:capital" );

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
if( $argc < 3 )
	exit( "Usage: php -f WB2Json.php <connection URI> <output json directory>\n" );

//
// Get arguments.
//
$c = $argv[ 1 ];
$j = $argv[ 2 ];

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
// Check output directory.
//
$directory = new SplFileInfo( $j );
if( ! $directory->isDir() )
	exit( "Output directory is not a directory.\n" );
elseif( ! $directory->isWritable() )
	exit( "Output directory is not writable.\n" );

//
// Connect database.
//
$database->Connect();

//
// Load descriptors.
//
$const_term = TERMS($database );
$const_desc = DESCRIPTORS($database );

//
// Handle standards.
//
DataSources($directory, $standards, $languages, $const_term, $const_desc );
Topics($directory, $standards, $languages, $const_term, $const_desc );
Indicators($directory, $standards, $languages, $const_term, $const_desc );
Income($directory, $standards, $languages, $const_term, $const_desc );
Lending($directory, $standards, $languages, $const_term, $const_desc );
Country($directory, $standards, $languages, $const_term, $const_desc );

echo( "\nDone!\n" );



/*=======================================================================================
 *																						*
 *									DESCRIPTORS HANDLERS 			  					*
 *																						*
 *======================================================================================*/



/*===================================================================================
 *	TERMS																			*
 *==================================================================================*/

/**
 * <h4>Load terms.</h4><p />
 *
 * This method will return the list of terms, an array with constant as key and
 * _key as value.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 *
 * @return array						List of terms.
 */
function TERMS( \Milko\Wrapper\ClientServer	$theDatabase)
{
	//
	// Init local storage.
	//
	$terms = [];
	$collection = $theDatabase->Client( "TERMS", [] );
	$collection->Connect();

	//
	// Inform.
	//
	echo( "TERMS\n" );

	//
	// Iterate descriptors.
	//
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Handle only those with const.
		//
		if( array_key_exists( "const", $input ) )
			$terms[ $input[ "const" ] ] = $input[ "_key" ];

	} // Iterate all records.

	return $terms;																	// ==>

} // TERMS.


/*===================================================================================
 *	DESCRIPTORS																		*
 *==================================================================================*/

/**
 * <h4>Load descriptors.</h4><p />
 *
 * This method will return the list of descriptors, an array with constant as key and
 * _key as value.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 *
 * @return array						List of descriptors.
 */
function DESCRIPTORS( \Milko\Wrapper\ClientServer	$theDatabase)
{
	//
	// Init local storage.
	//
	$descriptors = [];
	$collection = $theDatabase->Client( "DESCRIPTORS", [] );
	$collection->Connect();

	//
	// Inform.
	//
	echo( "DESCRIPTORS\n" );

	//
	// Iterate descriptors.
	//
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Handle only those with const.
		//
		if( array_key_exists( "const", $input ) )
			$descriptors[ $input[ "const" ] ] = $input[ "_key" ];

	} // Iterate all records.

	return $descriptors;															// ==>

} // DESCRIPTORS.



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
 * @param array					theTerms	 		List of terms.
 * @param array					$theDescriptors	 	List of descriptors.
 */
function DataSources( SplFileInfo	$theDirectory,
								    $theStandards,
								    $theLanguages,
								    $theTerms,
								    $theDescriptors)
{
	//
	// Init local storage.
	//
	$standard = kStdDataSource;
	$link = $theStandards[ kStdDataSource ];
	$namespace = $theTerms[ "kWB_source" ];

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
	$language = $theTerms[ "kISO_639_3" ] . ":eng";
	do
	{
		//
		// Make request.
		//
		$request = kStdLink . "en/$link?page=$page&per_page=$lines&format=json";
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
			// Load symbols.
			//
			$cur[ $theDescriptors[ "kSymbol" ] ] = $code;
			$cur[ $theDescriptors[ "kSynonym" ] ] = [ $code ];

			//
			// Add link.
			//
			if( $record[ "url" ] != "" )
				$cur[ $theDescriptors[ "kInfo" ] ] = $record[ "url" ];

			//
			// Add data availability.
			//
			if( array_key_exists("dataavailability", $record )
				&& ($record[ "dataavailability" ] != "") )
				$cur[ $theDescriptors[ "kSTD_avail_data" ] ]
					= $theTerms[ "kSTD_avail" ] . ":" . $record[ "dataavailability" ];

			//
			// Add metadata availability.
			//
			if( array_key_exists("metadataavailability", $record )
				&& ($record[ "metadataavailability" ] != "") )
				$cur[ $theDescriptors[ "kSTD_avail_meta" ] ]
					= $theTerms[ "kSTD_avail" ] . ":" . $record[ "metadataavailability" ];

			//
			// Load label.
			//
			$cur[ $theDescriptors[ "kLabel" ] ] = [ $language => $record[ "name" ] ];

			//
			// Load definition.
			//
			if( $record[ "description" ] != "" )
				$cur[ $theDescriptors[ "kDefinition" ] ]
					= [ $language => $record[ "description" ] ];

			//
			// Add last modification date.
			//
			if( array_key_exists("lastupdated", $record )
			 && ($record[ "lastupdated" ] != "") )
				$cur[ $theDescriptors[ "kMDate" ] ]
					= str_replace( "-", "", $record[ "lastupdated" ] );

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
		$language = $theTerms[ "kISO_639_3" ] . ":" . $lng3;

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
					$current[ $theDescriptors[ "kLabel" ] ][ $language ]
						= $record[ "name" ];

				//
				// Load definition.
				//
				if( $record[ "description" ] != "" )
					$current[ $theDescriptors[ "kDefinition" ] ][ $language ]
						= $record[ "description" ];

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
		$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];

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
 * @param array					$theTerms		 	List of terms.
 * @param array					$theDescriptors	 	List of descriptors.
 */
function Topics( SplFileInfo	$theDirectory,
							    $theStandards,
							    $theLanguages,
							    $theTerms,
							    $theDescriptors)
{
	//
	// Init local storage.
	//
	$standard = kStdTopic;
	$link = $theStandards[ kStdTopic ];
	$namespace = $theTerms[ 'kWB_topic' ];

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
	$language = $theTerms[ "kISO_639_3" ] . ":eng";
	do
	{
		//
		// Make request.
		//
		$request = kStdLink . "en/$link?page=$page&per_page=$lines&format=json";
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
			// Load symbols.
			//
			$cur[ $theDescriptors[ "kSymbol" ] ] = $code;
			$cur[ $theDescriptors[ "kSynonym" ] ] = [ $code ];

			//
			// Load label.
			//
			$cur[ $theDescriptors[ "kLabel" ] ]
				= [ $language => $record[ "value" ] ];

			//
			// Load definition.
			//
			if( $record[ "sourceNote" ] != "" )
				$cur[ $theDescriptors[ "kDefinition" ] ]
					= [ $language => $record[ "sourceNote" ] ];

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
		$language = $theTerms[ "kISO_639_3" ] . ":$lng3";

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
					$current[ $theDescriptors[ "kLabel" ] ][ $language ]
						= $record[ "value" ];

				//
				// Load definition.
				//
				if( $record[ "sourceNote" ] != "" )
					$current[ $theDescriptors[ "kDefinition" ] ][ $language ]
						= $record[ "sourceNote" ];

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
		$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];

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
 * @param array					$theTerms		 	List of terms.
 * @param array					$theDescriptors	 	List of descriptors.
 */
function Indicators( SplFileInfo	$theDirectory,
					 				$theStandards,
					 				$theLanguages,
					 				$theTerms,
					 				$theDescriptors )
{
	//
	// Init local storage.
	//
	$standard = kStdIndicator;
	$link = $theStandards[ kStdIndicator ];
	$namespace = $theTerms[ 'kWB_indicator' ];

	//
	// Inform.
	//
	echo( "$standard " );

	//
	// Init loop local storage.
	//
	$page = 1;
	$lines = 800;

	//
	// Load base records.
	//
	echo( "en ");
	$language = $theTerms[ "kISO_639_3" ] . ":eng";
	$terms = [];
	do
	{
		//
		// Make request.
		//
		$request = kStdLink . "en/$link?page=$page&per_page=$lines&format=json";
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
			// Load symbols.
			//
			$cur[ $theDescriptors[ "kSymbol" ] ] = $code;
			$cur[ $theDescriptors[ "kSynonym" ] ] = [ $code ];

			//
			// Load source.
			//
			$cur[ $theDescriptors[ "kWB_source" ] ]
				= $theTerms[ "kWB_source" ] . ":" . $record[ "source" ][ "id" ];

			//
			// Load topics.
			//
			if( count( $record[ "topics" ] ) )
			{
				$tmp = [];
				foreach( $record[ "topics" ] as $topic )
				{
					if( array_key_exists( "id", $topic ) )
						$tmp[] = $theTerms[ "kWB_topic" ] . ":" . $topic[ "id" ];
				}
				if( count( $tmp ) )
					$cur[ $theDescriptors[ "kWB_topic" ] ] = $tmp;

			} // Has topics.

			//
			// Load organisation.
			//
			if( $record[ "sourceOrganization" ] != "" )
				$cur[ $theDescriptors[ "kWB_org" ] ]
					= [ $language => $record[ "sourceOrganization" ] ];

			//
			// Load label.
			//
			$cur[ $theDescriptors[ "kLabel" ] ]
				= [ $language => $record[ "name" ] ];

			//
			// Load definition.
			//
			if( $record[ "sourceNote" ] != "" )
				$cur[ $theDescriptors[ "kDefinition" ] ]
					= [ $language => $record[ "sourceNote" ] ];

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
	foreach( $theLanguages as $lng2 => $lng3 )
	{
		//
		// Set language.
		//
		echo( "\n          $lng2 ");
		$language = $theTerms[ "kISO_639_3" ] . ":$lng3";

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
				// Load organisation.
				//
				if( $record[ "sourceOrganization" ] != "" )
					$cur[ $theDescriptors[ "kWB_org" ] ][ $language ]
						= $record[ "sourceOrganization" ];

				//
				// Load label.
				//
				if( $record[ "name" ] != "" )
					$current[ $theDescriptors[ "kLabel" ] ][ $language ]
						= $record[ "name" ];

				//
				// Load definition.
				//
				if( $record[ "sourceNote" ] != "" )
					$current[ $theDescriptors[ "kDefinition" ] ][ $language ]
						= $record[ "sourceNote" ];

			} // Iterating records.

			//
			// Next page.
			//
			$page++;
			echo(".");

		} while( count( $input[ 1 ] ) );

	} // Iterating languages.

	echo( "\n" );

	//
	// Build edges.
	//
	$edges = [];
	$sources = $topics = [];
	foreach( $terms as $term )
	{
		//
		// Init edge.
		//
		$cat = false;

		//
		// Build source edge.
		//
		if( array_key_exists( kDesSrc, $term ) )
		{
			$tmp = $term[ kDesSrc ];

			$edge = [];
			$from = $term[ kId ];
			$to = "TERMS/$tmp";
			$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ $term[ kNid ] ];
			$edges[] = $edge;
			$cat = true;

			if( ! in_array( $tmp, $sources ) )
			{
				$edge = [];
				$from = "TERMS/$tmp";
				$to = $term[ kNid ];
				$predicate = "TERMS/" . $theTerms[ "k_predicate_category_of" ];
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
				$edge[ $theDescriptors[ "kBranches" ] ] = [ $term[ kNid ] ];
				$edges[] = $edge;
				$sources[] = $tmp;
			}
		}

		//
		// Build topics edge.
		//
		if( array_key_exists( $theDescriptors[ "kWB_topic" ], $term ) )
		{
			foreach( $term[ $theDescriptors[ "kWB_topic" ] ] as $topic )
			{
				$edge = [];
				$from = $term[ kId ];
				$to = "TERMS/$topic";
				$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
				$edge[ $theDescriptors[ "kBranches" ] ] = [ $term[ kNid ] ];
				$edges[] = $edge;
				$cat = true;

				if( ! in_array( $topic, $topics ) )
				{
					$edge = [];
					$from = "TERMS/$topic";
					$to = $term[ kNid ];
					$predicate = "TERMS/" . $theTerms[ "k_predicate_category_of" ];
					$hash = md5( "$from\t$to\t$predicate" );
					$edge[ kId ] = "SCHEMAS/$hash";
					$edge[ kKey ] = $hash;
					$edge[ kFrom ] = $from;
					$edge[ kTo ] = $to;
					$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
					$edge[ $theDescriptors[ "kBranches" ] ] = [ $term[ kNid ] ];
					$edges[] = $edge;
					$topics[] = $topic;
				}
			}
		}

		//
		// Build edge.
		//
		if( ! $cat )
		{
			$from = $term[ kId ];
			$to = $term[ kNid ];
			$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
			$edges[] = $edge;
		}

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


/*===================================================================================
 *	Income																			*
 *==================================================================================*/

/**
 * <h4>Handle World Bank income levels.</h4><p />
 *
 * This method will generate the WB:income terms and schema.
 *
 * @param SplFileInfo			$theDirectory	 	Output directory.
 * @param array					$theStandards	 	List of standards.
 * @param array					$theLanguages	 	List of languages.
 * @param array					$theTerms		 	List of terms.
 * @param array					$theDescriptors	 	List of descriptors.
 */
function Income( SplFileInfo	$theDirectory,
							    $theStandards,
							    $theLanguages,
							    $theTerms,
							    $theDescriptors )
{
	//
	// Init local storage.
	//
	$standard = kStdIncome;
	$link = $theStandards[ kStdIncome ];
	$namespace = $theTerms[ 'kWB_income' ];

	//
	// Inform.
	//
	echo( "$standard    " );

	//
	// Init loop local storage.
	//
	$page = 1;
	$lines = 10;
	$terms = [];
	$edges = [];

	//
	// Load base records.
	//
	echo( "en ");
	$language = $theTerms[ "kISO_639_3" ] . ":eng";
	do
	{
		//
		// Make request.
		//
		$request = kStdLink . "en/$link?page=$page&per_page=$lines&format=json";
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
			// Load symbols.
			//
			$cur[ $theDescriptors[ "kSymbol" ] ] = $code;
			$cur[ $theDescriptors[ "kSynonym" ] ] = [ $code ];

			//
			// Load label.
			//
			$cur[ $theDescriptors[ "kLabel" ] ] = [ $language => $record[ "value" ] ];

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
	foreach( $theLanguages as $lng2 => $lng3 )
	{
		//
		// Set language.
		//
		echo( "\n          $lng2 ");
		$language = $theTerms[ "kISO_639_3" ] . ":$lng3";

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
					$current[ $theDescriptors[ "kLabel" ] ][ $language ]
						= $record[ "value" ];

			} // Iterating records.

			//
			// Next page.
			//
			$page++;
			echo(".");

		} while( count( $input[ 1 ] ) );

	} // Iterating languages.

	echo( "\n" );

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
		$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];

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

} // Income.


/*===================================================================================
 *	Lending																			*
 *==================================================================================*/

/**
 * <h4>Handle World Bank lending types.</h4><p />
 *
 * This method will generate the WB:lending terms and schema.
 *
 * @param SplFileInfo			$theDirectory	 	Output directory.
 * @param array					$theStandards	 	List of standards.
 * @param array					$theLanguages	 	List of languages.
 * @param array					$theTerms		 	List of terms.
 * @param array					$theDescriptors	 	List of descriptors.
 */
function Lending( SplFileInfo	$theDirectory,
							    $theStandards,
							    $theLanguages,
							    $theTerms,
							    $theDescriptors )
{
	//
	// Init local storage.
	//
	$standard = kStdLending;
	$link = $theStandards[ kStdLending ];
	$namespace = $theTerms[ 'kWB_lending' ];

	//
	// Inform.
	//
	echo( "$standard   " );

	//
	// Init loop local storage.
	//
	$page = 1;
	$lines = 10;
	$terms = [];
	$edges = [];

	//
	// Load base records.
	//
	echo( "en ");
	$language = $theTerms[ "kISO_639_3" ] . ":eng";
	do
	{
		//
		// Make request.
		//
		$request = kStdLink . "en/$link?page=$page&per_page=$lines&format=json";
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
			// Load symbols.
			//
			$cur[ $theDescriptors[ "kSymbol" ] ] = $code;
			$cur[ $theDescriptors[ "kSynonym" ] ] = [ $code ];

			//
			// Load label.
			//
			$cur[ $theDescriptors[ "kLabel" ] ] = [ $language => $record[ "value" ] ];

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
	foreach( $theLanguages as $lng2 => $lng3 )
	{
		//
		// Set language.
		//
		echo( "\n          $lng2 ");
		$language = $theTerms[ "kISO_639_3" ] . ":$lng3";

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
					$current[ $theDescriptors[ "kLabel" ] ][ $language ]
						= $record[ "value" ];

			} // Iterating records.

			//
			// Next page.
			//
			$page++;
			echo(".");

		} while( count( $input[ 1 ] ) );

	} // Iterating languages.

	echo( "\n" );

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
		$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];

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

} // Lending.


/*===================================================================================
 *	Country																			*
 *==================================================================================*/

/**
 * <h4>Handle World Bank countries.</h4><p />
 *
 * This method will generate the WB_COUNTRY collection.
 *
 * @param SplFileInfo			$theDirectory	 	Output directory.
 * @param array					$theStandards	 	List of standards.
 * @param array					$theLanguages	 	List of languages.
 * @param array					$theTerms		 	List of terms.
 * @param array					$theDescriptors	 	List of descriptors.
 */
function Country( SplFileInfo	$theDirectory,
							    $theStandards,
							    $theLanguages,
							    $theTerms,
							    $theDescriptors )
{
	//
	// Init local storage.
	//
	$standard = kStdCountry;
	$link = $theStandards[ kStdCountry ];
	$collection = "WB_COUNTRY";

	//
	// Inform.
	//
	echo( "$standard   " );

	//
	// Init loop local storage.
	//
	$page = 1;
	$lines = 50;
	$records = [];

	//
	// Load base records.
	//
	echo( "en ");
	$language = $theTerms[ "kISO_639_3" ] . ":eng";
	do
	{
		//
		// Make request.
		//
		$request = kStdLink . "en/$link?page=$page&per_page=$lines&format=json";
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
			$records[ $code ] = [];
			$cur = & $records[ $code ];

			//
			// Load term.
			//
			$cur[ "id" ] = $record[ "id" ];
			$cur[ "iso2Code" ] = $record[ "iso2Code" ];

			//
			// Init label.
			//
			$cur[ kLabel ] = [ $language => $record[ "name" ] ];

			//
			// Handle region.
			//
			if( $record[ "region" ][ "id" ] != "" )
				$cur[ "region" ] = [
					"id" => $record[ "region" ][ "id" ],
					kLabel => [ $language => $record[ "region" ][ "value" ] ]
				];

			//
			// Handle admin region.
			//
			if( $record[ "adminregion" ][ "id" ] != "" )
				$cur[ "adminregion" ] = [
					"id" => $record[ "adminregion" ][ "id" ],
					kLabel => [ $language => $record[ "adminregion" ][ "value" ] ]
				];

			//
			// Handle income & lending.
			//
			if( $record[ "incomeLevel" ][ "id" ] != "" )
				$cur[ "incomeLevel" ]
					= $theTerms[ "kWB_income" ] . ":" . $record[ "incomeLevel" ][ "id" ];
			if( $record[ "lendingType" ][ "id" ] != "" )
				$cur[ "lendingType" ]
					= $theTerms[ "kWB_lending" ] . ":" . $record[ "lendingType" ][ "id" ];

			//
			// Handle capital.
			//
			if( array_key_exists( "capitalCity", $record )
			 && ($record[ "capitalCity" ] != "" ) )
			{
				$cur[ "capitalCity" ]
					= [ kLabel =>  [ $language => $record[ "capitalCity" ] ] ];
				if( array_key_exists( "longitude", $record )
				 && array_key_exists( "latitude", $record )
				 && ($record[ "longitude" ] != "")
				 && ($record[ "latitude" ] != "") )
					$cur[ "capitalCity" ]
						[ "coordinates" ]
							= [ (double) $record[ "longitude" ],
								(double) $record[ "latitude" ] ];
			}

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
	foreach( $theLanguages as $lng2 => $lng3 )
	{
		//
		// Set language.
		//
		echo( "\n          $lng2 ");
		$language = $theTerms[ "kISO_639_3" ] . ":$lng3";

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
				$cur = & $records[ $code ];

				//
				// Load label.
				//
				if( array_key_exists( "name", $record )
				 && ($record[ "name" ] != "") )
					$cur[ kLabel ][ $language ] = $record[ "name" ];

				//
				// Load region.
				//
				if( ($record[ "region" ][ "id" ] != "")
				 && ($record[ "region" ][ "value" ] != "") )
					$cur[ "region" ][ kLabel ][ $language ]
						= $record[ "region" ][ "value" ];

				//
				// Load admin region.
				//
				if( ($record[ "adminregion" ][ "id" ] != "")
				 && ($record[ "adminregion" ][ "value" ] != "") )
					$cur[ "adminregion" ][ kLabel ][ $language ]
						= $record[ "adminregion" ][ "value" ];

				//
				// Load capital city.
				//
				if( array_key_exists( "capitalCity", $record )
				 && ($record[ "capitalCity" ] != "" ) )
					$cur[ "capitalCity" ][ kLabel ][ $language ]
						= $record[ "capitalCity" ];

			} // Iterating records.

			//
			// Next page.
			//
			$page++;
			echo(".");

		} while( count( $input[ 1 ] ) );

	} // Iterating languages.

	//
	// Collect regions.
	//
	$terms = [];
	$standard = kStdRegion;
	$namespace = $theTerms[ "kWB_region" ];
	foreach( $records as $record )
	{
		if( array_key_exists( "region", $record ) )
		{
			$code = $record[ "region" ][ "id" ];
			if( $code != "" )
			{
				if( ! array_key_exists( $code, $terms ) )
				{
					$terms[ $code ] = [];
					$cur = & $terms[ $code ];

					$key = "$namespace:$code";
					$cur[ kId ] = "TERMS/$key";
					$cur[ kKey ] = $key;
					$cur[ kNid ] = "TERMS/$namespace";
					$cur[ kLid ] = $code;
					$cur[ kGid ] = $key;
					$cur[ $theDescriptors[ "kSymbol" ] ] = $code;
					$cur[ $theDescriptors[ "kSynonym" ] ] = [ $code ];
					$cur[ $theDescriptors[ "kLabel" ] ]
						= $record[ "region" ][ kLabel ];
				}
			}
		}

	} // Iterating records.

	//
	// Build edges.
	//
	$edges = [];
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
		$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];

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

	//
	// Collect administrative regions.
	//
	$terms = [];
	$standard = kStdAdminRegion;
	$namespace = $theTerms[ "kWB_admin" ];
	foreach( $records as $record )
	{
		if( array_key_exists( "adminregion", $record ) )
		{
			$code = $record[ "adminregion" ][ "id" ];
			if( $code != "" )
			{
				if( ! array_key_exists( $code, $terms ) )
				{
					$terms[ $code ] = [];
					$cur = & $terms[ $code ];

					$key = "$namespace:$code";
					$cur[ kId ] = "TERMS/$key";
					$cur[ kKey ] = $key;
					$cur[ kNid ] = "TERMS/$namespace";
					$cur[ kLid ] = $code;
					$cur[ kGid ] = $key;
					$cur[ $theDescriptors[ "kSymbol" ] ] = $code;
					$cur[ $theDescriptors[ "kSynonym" ] ] = [ $code ];
					$cur[ $theDescriptors[ "kLabel" ] ]
						= $record[ "region" ][ kLabel ];
				}
			}
		}
	}

	//
	// Build edges.
	//
	$edges = [];
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
		$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];

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

	//
	// Collect countries.
	//
	$terms = [];
	$standard = kStdCountry;
	$namespace = $theTerms[ "kWB_country" ];
	foreach( $records as $record )
	{
		$cur = [];
		$code = $record[ "id" ];

		$key = "$namespace:$code";
		$cur[ kId ] = "TERMS/$key";
		$cur[ kKey ] = $key;
		$cur[ kNid ] = "TERMS/$namespace";
		$cur[ kLid ] = $code;
		$cur[ kGid ] = $key;
		$cur[ $theDescriptors[ "kSymbol" ] ] = $code;
		$cur[ $theDescriptors[ "kSynonym" ] ] = [ $code ];
		if( array_key_exists( "iso2Code", $record ) )
			$cur[ $theDescriptors[ "kSynonym" ] ][] = $record[ "iso2Code" ];

		if( array_key_exists( "region", $record ) )
			$cur[ $theDescriptors[ "kSTD_geo_politic" ] ]
				= $theTerms[ "kWB_region" ] . ":" . $record[ "region" ][ "id" ];
		if( array_key_exists( "adminregion", $record ) )
			$cur[ $theDescriptors[ "kSTD_geo_admin" ] ]
				= $theTerms[ "kWB_admin" ] . ":" . $record[ "adminregion" ][ "id" ];

		if( array_key_exists( "incomeLevel", $record ) )
			$cur[ $theDescriptors[ "kWB_income" ] ] = $record[ "incomeLevel" ];
		if( array_key_exists( "lendingType", $record ) )
			$cur[ $theDescriptors[ "kWB_lending" ]] = $record[ "lendingType" ];

		$cur[ $theDescriptors[ "kLabel" ] ] = $record[ kLabel ];
		if( array_key_exists( "capitalCity", $record ) )
		{
			if( array_key_exists( kLabel, $record[ "capitalCity" ] ) )
				$cur[ $theDescriptors[ "kWB_capital_city" ] ]
				[ $theDescriptors[ "kLabel" ] ] = $record[ "capitalCity" ][ kLabel ];
			if( array_key_exists( "coordinates", $record[ "capitalCity" ] ) )
				$cur[ $theDescriptors[ "kWB_capital_city" ] ]
				[ $theDescriptors[ "kSTD_geo_DEG" ] ] = $record[ "capitalCity" ][ "coordinates" ];
		}

		$terms[] = $cur;
	}

	//
	// Build edges.
	//
	$edges = [];
	$ns_country = $namespace;
	$regions = $admins = $incomes = $lendings = [];
	foreach( $records as $record )
	{
		//
		// Init local storage.
		//
		$cats = false;
		$code = $record[ "id" ];

		//
		// Build region edge.
		//
		if( array_key_exists( "region", $record ) )
		{
			if( ! in_array( $record[ "region" ][ "id" ], $regions ) )
			{
				$edge = [];
				$from = "TERMS/" . $theTerms[ "kWB_region" ] . ":"
					  . $record[ "region" ][ "id" ];
				$to = "TERMS/" . $ns_country;
				$predicate = "TERMS/" . $theTerms[ "k_predicate_category_of" ];
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
				$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
				$edges[] = $edge;
				$cats = true;
				$regions[] = $record[ "region" ][ "id" ];
			}

			$edge = [];
			$from = "TERMS/" . "$ns_country:$code";
			$to = "TERMS/" . $theTerms[ "kWB_region" ] . ":" . $record[ "region" ][ "id" ];
			$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ "TERMS/" . $ns_country ];
			$edges[] = $edge;
			$cats = true;
		}

		//
		// Build admin region edge.
		//
		if( array_key_exists( "adminregion", $record ) )
		{
			if( ! in_array( $record[ "adminregion" ][ "id" ], $admins ) )
			{
				$edge = [];
				$from = "TERMS/" . $theTerms[ "kWB_admin" ] . ":" . $record[ "adminregion" ][ "id" ];
				$to = "TERMS/" . $ns_country;
				$predicate = "TERMS/" . $theTerms[ "k_predicate_category_of" ];
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
				$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
				$edges[] = $edge;
				$cats = true;
				$admins[] = $record[ "adminregion" ][ "id" ];
			}

			$edge = [];
			$from = "TERMS/" . "$ns_country:$code";
			$to = "TERMS/" . $theTerms[ "kWB_admin" ] . ":" . $record[ "adminregion" ][ "id" ];
			$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ "TERMS/" . $ns_country ];
			$edges[] = $edge;
			$cats = true;
		}

		//
		// Build income edge.
		//
		$tmp = "incomeLevel";
		if( array_key_exists( $tmp, $record ) )
		{
			if( ! in_array( $record[ $tmp ], $incomes ) )
			{
				$edge = [];
				$from = "TERMS/" . $record[ $tmp ];
				$to = "TERMS/" . $ns_country;
				$predicate = "TERMS/" . $theTerms[ "k_predicate_category_of" ];
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
				$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
				$edges[] = $edge;
				$cats = true;
				$incomes[] = $record[ $tmp ];
			}

			$edge = [];
			$from = "TERMS/" . "$ns_country:$code";
			$to = "TERMS/" . $record[ $tmp ];
			$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ "TERMS/" . $ns_country ];
			$edges[] = $edge;
			$cats = true;
		}

		//
		// Build lending edge.
		//
		$tmp = "lendingType";
		if( array_key_exists( $tmp, $record ) )
		{
			if( ! in_array( $record[ $tmp ], $lendings ) )
			{
				$edge = [];
				$from = "TERMS/" . $record[ $tmp ];
				$to = "TERMS/" . $ns_country;
				$predicate = "TERMS/" . $theTerms[ "k_predicate_category_of" ];
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
				$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
				$edges[] = $edge;
				$cats = true;
				$lendings[] = $record[ $tmp ];
			}

			$edge = [];
			$from = "TERMS/" . "$ns_country:$code";
			$to = "TERMS/" . $record[ $tmp ];
			$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ "TERMS/" . $ns_country ];
			$edges[] = $edge;
			$cats = true;
		}

		//
		// Build country edge.
		//
		if( ! $cats )
		{
			$edge = [];
			$from = "TERMS/" . "$ns_country:$code";
			$to = "TERMS/" . $ns_country;
			$predicate = "TERMS/" . $theTerms[ "k_predicate_enum_of" ];
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
			$edges[] = $edge;
		}

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

} // Country.



?>
