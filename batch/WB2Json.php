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
define( "kStdLink", "http://api.worldbank.org/" );
define( "kLangNS", "ISO:639-3:" );
define( "kDesSrc", "WB:source" );
define( "kDesOrg", "WB:org" );
define( "kDesTop", "WB:topic" );
define( "kDesCountry", "WB:country" );
define( "kDesRegion", "WB:region" );
define( "kDesAdminRegion", "WB:region:admin" );
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
Income( $directory, $standards, $languages );
Lending( $directory, $standards, $languages );
Country( $directory, $standards, $languages );

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
			$cur[ kSymbol ] = $code;
			$cur[ kSynonym ] = [ $code ];

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
			$cur[ kSymbol ] = $code;
			$cur[ kSynonym ] = [ $code ];

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
	$namespace = "WB:" . kStdIndicator;

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
	$language = kLangNS . "eng";
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
			$cur[ kSymbol ] = $code;
			$cur[ kSynonym ] = [ $code ];

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
	foreach( $theLanguages as $lng2 => $lng3 )
	{
		//
		// Set language.
		//
		echo( "\n          $lng2 ");
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
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ kPredicate ] = $predicate;
			$edge[ kBranches ] = [ $term[ kNid ] ];
			$edges[] = $edge;
			$cat = true;

			if( ! in_array( $tmp, $sources ) )
			{
				$edge = [];
				$from = "TERMS/$tmp";
				$to = $term[ kNid ];
				$predicate = ":predicate:category-of";
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ kPredicate ] = $predicate;
				$edge[ kBranches ] = [ $term[ kNid ] ];
				$edges[] = $edge;
				$sources[] = $tmp;
			}
		}

		//
		// Build topics edge.
		//
		if( array_key_exists( kDesTop, $term ) )
		{
			foreach( $term[ kDesTop ] as $topic )
			{
				$edge = [];
				$from = $term[ kId ];
				$to = "TERMS/$topic";
				$predicate = ":predicate:enum-of";
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ kPredicate ] = $predicate;
				$edge[ kBranches ] = [ $term[ kNid ] ];
				$edges[] = $edge;
				$cat = true;

				if( ! in_array( $topic, $topics ) )
				{
					$edge = [];
					$from = "TERMS/$topic";
					$to = $term[ kNid ];
					$predicate = ":predicate:category-of";
					$hash = md5( "$from\t$to\t$predicate" );
					$edge[ kId ] = "SCHEMAS/$hash";
					$edge[ kKey ] = $hash;
					$edge[ kFrom ] = $from;
					$edge[ kTo ] = $to;
					$edge[ kPredicate ] = $predicate;
					$edge[ kBranches ] = [ $term[ kNid ] ];
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
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ kPredicate ] = $predicate;
			$edge[ kBranches ] = [ $to ];
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
 */
function Income( SplFileInfo	$theDirectory,
				 $theStandards,
				 $theLanguages)
{
	//
	// Init local storage.
	//
	$standard = kStdIncome;
	$link = $theStandards[ kStdIncome ];
	$namespace = "WB:" . kStdIncome;

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
	$language = kLangNS . "eng";
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
			$cur[ kSymbol ] = $code;
			$cur[ kSynonym ] = [ $code ];

			//
			// Load label.
			//
			$cur[ kLabel ] = [ $language => $record[ "value" ] ];

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
 */
function Lending( SplFileInfo	$theDirectory,
				  $theStandards,
				  $theLanguages)
{
	//
	// Init local storage.
	//
	$standard = kStdLending;
	$link = $theStandards[ kStdLending ];
	$namespace = "WB:" . kStdLending;

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
	$language = kLangNS . "eng";
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
			$cur[ kSymbol ] = $code;
			$cur[ kSynonym ] = [ $code ];

			//
			// Load label.
			//
			$cur[ kLabel ] = [ $language => $record[ "value" ] ];

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
 */
function Country( SplFileInfo	$theDirectory,
				  $theStandards,
				  $theLanguages)
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
	$language = kLangNS . "eng";
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
			$namespace = "WB:" . kStdIncome;
			$cur[ $namespace ] = "$namespace:" . $record[ "incomeLevel" ][ "id" ];
			$namespace = "WB:" . kStdLending;
			$cur[ $namespace ] = "$namespace:" . $record[ "lendingType" ][ "id" ];

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
					$cur[ "capitalCity" ][ kDesShape ] = [
						"type" => "Point",
						"coordinates" => [
							$record[ "longitude" ],
							$record[ "latitude" ]
						]
					];
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
	$namespace = kDesRegion;
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
					$cur[ kSymbol ] = $code;
					$cur[ kSynonym ] = [ $code ];
					$cur[ kLabel ] = $record[ "region" ][ kLabel ];
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

	//
	// Collect administrative regions.
	//
	$terms = [];
	$standard = kStdAdminRegion;
	$namespace = kDesAdminRegion;
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
					$cur[ kSymbol ] = $code;
					$cur[ kSynonym ] = [ $code ];
					$cur[ kLabel ] = $record[ "region" ][ kLabel ];
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

	//
	// Collect countries.
	//
	$terms = [];
	$standard = kStdCountry;
	$namespace = kDesCountry;
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
		$cur[ kSymbol ] = $code;
		$cur[ kSynonym ] = [ $code ];
		if( array_key_exists( "iso2Code", $record ) )
			$cur[ kSynonym ][] = $record[ "iso2Code" ];
		if( array_key_exists( "WB:" . kStdIncome, $record ) )
			$cur[ "WB:" . kStdIncome ] = $record[ "WB:" . kStdIncome ];
		if( array_key_exists( "WB:" . kStdLending, $record ) )
			$cur[ "WB:" . kStdLending ] = $record[ "WB:" . kStdLending ];
		$cur[ kLabel ] = $record[ kLabel ];
		if( array_key_exists( "capitalCity", $record ) )
			$cur[ kDesCapital ] = $record[ "capitalCity" ];

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
				$from = "TERMS/" . kDesRegion . ":" . $record[ "region" ][ "id" ];
				$to = "TERMS/" . $ns_country;
				$predicate = ":predicate:category-of";
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ kPredicate ] = $predicate;
				$edge[ kBranches ] = [ $to ];
				$edges[] = $edge;
				$cats = true;
				$regions[] = $record[ "region" ][ "id" ];
			}

			$edge = [];
			$from = "TERMS/" . "$ns_country:$code";
			$to = "TERMS/" . kDesRegion . ":" . $record[ "region" ][ "id" ];
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ kPredicate ] = $predicate;
			$edge[ kBranches ] = [ "TERMS/" . $ns_country ];
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
				$from = "TERMS/" . kDesAdminRegion . ":" . $record[ "adminregion" ][ "id" ];
				$to = "TERMS/" . $ns_country;
				$predicate = ":predicate:category-of";
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ kPredicate ] = $predicate;
				$edge[ kBranches ] = [ $to ];
				$edges[] = $edge;
				$cats = true;
				$admins[] = $record[ "adminregion" ][ "id" ];
			}

			$edge = [];
			$from = "TERMS/" . "$ns_country:$code";
			$to = "TERMS/" . kDesAdminRegion . ":" . $record[ "adminregion" ][ "id" ];
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ kPredicate ] = $predicate;
			$edge[ kBranches ] = [ "TERMS/" . $ns_country ];
			$edges[] = $edge;
			$cats = true;
		}

		//
		// Build income edge.
		//
		$tmp = "WB:" . kStdIncome;
		if( array_key_exists( $tmp, $record ) )
		{
			if( ! in_array( $record[ $tmp ], $incomes ) )
			{
				$edge = [];
				$from = "TERMS/" . $record[ $tmp ];
				$to = "TERMS/" . $ns_country;
				$predicate = ":predicate:category-of";
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ kPredicate ] = $predicate;
				$edge[ kBranches ] = [ $to ];
				$edges[] = $edge;
				$cats = true;
				$incomes[] = $record[ $tmp ];
			}

			$edge = [];
			$from = "TERMS/" . "$ns_country:$code";
			$to = "TERMS/" . $record[ $tmp ];
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ kPredicate ] = $predicate;
			$edge[ kBranches ] = [ "TERMS/" . $ns_country ];
			$edges[] = $edge;
			$cats = true;
		}

		//
		// Build lending edge.
		//
		$tmp = "WB:" . kStdLending;
		if( array_key_exists( $tmp, $record ) )
		{
			if( ! in_array( $record[ $tmp ], $lendings ) )
			{
				$edge = [];
				$from = "TERMS/" . $record[ $tmp ];
				$to = "TERMS/" . $ns_country;
				$predicate = ":predicate:category-of";
				$hash = md5( "$from\t$to\t$predicate" );
				$edge[ kId ] = "SCHEMAS/$hash";
				$edge[ kKey ] = $hash;
				$edge[ kFrom ] = $from;
				$edge[ kTo ] = $to;
				$edge[ kPredicate ] = $predicate;
				$edge[ kBranches ] = [ $to ];
				$edges[] = $edge;
				$cats = true;
				$lendings[] = $record[ $tmp ];
			}

			$edge = [];
			$from = "TERMS/" . "$ns_country:$code";
			$to = "TERMS/" . $record[ $tmp ];
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ kPredicate ] = $predicate;
			$edge[ kBranches ] = [ "TERMS/" . $ns_country ];
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
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ kPredicate ] = $predicate;
			$edge[ kBranches ] = [ $to ];
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
