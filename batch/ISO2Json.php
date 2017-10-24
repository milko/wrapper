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
define( "kLocale", "ISO:639:local:" );
define( "kSubdivision", "ISO:3166:type");
define( "kCountry", "ISO:3166-1");
define( "kWithdrawal", "ISO:withdrawal" );
define( "kBiblio", "ISO:639:biblio" );
define( "kDeployStandard", ":state:implementation:standard" );
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
// Connect languages collection.
//
$languages = $database->Client( "ISO_" . ISOCodes::k639_3, [] );
$languages->Connect();

//
// Handle standards.
//
$locales = array_merge( $locales, ISO_15924( $database, $languages, $directory ) );
$locales = array_merge( $locales, ISO_3166_1( $database, $languages, $directory ) );
$locales = array_merge( $locales, ISO_3166_2( $database, $languages, $directory ) );
$locales = array_merge( $locales, ISO_3166_3( $database, $languages, $directory ) );
$locales = array_merge( $locales, ISO_639_2( $database, $languages, $directory ) );

print_r( array_unique( $locales ) );

echo( "\nDone!\n" );



/*=======================================================================================
 *																						*
 *									STANDARDS HANDLERS	  			  					*
 *																						*
 *======================================================================================*/



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
 * @returs array						List of locales.
 */
function ISO_15924( \Milko\Wrapper\ClientServer	$theDatabase,
					\Milko\Wrapper\Client		$theLanguages,
					SplFileInfo					$theDirectory)
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
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = "TERMS/$namespace";
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ kSynonym ] = [ $key ];
		if( array_key_exists( "numeric", $input) )
			$record[ kSynonym ][] = $input[ "numeric" ];
		$record[ kSynonym ] = array_unique( $record[ kSynonym ] );
		$record[ kDeploy ] = kDeployStandard;

		//
		// Load labels.
		//
		$record[ kLabel ] = [];
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
					$record[ kLabel ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ kLabel ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ kLabel ][ $lang ] = $name;
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
		$edge[ kKey ] = md5( "$from\t$to\t$predicate" );
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ kPredicate ] = $predicate;
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
 * @returs array						List of locales.
 */
function ISO_3166_1( \Milko\Wrapper\ClientServer	$theDatabase,
					 \Milko\Wrapper\Client			$theLanguages,
					 SplFileInfo					$theDirectory)
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
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = "TERMS/$namespace";
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ kSynonym ] = [ $input[ "alpha_3" ] ];
		if( array_key_exists( "alpha_2", $input) )
			$record[ kSynonym ][] = $input[ "alpha_2" ];
		if( array_key_exists( "numeric", $input) )
			$record[ kSynonym ][] = $input[ "numeric" ];
		$record[ kSynonym ] = array_unique( $record[ kSynonym ] );
		$record[ kDeploy ] = kDeployStandard;

		//
		// Load labels.
		//
		$record[ kLabel ] = [];
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
					$record[ kLabel ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ kLabel ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ kLabel ][ $lang ] = $name;
					break;
			}
		}

		//
		// Load official name.
		//
		if( array_key_exists( "official_name", $input ) )
		{
			$record[ kDefinition ] = [];
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
						$record[ kDefinition ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
						break;

					case 3:
						$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
						if ( ! $match )
							throw new Exception("Unable to resolve [$lang] language.");
						$record[ kDefinition ][ kLangNS . $match[ $db_key ] ] = $name;
						break;

					default:
						$lang = kLocale . $lang;
						$locales[] = $lang;
						$record[ kDefinition ][ $lang ] = $name;
						break;
				}
			}
		}

		//
		// Load common name.
		//
		if( array_key_exists( "common_name", $input ) )
		{
			$record[ kDescription ] = [];
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
						$record[ kDescription ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
						break;

					case 3:
						$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
						if ( ! $match )
							throw new Exception("Unable to resolve [$lang] language.");
						$record[ kDescription ][ kLangNS . $match[ $db_key ] ] = $name;
						break;

					default:
						$lang = kLocale . $lang;
						$locales[] = $lang;
						$record[ kDescription ][ $lang ] = $name;
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
		$edge[ kKey ] = md5( "$from\t$to\t$predicate" );
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ kPredicate ] = $predicate;
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
 * @returs array						List of locales.
 */
function ISO_3166_2( \Milko\Wrapper\ClientServer	$theDatabase,
					 \Milko\Wrapper\Client			$theLanguages,
					 SplFileInfo					$theDirectory)
{
	//
	// Init local storage.
	//
	$locales = [];
	$standard = ISOCodes::k3166_2;
	$namespace = "ISO:$standard";
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
		$edge = [];
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
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = "TERMS/$namespace";
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ kSynonym ] = [ $key ];
		$record[ kDeploy ] = kDeployStandard;

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
		$record[ kSubdivision ] = implode(" ", $name );

		//
		// Load labels.
		//
		$record[ kLabel ] = [];
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
					$record[ kLabel ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ kLabel ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ kLabel ][ $lang ] = $name;
					break;
			}
		}

		//
		// Handle parent.
		//
		if( array_key_exists( "parent", $input ) )
			$record[ "__parent__" ] = $country . "-" . $input[ "parent" ];

		//
		// Append to edges.
		//
		$from = "TERMS/" . $record[ kKey ];
		$to = $record[ kNid ];
		$predicate = ":predicate:enum-of";
		$edge[ kKey ] = md5( "$from\t$to\t$predicate" );
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ kPredicate ] = $predicate;
		$edges[] = $edge;

		//
		// Append to parent.
		//
		if( array_key_exists( "__parent__", $record ) )
		{
			$from = "TERMS/" . $record[ kKey ];
			$to = "TERMS/$namespace:" . $record[ "__parent__" ];
			$predicate = ":predicate:enum-of";
			$edge[ kKey ] = md5( "$from\t$to\t$predicate" );
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ kPredicate ] = $predicate;
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
			$edge[ kKey ] = md5( "$from\t$to\t$predicate" );
			$edge[ kFrom ] = $from;
			$edge[ kTo ] = $to;
			$edge[ kPredicate ] = $predicate;
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
	$tmp = [];
	foreach( $types as $i => $n )
	{
		$record = [];
		$index = sprintf( "%02d", $i + 1 );
		$record[ kKey ] = kSubdivision . ":$index";
		$record[ kNid ] = "TERMS/" . kSubdivision;
		$record[ kLid ] = $index;
		$record[ kGid ] = $record[ kKey ];
		$record[ kDeploy ] = kDeployStandard;
		$record[ kLabel ] = [ kLanguage => $n ];
		$record[ kBranches ] = [ $record[ kNid ] ];
		$tmp[] = $record;
	}
	$file = $theDirectory->getRealPath() . DIRECTORY_SEPARATOR . "TERMS_ISO_3166_types.json";
	$data = json_encode( $tmp, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE );
	file_put_contents( $file, $data );

	//
	// Refactor subdivision types.
	//
	foreach( $buffer as $i => $record )
	{
		if( array_key_exists( kSubdivision, $record ) )
		{
			$tmp = $record[ kSubdivision ];
			$index = array_search($tmp, $types );
			if( $index === false )
				throw new Exception("Unable to match subdivision [$tmp].");
			$record[ kSubdivision ] = kSubdivision . ":" . sprintf( "%02d", $index + 1 );
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
 * @returs array						List of locales.
 */
function ISO_3166_3( \Milko\Wrapper\ClientServer	$theDatabase,
					 \Milko\Wrapper\Client			$theLanguages,
					 SplFileInfo					$theDirectory)
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
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = "TERMS/$namespace";
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ kSynonym ] = [ $input[ "alpha_2" ], $input[ "alpha_3" ], $input[ "alpha_4" ] ];
		if( array_key_exists( "numeric", $input) )
			$record[ kSynonym ][] = $input[ "numeric" ];
		$record[ kSynonym ] = array_unique( $record[ kSynonym ] );
		$record[ kDeploy ] = kDeployStandard;

		//
		// Handle withdrawal date.
		//
		if( array_key_exists( "withdrawal_date", $input ) )
		{
			$tmp = explode("-", $input[ "withdrawal_date" ] );
			$record[ kWithdrawal ] = implode("", $tmp );
		}

		//
		// Load labels.
		//
		$record[ kLabel ] = [];
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
					$record[ kLabel ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ kLabel ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ kLabel ][ $lang ] = $name;
					break;
			}
		}

		//
		// Load comment.
		//
		if( array_key_exists( "comment", $input ) )
			$record[ kNote ][ kLanguage ] = htmlspecialchars( $input[ "comment" ] );

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
		$edge[ kKey ] = md5( "$from\t$to\t$predicate" );
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ kPredicate ] = $predicate;
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
 * @returs array						List of locales.
 */
function ISO_639_2( \Milko\Wrapper\ClientServer	$theDatabase,
					\Milko\Wrapper\Client		$theLanguages,
					SplFileInfo					$theDirectory)
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
		// Init loop storage.
		//
		$edge = [];
		$record = [];

		//
		// Load record.
		//
		$key = $input[ $db_key ];
		$record[ kKey ] = "$namespace:$key";
		$record[ kNid ] = "TERMS/$namespace";
		$record[ kLid ] = $key;
		$record[ kGid ] = $record[ kKey ];
		$record[ kSynonym ] = [ $input[ "alpha_3" ] ];
		if( array_key_exists( "alpha_2", $input) )
			$record[ kSynonym ][] = $input[ "alpha_2" ];
		$record[ kSynonym ] = array_unique( $record[ kSynonym ] );
		$record[ kDeploy ] = kDeployStandard;
		if( array_key_exists("bibliographic", $input ) )
			$record[ kBiblio ] = $input[ "bibliographic" ];

		//
		// Load labels.
		//
		$record[ kLabel ] = [];
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
					$record[ kLabel ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
					break;

				case 3:
					$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
					if ( ! $match )
						throw new Exception("Unable to resolve [$lang] language.");
					$record[ kLabel ][ kLangNS . $match[ $db_key ] ] = $name;
					break;

				default:
					$lang = kLocale . $lang;
					$locales[] = $lang;
					$record[ kLabel ][ $lang ] = $name;
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
						$record[ kDefinition ][ kLangNS . $match[ 0 ][ $db_key ] ] = $name;
						break;

					case 3:
						$match = $theLanguages->Connection()->findOne( [ $db_key => $lang ] );
						if ( ! $match )
							throw new Exception("Unable to resolve [$lang] language.");
						$record[ kDefinition ][ kLangNS . $match[ $db_key ] ] = $name;
						break;

					default:
						$lang = kLocale . $lang;
						$locales[] = $lang;
						$record[ kLabel ][ $lang ] = $name;
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
		$edge[ kKey ] = md5( "$from\t$to\t$predicate" );
		$edge[ kFrom ] = $from;
		$edge[ kTo ] = $to;
		$edge[ kPredicate ] = $predicate;
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

} // ISO_639_2.


?>