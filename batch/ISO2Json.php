<?php

/**
 * ISO2Json.php
 *
 * This file contains the script to write the JSON files from the ISO database using the
 * SMART standards, usage:
 *
 * <code>
 * php -f ISO2Json.php <connection URI> <json output directory>
 * </code>
 *
 * <ul>
 * 	<li><b>connection URI</b>: Connection URI.
 * 	<li><b>json directory</b>: Path to the output directory for JSON files.
 * </ul>
 *
 * Note: The connected database is expected to have a collection names "DESCRIPTORS" which
 * contains the current descriptors with the "constant" field.
 *
 * The list of generated files will be the following:
 *
 * <ul>
 * 	<li>ISO_15924
 * 	<li>ISO_3166-1
 * 	<li>ISO_3166-2
 * 	<li>ISO_3166-3
 * 	<li>ISO_4217
 * 	<li>ISO_639-2
 * 	<li>ISO_639-3
 * 	<li>ISO_639-5
 * </ul>
 *
 * <em>All files will be overwritten.</em>
 *
 * @example
 * <code>
 * // Load from the ISO ArangoDB database.
 * php -f "batch/ISO2Json.php" "tcp://localhost:8529/ISO" "output/JSON"
 *
 * // Load from the ISO MongoDB database.
 * php -f "batch/ISO2Json.php" "mongodb://localhost:27017/ISO" "output/JSON"
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

//
// Constants.
//
define( "kLangNS", "ISO:639-3:" );
define( "kLanguage", "ISO:639-3:eng" );
define( "kLocaleNS", "ISO:639:local" );
define( "kLocaleSTD", "639-locale" );
define( "kLocale", "ISO:639:local:" );
define( "kSubdivision", "ISO:3166:type");
define( "kCountry", "ISO:3166-1");
define( "kWithdrawal", "ISO:withdrawal" );
define( "kBiblio", "ISO:639:biblio" );
define( "kScope", "ISO:639:scope" );
define( "kType", "ISO:639:type" );
define( "kDeployStandard", ":state:implementation:standard" );
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
define( "kBranches", "branches" );

//
// Globals.
//
$locales = [];

/*=======================================================================================
 *																						*
 *											MAIN										*
 *																						*
 *======================================================================================*/

//
// Check arguments.
//
if( $argc < 3 )
	exit( "Usage: php -f ISO2Json.php <connection URI> <output json directory>\n" );

//
// Get arguments.
//
$c = $argv[ 1 ];
$j = $argv[ 2 ];

//
// Check output directory.
//
$directory = new SplFileInfo( $j );
if( ! $directory->isDir() )
	exit( "Output directory is not a directory.\n" );
elseif( ! $directory->isWritable() )
	exit( "Output directory is not writable.\n" );

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
// Connect database.
//
$database->Connect();

//
// Load descriptors.
//
$descriptors = DESCRIPTORS( $database );

//
// Connect languages collection.
//
$languages = $database->Client( "ISO_" . ISOCodes::k639_3, [] );
$languages->Connect();

//
// Handle standards.
//
$locales = array_merge( $locales, ISO_4217( $database, $languages, $directory, $descriptors ) );
$locales = array_merge( $locales, ISO_15924( $database, $languages, $directory, $descriptors ) );
$locales = array_merge( $locales, ISO_3166_1( $database, $languages, $directory, $descriptors ) );
$locales = array_merge( $locales, ISO_3166_2( $database, $languages, $directory, $descriptors ) );
$locales = array_merge( $locales, ISO_3166_3( $database, $languages, $directory, $descriptors ) );
$locales = array_merge( $locales, ISO_639_2( $database, $languages, $directory, $descriptors ) );
$locales = array_merge( $locales, ISO_639_3( $database, $languages, $directory, $descriptors ) );
$locales = array_merge( $locales, ISO_639_5( $database, $languages, $directory, $descriptors ) );

//
// Connect language locales collection.
//
$languages = $database->Client( "ISO_639-local", [] );
$languages->Connect();

//
// Handle locales.
//
ISO_639_Locales( $database, $languages, $directory, array_unique( $locales ), $descriptors );

echo( "\nDone!\n" );



/*=======================================================================================
 *																						*
 *									GLOBAL HANDLERS	  				  					*
 *																						*
 *======================================================================================*/



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
	echo( "DESCRIPTorS\n" );

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
 *	ISO 4217																		*
 *==================================================================================*/

/**
 * <h4>Handle ISO 4217.</h4><p />
 *
 * This method will generate the ISO 4217 terms and schema.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 * @param \Milko\Wrapper\Client			$theLanguages	 	Languages collection.
 * @param SplFileInfo					$theDirectory	 	Output directory.
 * @param array							$theDescriptors	 	Descriptors list.
 *
 * @return array						List of locales.
 */
function ISO_4217( \Milko\Wrapper\ClientServer	$theDatabase,
				   \Milko\Wrapper\Client		$theLanguages,
				   SplFileInfo					$theDirectory,
												$theDescriptors)
{
	//
	// Init local storage.
	//
	$locales = [];
	$standard = ISOCodes::k4217;
	$namespace = "ISO:$standard";
	$enumeration = "TERMS/$namespace";
	$collection = $theDatabase->Client( "ISO_$standard", [] );
	$collection->Connect();
	$db_key = $collection->DocumentKey();

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Create terms.
	//
	$edges = [];
	$buffer = [];
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Init loop storage.
		//
		$edge = [];
		$record = [];

		//
		// Load record.
		//
		$key = $input[ $db_key ];
		$record[ kId ] = "TERMS/$namespace:$key";
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = $enumeration;
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ $theDescriptors[ "kSymbol" ] ] = $key;
		$record[ $theDescriptors[ "kSynonym" ] ] = $input[ kSynonym ];
		if( array_key_exists( "ISO:4217:fraction", $input ) )
			$record[ $theDescriptors[ "kISO_4217_fraction" ] ] = $input[ "ISO:4217:fraction" ];
		$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;

		//
		// Load labels.
		//
		$record[ $theDescriptors[ "kLabel" ] ] = [];
		foreach( $input[ "name" ] as $lang => $name )
		{
			//
			// Check language by length.
			//
			switch (strlen( $lang ) )
			{
				case 2:
					$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
					if ( ! count( $match ) )
						throw new Exception("Unable to resolve [$lang] language.");
					if( count( $match ) > 1 )
						throw new Exception("Ambiguous language [$lang] in record [$key].");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ $theDescriptors[ "kLabel" ] ][ $lang ] = $name;
					break;
			}
		}

		//
		// Append to buffer.
		//
		$buffer[] = $record;

		//
		// Append to edges.
		//
		$from = "TERMS/" . $record[ kKey ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
		$edges[] = $edge;

	} // Iterate all records.

	//
	// Write TERMS JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_$standard.json";
	$data = json_encode( $buffer, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	return array_unique( $locales );												// ==>

} // ISO_4217.


/*===================================================================================
 *	ISO 15924																		*
 *==================================================================================*/

/**
 * <h4>Handle ISO 15924.</h4><p />
 *
 * This method will generate the ISO 15924 terms and schema.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 * @param \Milko\Wrapper\Client			$theLanguages	 	Languages collection.
 * @param SplFileInfo					$theDirectory	 	Output directory.
 * @param array							$theDescriptors	 	Descriptors list.
 *
 * @returs array						List of locales.
 */
function ISO_15924( \Milko\Wrapper\ClientServer	$theDatabase,
                    \Milko\Wrapper\Client		$theLanguages,
                    SplFileInfo					$theDirectory,
												$theDescriptors)
{
	//
	// Init local storage.
	//
	$locales = [];
	$standard = ISOCodes::k15924;
	$namespace = "ISO:$standard";
	$enumeration = "TERMS/$namespace";
	$collection = $theDatabase->Client( "ISO_$standard", [] );
	$collection->Connect();
	$db_key = $collection->DocumentKey();

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Create terms.
	//
	$edges = [];
	$buffer = [];
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Init loop storage.
		//
		$edge = [];
		$record = [];

		//
		// Load record.
		//
		$key = $input[ $db_key ];
		$record[ kId ] = "TERMS/$namespace:$key";
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = $enumeration;
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ $theDescriptors[ "kSymbol" ] ] = $key;
		$record[ $theDescriptors[ "kSynonym" ] ] = $input[ kSynonym ];
		$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;

		//
		// Load labels.
		//
		$record[ $theDescriptors[ "kLabel" ] ] = [];
		foreach( $input[ "name" ] as $lang => $name )
		{
			//
			// Check language by length.
			//
			switch (strlen( $lang ) )
			{
				case 2:
					$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
					if ( ! count( $match ) )
						throw new Exception("Unable to resolve [$lang] language.");
					if( count( $match ) > 1 )
						throw new Exception("Ambiguous language [$lang] in record [$key].");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ $theDescriptors[ "kLabel" ] ][ $lang ] = $name;
					break;
			}
		}

		//
		// Append to buffer.
		//
		$buffer[] = $record;

		//
		// Append to edges.
		//
		$from = "TERMS/" . $record[ kKey ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches"  ] ] = [ $to ];
		$edges[] = $edge;

	} // Iterate all records.

	//
	// Write TERMS JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_$standard.json";
	$data = json_encode( $buffer, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	return array_unique( $locales );												// ==>

} // ISO_15924.


/*===================================================================================
 *	ISO 3166-1																		*
 *==================================================================================*/

/**
 * <h4>Handle ISO 3166-1.</h4><p />
 *
 * This method will generate the ISO 3166-1 terms and schema.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 * @param \Milko\Wrapper\Client			$theLanguages	 	Languages collection.
 * @param SplFileInfo					$theDirectory	 	Output directory.
 * @param array							$theDescriptors	 	Descriptors list.
 *
 * @returs array						List of locales.
 */
function ISO_3166_1( \Milko\Wrapper\ClientServer	$theDatabase,
					 \Milko\Wrapper\Client			$theLanguages,
					 SplFileInfo					$theDirectory,
					 								$theDescriptors)
{
	//
	// Init local storage.
	//
	$locales = [];
	$standard = ISOCodes::k3166_1;
	$namespace = "ISO:$standard";
	$collection = $theDatabase->Client( "ISO_$standard", [] );
	$collection->Connect();
	$db_key = $collection->DocumentKey();

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Create terms.
	//
	$edges = [];
	$buffer = [];
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Init loop storage.
		//
		$edge = [];
		$record = [];

		//
		// Load record.
		//
		$key = $input[ $db_key ];
		$record[ kId ] = "TERMS/$namespace:$key";
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = "TERMS/$namespace";
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ $theDescriptors[ "kSymbol" ] ] = $key;
		$record[ $theDescriptors[ "kSynonym" ] ] = $input[ kSynonym ];
		$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;

		//
		// Load labels.
		//
		$record[ $theDescriptors[ "kLabel" ] ] = [];
		foreach( $input[ "name" ] as $lang => $name )
		{
			//
			// Check language by length.
			//
			switch (strlen( $lang ) )
			{
				case 2:
					$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
					if ( ! count( $match ) )
						throw new Exception("Unable to resolve [$lang] language.");
					if( count( $match ) > 1 )
						throw new Exception("Ambiguous language [$lang] in record [$key].");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ $theDescriptors[ "kLabel" ] ][ $lang ] = $name;
					break;
			}
		}

		//
		// Load official name.
		//
		if( array_key_exists( "official_name", $input ) )
		{
			$record[ $theDescriptors[ "kDefinition" ] ] = [];
			foreach( $input[ "official_name" ] as $lang => $name )
			{
				//
				// Check language by length.
				//
				switch (strlen( $lang ) )
				{
					case 2:
						$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
						if ( ! count( $match ) )
							throw new Exception("Unable to resolve [$lang] language.");
						if( count( $match ) > 1 )
							throw new Exception("Ambiguous language [$lang] in record [$key].");
						$record[ $theDescriptors[ "kDefinition" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
						break;

					case 3:
						$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
						if ( ! $match )
							throw new Exception("Unable to resolve [$lang] language.");
						$record[ $theDescriptors[ "kDefinition" ] ][ kLangNS . $match[ $db_key ] ] = $name;
						break;

					default:
						$lang = kLocale . $lang;
						$locales[] = $lang;
						$record[ $theDescriptors[ "kDefinition" ] ][ $lang ] = $name;
						break;
				}
			}
		}

		//
		// Load common name.
		//
		if( array_key_exists( "common_name", $input ) )
		{
			$field = ( array_key_exists( $theDescriptors[ "kDefinition" ], $record ) )
				   ? $theDescriptors[ "kDescription" ]
				   : $theDescriptors[ "kDefinition" ];
			$record[ $field ] = [];
			foreach( $input[ "common_name" ] as $lang => $name )
			{
				//
				// Normalise name.
				//
				if( $field == kDescription )
					$name = htmlspecialchars( $name );
				//
				// Check language by length.
				//
				switch (strlen( $lang ) )
				{
					case 2:
						$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
						if ( ! count( $match ) )
							throw new Exception("Unable to resolve [$lang] language.");
						if( count( $match ) > 1 )
							throw new Exception("Ambiguous language [$lang] in record [$key].");
						$record[ $field ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
						break;

					case 3:
						$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
						if ( ! $match )
							throw new Exception("Unable to resolve [$lang] language.");
						$record[ $field ][ kLangNS . $match[ $db_key ] ] = $name;
						break;

					default:
						$lang = kLocale . $lang;
						$locales[] = $lang;
						$record[ $field ][ $lang ] = $name;
						break;
				}
			}
		}

		//
		// Append to buffer.
		//
		$buffer[] = $record;

		//
		// Append to edges.
		//
		$from = "TERMS/" . $record[ kKey ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
		$edges[] = $edge;

	} // Iterate all records.

	//
	// Write JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_$standard.json";
	$data = json_encode( $buffer, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	return array_unique( $locales );												// ==>

} // ISO_3166_1.


/*===================================================================================
 *	ISO 3166-2																		*
 *==================================================================================*/

/**
 * <h4>Handle ISO 3166-2.</h4><p />
 *
 * This method will generate the ISO 3166-2 terms and schema.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 * @param \Milko\Wrapper\Client			$theLanguages	 	Languages collection.
 * @param SplFileInfo					$theDirectory	 	Output directory.
 * @param array							$theDescriptors	 	Descriptors list.
 *
 * @return array						List of locales.
 */
function ISO_3166_2( \Milko\Wrapper\ClientServer	$theDatabase,
					 \Milko\Wrapper\Client			$theLanguages,
					 SplFileInfo					$theDirectory,
													$theDescriptors)
{
	//
	// Init local storage.
	//
	$locales = [];
	$standard = ISOCodes::k3166_2;
	$namespace = "ISO:$standard";
	$enumeration = "TERMS/$namespace";
	$collection = $theDatabase->Client( "ISO_$standard", [] );
	$collection->Connect();
	$countries = $theDatabase->Client( "ISO_" . ISOCodes::k3166_1, [] );
	$countries->Connect();
	$db_key = $collection->DocumentKey();

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Create terms.
	//
	$types = [];
	$edges = [];
	$buffer = [];
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Init loop storage.
		//
		$record = [];

		//
		// Parse code.
		//
		$tmp = explode( "-", $input[ $db_key ] );
		$country = $tmp[ 0 ];
		$code = $tmp[ 1 ];

		//
		// Load record.
		//
		$key = $input[ $db_key ];
		$record[ kId ] = "TERMS/$namespace:$key";
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = "TERMS/$namespace";
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ $theDescriptors[ "kSymbol" ] ] = $key;
		$record[ $theDescriptors[ "kSynonym" ] ] = $input[ kSynonym ];
		$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;

		//
		// Handle subdivision type.
		//
		$name = [];
		$tmp = explode(" ", $input[ "type" ] );
		foreach( $tmp as $i => $n ) {
			$string = trim( $n );
			if( $i == 0 )
				$string = ucfirst( $string );
			else
				$string = strtolower( $string );
			$name[] = $string; }
		$types[] = implode(" ", $name );
		$record[ $theDescriptors[ "kSTD_geo_admin" ] ] = implode(" ", $name );

		//
		// Load labels.
		//
		$record[ $theDescriptors[ "kLabel" ] ] = [];
		foreach( $input[ "name" ] as $lang => $name )
		{
			//
			// Check language by length.
			//
			switch (strlen( $lang ) )
			{
				case 2:
					$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
					if ( ! count( $match ) )
						throw new Exception("Unable to resolve [$lang] language.");
					if( count( $match ) > 1 )
						throw new Exception("Ambiguous language [$lang] in record [$key].");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ $theDescriptors[ "kLabel" ] ][ $lang ] = $name;
					break;
			}
		}

		//
		// Handle parent.
		//
		if( array_key_exists( "parent", $input ) )
			$record[ "__parent__" ] = $country . "-" . $input[ "parent" ];

		//
		// Init loop storage.
		//
		$edge = [];

		//
		// Append to edges.
		//
		$from = "TERMS/" . $record[ kKey ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $enumeration ];
		$edges[] = $edge;

		//
		// Init loop storage.
		//
		$edge = [];

		//
		// Append to parent.
		//
		if( array_key_exists( "__parent__", $record ) )
		{
			$from = "TERMS/" . $record[ kKey ];
			$to = "TERMS/$namespace:" . $record[ "__parent__" ];
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ $enumeration ];
			$edges[] = $edge;
			unset( $record[ "__parent__" ] );
		}

		//
		// Append to country.
		//
		else
		{
			$match = $countries->Connection()->findOne( [ "alpha_2" => $country ] );
			if ( ! $match )
				throw new Exception("Unable to resolve [$country] country in [$key].");
			$from = "TERMS/" . $record[ kKey ];
			$to = "TERMS/" . kCountry . ":" . $match[ $db_key ];
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ $enumeration ];
			$edges[] = $edge;
		}

		//
		// Append to buffer.
		//
		$buffer[] = $record;

	} // Iterate all records.

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Create subdivisions enumeration.
	//
	$types = array_unique( $types );
	sort( $types );

	//
	// Create subdivision type terms.
	//
	$tmp1 = [];
	$tmp2 = [];
	foreach( $types as $i => $n )
	{
		//
		// Build term.
		//
		$record = [];
		$index = sprintf( "%02d", $i + 1 );
		$key = kSubdivision . ":$index";
		$record[ kId ] = "TERMS/$key";
		$record[ kKey ] = $key;
		$record[ kNid ] = "TERMS/" . kSubdivision;
		$record[ kLid ] = $index;
		$record[ kGid ] = $record[ kKey ];
		$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;
		$record[ $theDescriptors[ "kLabel" ] ] = [ kLanguage => $n ];
		$tmp1[] = $record;

		//
		// Build edge.
		//
		$from = $record[ kId ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$record = [];
		$record[ kId ] = "SCHEMAS/$hash";
		$record[ kKey ] = $hash;
		$record[ kFrom ] = $from;
		$record[ kTo ] = $to;
		$record[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$record[ $theDescriptors[ "kBranches" ] ] = [ $to ];
		$tmp2[] = $record;
	}

	//
	// Write terms.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_3166_types.json";
	$data = json_encode( $tmp1, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write edges.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_3166_types.json";
	$data = json_encode( $tmp2, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Refactor subdivision types.
	//
	foreach( $buffer as $i => $record )
	{
		if( array_key_exists( $theDescriptors[ "kCategory" ], $record ) )
		{
			$tmp = $record[ $theDescriptors[ "kCategory" ] ];
			$index = array_search($tmp, $types );
			if( $index === false )
				throw new Exception("Unable to match subdivision [$tmp].");
			$record[ $theDescriptors[ "kCategory" ] ] = kSubdivision . ":" . sprintf( "%02d", $index + 1 );
			$buffer[ $i ] = $record;
		}
	}

	//
	// Write JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_$standard.json";
	$data = json_encode( $buffer, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	return array_unique( $locales );												// ==>

} // ISO_3166_2.


/*===================================================================================
 *	ISO 3166-3																		*
 *==================================================================================*/

/**
 * <h4>Handle ISO 3166-3.</h4><p />
 *
 * This method will generate the ISO 3166-3 terms and schema.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 * @param \Milko\Wrapper\Client			$theLanguages	 	Languages collection.
 * @param SplFileInfo					$theDirectory	 	Output directory.
 * @param array							$theDescriptors	 	Descriptors list.
 *
 * @return array						List of locales.
 */
function ISO_3166_3( \Milko\Wrapper\ClientServer	$theDatabase,
					 \Milko\Wrapper\Client			$theLanguages,
					 SplFileInfo					$theDirectory,
													$theDescriptors)
{
	//
	// Init local storage.
	//
	$locales = [];
	$standard = ISOCodes::k3166_3;
	$namespace = "ISO:$standard";
	$collection = $theDatabase->Client( "ISO_$standard", [] );
	$collection->Connect();
	$db_key = $collection->DocumentKey();

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Create terms.
	//
	$edges = [];
	$buffer = [];
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Init loop storage.
		//
		$edge = [];
		$record = [];

		//
		// Load record.
		//
		$key = $input[ $db_key ];
		$record[ kId ] = "TERMS/$namespace:$key";
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = "TERMS/$namespace";
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ $theDescriptors[ "kSymbol" ] ] = $key;
		$record[ $theDescriptors[ "kSynonym" ] ] = $input[ kSynonym ];
		$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;

		//
		// Handle withdrawal date.
		//
		if( array_key_exists( "withdrawal_date", $input ) )
		{
			$tmp = explode("-", $input[ "withdrawal_date" ] );
			$record[ $theDescriptors[ "kTDate" ] ] = implode("", $tmp );
		}

		//
		// Load labels.
		//
		$record[ $theDescriptors[ "kLabel" ] ] = [];
		foreach( $input[ "name" ] as $lang => $name )
		{
			//
			// Check language by length.
			//
			switch (strlen( $lang ) )
			{
				case 2:
					$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
					if ( ! count( $match ) )
						throw new Exception("Unable to resolve [$lang] language.");
					if( count( $match ) > 1 )
						throw new Exception("Ambiguous language [$lang] in record [$key].");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ $theDescriptors[ "kLabel" ] ][ $lang ] = $name;
					break;
			}
		}

		//
		// Load comment.
		//
		if( array_key_exists( "comment", $input ) )
			$record[ $theDescriptors[ "kNote" ] ][ kLanguage ] = htmlspecialchars( $input[ "comment" ] );

		//
		// Append to buffer.
		//
		$buffer[] = $record;

		//
		// Append to edges.
		//
		$from = "TERMS/" . $record[ kKey ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
		$edges[] = $edge;

	} // Iterate all records.

	//
	// Write JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_$standard.json";
	$data = json_encode( $buffer, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	return array_unique( $locales );												// ==>

} // ISO_3166_3.


/*===================================================================================
 *	ISO 639-2																		*
 *==================================================================================*/

/**
 * <h4>Handle ISO 639-2.</h4><p />
 *
 * This method will generate the ISO 15924 terms and schema.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 * @param \Milko\Wrapper\Client			$theLanguages	 	Languages collection.
 * @param SplFileInfo					$theDirectory	 	Output directory.
 * @param array							$theDescriptors	 	Descriptors list.
 *
 * @return array						List of locales.
 */
function ISO_639_2( \Milko\Wrapper\ClientServer	$theDatabase,
                    \Milko\Wrapper\Client		$theLanguages,
                    SplFileInfo					$theDirectory,
												$theDescriptors)
{
	//
	// Init local storage.
	//
	$locales = [];
	$standard = ISOCodes::k639_2;
	$namespace = "ISO:$standard";
	$enumeration = "TERMS/$namespace";
	$collection = $theDatabase->Client( "ISO_$standard", [] );
	$collection->Connect();
	$db_key = $collection->DocumentKey();

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Create terms.
	//
	$edges = [];
	$buffer = [];
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Skip if already in ISO 639-3.
		//
		$key = $input[ $db_key ];
		$match = $theLanguages->Connection()->findOne( [ $db_key => $key ] );
		if( ! $match )
		{
			//
			// Init loop storage.
			//
			$edge = [];
			$record = [];

			//
			// Load record.
			//
			$record[ kId ] = "TERMS/$namespace:$key";
			$record[ kKey ] = "$namespace:$key";
			$record[ kNid ] = $enumeration;
			$record[ kLid ] = $key;
			$record[ kGid ] = $record[ kKey ];
			$record[ $theDescriptors[ "kSymbol" ] ] = $key;
			$record[ $theDescriptors[ "kSynonym" ] ] = $input[ kSynonym ];
			$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;
			if( array_key_exists("bibliographic", $input ) )
				$record[ $theDescriptors[ "kISO_639_biblio" ] ] = $input[ "bibliographic" ];

			//
			// Load labels.
			//
			$record[ $theDescriptors[ "kLabel" ] ] = [];
			foreach( $input[ "name" ] as $lang => $name )
			{
				//
				// Check language by length.
				//
				switch (strlen( $lang ) )
				{
					case 2:
						$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
						if ( ! count( $match ) )
							throw new Exception("Unable to resolve [$lang] language.");
						if( count( $match ) > 1 )
							throw new Exception("Ambiguous language [$lang] in record [$key].");
						$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
						break;

					case 3:
						$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
						if ( ! $match )
							throw new Exception("Unable to resolve [$lang] language.");
						$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ $db_key ] ] = $name;
						break;

					default:
						$lang = kLocale . $lang;
						$locales[] = $lang;
						$record[ $theDescriptors[ "kLabel" ] ][ $lang ] = $name;
						break;
				}
			}

			//
			// Load common names.
			//
			if( array_key_exists( "common_name", $input ) )
			{
				$record[ kDefinition ] = [];
				foreach( $input[ "common_name" ] as $lang => $name )
				{
					//
					// Check language by length.
					//
					switch (strlen( $lang ) )
					{
						case 2:
							$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
							if ( ! count( $match ) )
								throw new Exception("Unable to resolve [$lang] language.");
							if( count( $match ) > 1 )
								throw new Exception("Ambiguous language [$lang] in record [$key].");
							$record[ $theDescriptors[ "kDefinition" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
							break;

						case 3:
							$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
							if ( ! $match )
								throw new Exception("Unable to resolve [$lang] language.");
							$record[ $theDescriptors[ "kDefinition" ] ][ kLangNS . $match[ $db_key ] ] = $name;
							break;

						default:
							$lang = kLocale . $lang;
							$locales[] = $lang;
							$record[ $theDescriptors[ "kDefinition" ] ][ $lang ] = $name;
							break;
					}
				}
			}

			//
			// Append to buffer.
			//
			$buffer[] = $record;

			//
			// Append to edges.
			//
			$from = "TERMS/" . $record[ kKey ];
			$to = $record[ kNid ];
			$predicate = ":predicate:enum-of";
			$hash = md5( "$from\t$to\t$predicate" );
			$edge[ kId ] = "SCHEMAS/$hash";
			$edge[ kKey ] = $hash;
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
			$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
			$edges[] = $edge;
		}

	} // Iterate all records.

	//
	// Write TERMS JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_$standard.json";
	$data = json_encode( $buffer, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	return array_unique( $locales );												// ==>

} // ISO_639_2.


/*===================================================================================
 *	ISO 639-3																		*
 *==================================================================================*/

/**
 * <h4>Handle ISO 639-3.</h4><p />
 *
 * This method will generate the ISO 639-3 terms and schema.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 * @param \Milko\Wrapper\Client			$theLanguages	 	Languages collection.
 * @param SplFileInfo					$theDirectory	 	Output directory.
 * @param array							$theDescriptors	 	Descriptors list.
 *
 * @return array						List of locales.
 */
function ISO_639_3( \Milko\Wrapper\ClientServer	$theDatabase,
                    \Milko\Wrapper\Client		$theLanguages,
                    SplFileInfo					$theDirectory,
												$theDescriptors)
{
	//
	// Init local storage.
	//
	$locales = [];
	$standard = ISOCodes::k639_3;
	$namespace = "ISO:$standard";
	$enumeration = "TERMS/$namespace";
	$collection = $theDatabase->Client( "ISO_$standard", [] );
	$collection->Connect();
	$db_key = $collection->DocumentKey();

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Create terms.
	//
	$edges = [];
	$buffer = [];
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Init loop storage.
		//
		$edge = [];
		$record = [];

		//
		// Load record.
		//
		$key = $input[ $db_key ];
		$record[ kId ] = "TERMS/$namespace:$key";
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = $enumeration;
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ $theDescriptors[ "kSymbol" ] ] = $key;
		$record[ $theDescriptors[ "kSynonym" ] ] = $input[ kSynonym ];
		$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;
		if( array_key_exists("bibliographic", $input ) )
			$record[ $theDescriptors[ "kISO_639_biblio" ] ] = $input[ "bibliographic" ];
		if( array_key_exists("scope", $input ) )
			$record[ $theDescriptors[ "kISO_639_scope" ] ] = kScope . ":" . $input[ "scope" ];
		if( array_key_exists("type", $input ) )
			$record[ $theDescriptors[ "kISO_639_type" ] ] = kType . ":" . $input[ "type" ];

		//
		// Load labels.
		//
		$record[ $theDescriptors[ "kLabel" ] ] = [];
		foreach( $input[ "name" ] as $lang => $name )
		{
			//
			// Check language by length.
			//
			switch (strlen( $lang ) )
			{
				case 2:
					$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
					if ( ! count( $match ) )
						throw new Exception("Unable to resolve [$lang] language.");
					if( count( $match ) > 1 )
						throw new Exception("Ambiguous language [$lang] in record [$key].");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ $theDescriptors[ "kLabel" ] ][ $lang ] = $name;
					break;
			}
		}

		//
		// Load common names.
		//
		if( array_key_exists( "common_name", $input ) )
		{
			$record[ $theDescriptors[ "kDefinition" ] ] = [];
			foreach( $input[ "common_name" ] as $lang => $name )
			{
				//
				// Check language by length.
				//
				switch (strlen( $lang ) )
				{
					case 2:
						$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
						if ( ! count( $match ) )
							throw new Exception("Unable to resolve [$lang] language.");
						if( count( $match ) > 1 )
							throw new Exception("Ambiguous language [$lang] in record [$key].");
						$record[ $theDescriptors[ "kDefinition" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
						break;

					case 3:
						$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
						if ( ! $match )
							throw new Exception("Unable to resolve [$lang] language.");
						$record[ $theDescriptors[ "kDefinition" ] ][ kLangNS . $match[ $db_key ] ] = $name;
						break;

					default:
						$lang = kLocale . $lang;
						$locales[] = $lang;
						$record[ $theDescriptors[ "kDefinition" ] ][ $lang ] = $name;
						break;
				}
			}
		}

		//
		// Load inverted name.
		//
		if( array_key_exists( "inverted_name", $input ) )
		{
			$field = ( array_key_exists( $theDescriptors[ "kDefinition" ], $record ) )
				   ? $theDescriptors[ "kDescription" ]
				   : $theDescriptors[ "kDefinition" ];
			$record[ $field ] = [];
			foreach( $input[ "inverted_name" ] as $lang => $name )
			{
				//
				// Normalise name.
				//
				if( $field == $theDescriptors[ "kDescription" ] )
					$name = htmlspecialchars( $name );
				//
				// Check language by length.
				//
				switch (strlen( $lang ) )
				{
					case 2:
						$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
						if ( ! count( $match ) )
							throw new Exception("Unable to resolve [$lang] language.");
						if( count( $match ) > 1 )
							throw new Exception("Ambiguous language [$lang] in record [$key].");
						$record[ $field ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
						break;

					case 3:
						$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
						if ( ! $match )
							throw new Exception("Unable to resolve [$lang] language.");
						$record[ $field ][ kLangNS . $match[ $db_key ] ] = $name;
						break;

					default:
						$lang = kLocale . $lang;
						$locales[] = $lang;
						$record[ $field ][ $lang ] = $name;
						break;
				}
			}
		}

		//
		// Append to buffer.
		//
		$buffer[] = $record;

		//
		// Append to edges.
		//
		$from = "TERMS/" . $record[ kKey ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
		$edges[] = $edge;

	} // Iterate all records.

	//
	// Write TERMS JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_$standard.json";
	$data = json_encode( $buffer, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	return array_unique( $locales );												// ==>

} // ISO_639_3.


/*===================================================================================
 *	ISO 639-5																		*
 *==================================================================================*/

/**
 * <h4>Handle ISO 639-5.</h4><p />
 *
 * This method will generate the ISO 639-5 terms and schema.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 * @param \Milko\Wrapper\Client			$theLanguages	 	Languages collection.
 * @param SplFileInfo					$theDirectory	 	Output directory.
 * @param array							$theDescriptors	 	Descriptors list.
 *
 * @return array						List of locales.
 */
function ISO_639_5( \Milko\Wrapper\ClientServer	$theDatabase,
					\Milko\Wrapper\Client		$theLanguages,
					SplFileInfo					$theDirectory,
												$theDescriptors)
{
	//
	// Init local storage.
	//
	$locales = [];
	$standard = ISOCodes::k639_5;
	$namespace = "ISO:$standard";
	$enumeration = "TERMS/$namespace";
	$collection = $theDatabase->Client( "ISO_$standard", [] );
	$collection->Connect();
	$db_key = $collection->DocumentKey();

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Create terms.
	//
	$edges = [];
	$buffer = [];
	foreach( $collection->Connection()->find( [] ) as $input )
	{
		//
		// Init loop storage.
		//
		$edge = [];
		$record = [];

		//
		// Load record.
		//
		$key = $input[ $db_key ];
		$record[ kId ] = "TERMS/$namespace:$key";
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = $enumeration;
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ $theDescriptors[ "kSymbol" ] ] = $key;
		$record[ $theDescriptors[ "kSynonym" ] ] = $input[ kSynonym ];
		$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;

		//
		// Load labels.
		//
		$record[ $theDescriptors[ "kLabel" ] ] = [];
		foreach( $input[ "name" ] as $lang => $name )
		{
			//
			// Check language by length.
			//
			switch (strlen( $lang ) )
			{
				case 2:
					$match = $theLanguages->Connection()->find(["alpha_2" => $lang])->toArray();
					if ( ! count( $match ) )
						throw new Exception("Unable to resolve [$lang] language.");
					if( count( $match ) > 1 )
						throw new Exception("Ambiguous language [$lang] in record [$key].");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ $theDescriptors[ "kLabel" ] ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ $theDescriptors[ "kLabel" ] ][ $lang ] = $name;
					break;
			}
		}

		//
		// Append to buffer.
		//
		$buffer[] = $record;

		//
		// Append to edges.
		//
		$from = "TERMS/" . $record[ kKey ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
		$edges[] = $edge;

	} // Iterate all records.

	//
	// Write TERMS JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_$standard.json";
	$data = json_encode( $buffer, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	return array_unique( $locales );												// ==>

} // ISO_639_5.


/*===================================================================================
 *	ISO 639 Locales																	*
 *==================================================================================*/

/**
 * <h4>Handle ISO 639 Locales.</h4><p />
 *
 * This method will generate the ISO 639 locales terms and schema.
 *
 * @param \Milko\Wrapper\ClientServer	$theDatabase	 	Database.
 * @param \Milko\Wrapper\Client			$theLanguages	 	Output languages collection.
 * @param SplFileInfo					$theDirectory	 	Output directory.
 * @param array							$theLocales		 	List of locale codes.
 * @param array							$theDescriptors	 	Descriptors list.
 */
function ISO_639_Locales( \Milko\Wrapper\ClientServer	$theDatabase,
						  \Milko\Wrapper\Client			$theLanguages,
						  SplFileInfo					$theDirectory,
														$theLocales,
														$theDescriptors)
{
	//
	// Init local storage.
	//
	$standard = kLocaleSTD;
	$namespace = kLocaleNS;

	//
	// Inform.
	//
	echo( "$standard\n" );

	//
	// Create terms.
	//
	$edges = [];
	$buffer = [];
	foreach( $theLocales as $input )
	{
		//
		// Init loop storage.
		//
		$edge = [];
		$record = [];

		//
		// Parse code.
		//
		$tmp = explode( ":", $input );
		$code = $tmp[ 3 ];
		$tmp = explode( '_', $code );
		$code_alt = ( count( $tmp ) == 2 )
				  ? ($tmp[0] . "-" . $tmp[1])
				  : NULL;

		//
		// Load record.
		//
		$record[ kId ] = "TERMS/$input";
		$record[ kKey ] = $input;
		$record[ kNid ] = "TERMS/$namespace";
		$record[ kLid ] = $code;
		$record[ kGid ] = $input;
		$record[ $theDescriptors[ "kSymbol" ] ] = $code;
		$record[ $theDescriptors[ "kSynonym" ] ] = [ $code ];
		if( $code_alt !== NULL )
			$record[ $theDescriptors[ "kSynonym" ] ][] = $code_alt;
		$record[ $theDescriptors[ "kDeploy" ] ] = kDeployStandard;

		//
		// Load labels.
		//
		switch( $code )
		{
			case "zh_CN":
				$record[ $theDescriptors[ "kLabel" ] ]
					= [ kLanguage =>
							"Chinese language in Peoples Republic of China" ];
				break;

			case "zh_HK":
				$record[ $theDescriptors[ "kLabel" ] ]
					= [ kLanguage =>
							"Chinese language in Hong Kong" ];
				break;

			case "zh_TW":
				$record[ $theDescriptors[ "kLabel" ] ]
					= [ kLanguage =>
							"Chinese language in Taiwan R.O.C." ];
				break;

			case "pt_BR":
				$record[ $theDescriptors[ "kLabel" ] ]
					= [ kLanguage =>
							"Portuguese language in Brasil" ];
				break;

			case "sr@latin":
				$record[ $theDescriptors[ "kLabel" ] ]
					= [ kLanguage =>
							"Latin transliteration of Serbian" ];
				break;

			case "tt@iqtelif":
				$record[ $theDescriptors[ "kLabel" ] ]
					= [ kLanguage =>
							"Tatar language in Tatarstan" ];
				$record[ $theDescriptors[ "kDefinition" ] ]
					= [ kLanguage =>
							"Tatar Language Locale using IQTElif alphabet; for Tatarstan, Russian Federation." ];
				break;

			case "bn_IN":
				$record[ $theDescriptors[ "kLabel" ] ]
					= [ kLanguage =>
							"Bangla language in India" ];
				break;

			default:
				$record[ $theDescriptors[ "kLabel" ] ] = [ kLanguage => "" ];
				break;
		}

		//
		// Append to buffer.
		//
		$buffer[] = $record;

		//
		// Append to edges.
		//
		$from = "TERMS/" . $record[ kKey ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$hash = md5( "$from\t$to\t$predicate" );
		$edge[ kId ] = "SCHEMAS/$hash";
		$edge[ kKey ] = $hash;
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ $theDescriptors[ "kPredicate" ] ] = $predicate;
		$edge[ $theDescriptors[ "kBranches" ] ] = [ $to ];
		$edges[] = $edge;

	} // Iterate all records.

	//
	// Write TERMS JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_$standard.json";
	$data = json_encode( $buffer, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Write EDGES JSON file.
	//
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "SCHEMAS_ISO_$standard.json";
	$data = json_encode( $edges, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

} // ISO_639_Locales.



?>
