<?php

//
// ArangoDB playground.
//

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Namespaces.
//
use triagens\ArangoDb\Database as ArangoDatabase;
use triagens\ArangoDb\Collection as ArangoCollection;
use triagens\ArangoDb\CollectionHandler as ArangoCollectionHandler;
use triagens\ArangoDb\Endpoint as ArangoEndpoint;
use triagens\ArangoDb\Connection as ArangoConnection;
use triagens\ArangoDb\ConnectionOptions as ArangoConnectionOptions;
use triagens\ArangoDb\DocumentHandler as ArangoDocumentHandler;
use triagens\ArangoDb\Document as ArangoDocument;
use triagens\ArangoDb\Exception as ArangoException;
use triagens\ArangoDb\Export as ArangoExport;
use triagens\ArangoDb\ConnectException as ArangoConnectException;
use triagens\ArangoDb\ClientException as ArangoClientException;
use triagens\ArangoDb\ServerException as ArangoServerException;
use triagens\ArangoDb\Statement as ArangoStatement;
use triagens\ArangoDb\UpdatePolicy as ArangoUpdatePolicy;

//
// Define test database name.
//
define( "kTEST_DB", "GENERIC_WRAPPER_TESTS" );

//
// Conection oprions.
//
$connectionOptions = [
    //
    // Database name.
    //
    ArangoConnectionOptions::OPTION_DATABASE        => '_system',

    // normal unencrypted connection via TCP/IP
    ArangoConnectionOptions::OPTION_ENDPOINT        => 'tcp://localhost:8529',

//    // SSL endpoint to connect to
//    ArangoConnectionOptions::OPTION_ENDPOINT        => 'ssl://localhost:8529',
//    // SSL certificate validation
//    ArangoConnectionOptions::OPTION_VERIFY_CERT     => false,
//    // allow self-signed certificates
//    ArangoConnectionOptions::OPTION_ALLOW_SELF_SIGNED => true,
//    // https://www.openssl.org/docs/manmaster/apps/ciphers.html
//    ArangoConnectionOptions::OPTION_CIPHERS         => 'DEFAULT',

//    // connection via UNIX domain socket
//    ArangoConnectionOptions::OPTION_ENDPOINT        => 'unix:///tmp/arangodb.sock',

    // can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
    ArangoConnectionOptions::OPTION_CONNECTION      => 'Keep-Alive',
    // use basic authorization
    ArangoConnectionOptions::OPTION_AUTH_TYPE       => 'Basic',

    // authentication parameters
    // (note: must also start server with option
    //`--server.disable-authentication false`)
    // user for basic authorization
    ArangoConnectionOptions::OPTION_AUTH_USER       => 'root',
    // password for basic authorization
    ArangoConnectionOptions::OPTION_AUTH_PASSWD     => 'orzomotrillo',

    // timeout in seconds
    ArangoConnectionOptions::OPTION_TIMEOUT         => 30,

//    // tracer function, can be used for debugging
//    ArangoConnectionOptions::OPTION_TRACE           => $traceFunc,

    // create unknown collections automatically
    ArangoConnectionOptions::OPTION_CREATE          => true,

    // last update wins
    ArangoConnectionOptions::OPTION_UPDATE_POLICY   => ArangoUpdatePolicy::LAST
];

//
// Check endpoints.
//
echo( "Check endpoints:\n" );
$uri_tcp = 'tcp://127.0.0.1:8529';
echo( "$uri_tcp ==> " );
var_dump( ArangoEndpoint::isValid( $uri_tcp ) );
$uri_sock = 'unix:///tmp/arangodb.sock';
echo( "$uri_sock ==> " );
var_dump( ArangoEndpoint::isValid( $uri_sock ) );
echo( "\n" );

//
// Show connection options.
//
echo( "Connection options:\n" );
print_r( $connectionOptions );
echo( "\n" );

echo( "\n====================================================================================\n\n" );

//
// Create server connection.
//
echo( "Create server connection:\n" );
$server_connection = new ArangoConnection($connectionOptions);
echo( "\n" );

//
// Get connection information.
//
echo( "Get connection information:\n" );
$list = ArangoDatabase::getInfo( $server_connection );
print_r( $list );
echo( "\n" );

//
// Get connection endpoints.
//
echo( "Get connection endpoints:\n" );
$list = ArangoEndpoint::listEndpoints( $server_connection );
print_r( $list );
echo( "\n" );

//
// List databases.
//
echo( "List databases:\n" );
$list = ArangoDatabase::listDatabases( $server_connection );
print_r( $list );
echo( "\n" );

//
// List user databases.
//
echo( "User List databases:\n" );
$list = ArangoDatabase::listUserDatabases( $server_connection );
print_r( $list );
echo( "\n" );

echo( "\n====================================================================================\n\n" );

//
// Drop test database.
//
echo( "Drop test database:\n" );
if( in_array( kTEST_DB, ArangoDatabase::listDatabases( $server_connection )[ 'result' ] ) ) {
	$result = ArangoDatabase::delete( $server_connection, kTEST_DB );
	print_r( $result );
} else echo( "Not found.\n" );
echo( "\n" );

//
// Create test database.
//
echo( "Create test database:\n" );
if( ! in_array( kTEST_DB, ArangoDatabase::listDatabases( $server_connection )[ 'result' ] ) ) {
	$result = ArangoDatabase::create( $server_connection, kTEST_DB );
	print_r( $result );
}
echo( "\n" );

//
// Set database.
//
echo( "Set test database:\n" );
$database_connection = new ArangoConnection($connectionOptions);
$database_connection->setDatabase( kTEST_DB );
$list = ArangoDatabase::getInfo( $database_connection );
print_r( $list );
echo( "\n" );

echo( "\n====================================================================================\n\n" );

//
// Get collection handler.
//
echo( "Get collection handler\n" );
$collectionHandler = new ArangoCollectionHandler( $database_connection );
echo( "\n" );

//
// Get collections list.
//
echo( "Get collections list:\n" );
$list = $collectionHandler->getAllCollections();
print_r( $list );
echo( "\n" );

//
// Get non system collections list.
//
echo( "Get non system collections list:\n" );
$list = $collectionHandler->getAllCollections( ['excludeSystem' => TRUE] );
print_r( $list );
echo( "\n" );

echo( "\n====================================================================================\n\n" );

//
// Check for collection.
//
echo( "Check for collection:\n" );
echo( 'test_collection ==> ' );
$found = $collectionHandler->has( 'test_collection' );
var_dump( $found );
if( $found )
{
	echo( "Found:" );
	$collection = $collectionHandler->get( 'test_collection' );
	print_r( $collection );
}
echo( "\n" );

//
// Create collection.
//
if( ! $found )
{
	echo( "Create collection:\n" );
	$collection = $collectionHandler->create( 'test_collection' );
	print_r( $collection );
}
echo( "\n" );

//
// Get collection info.
//
echo( "Get collection info:\n" );
$collection = $collectionHandler->get( 'test_collection' );
print_r( $collection );
echo( "\n" );

//
// Get collections list.
//
echo( "Get collections list:\n" );
$list = $collectionHandler->getAllCollections();
print_r( $list );
echo( "\n" );

//
// Get non system collections list.
//
echo( "Get non system collections list:\n" );
$list = $collectionHandler->getAllCollections( ['excludeSystem' => TRUE] );
print_r( $list );
echo( "\n" );

//
// Get collection ID.
//
echo( "Get collection ID:\n" );
$result = $collection->getId();
var_dump( $result );
echo( "\n" );

//
// Get collection name.
//
echo( "Get collection name:\n" );
$result = $collection->getName();
var_dump( $result );
echo( "\n" );

//
// Get collection type.
//
echo( "Get collection type:\n" );
$result = $collection->getType();
var_dump( $result );
echo( "\n" );

//
// Truncate collection.
//
echo( "Truncate collection:\n" );
$result = $collectionHandler->truncate( $collection );
var_dump( $result );
echo( "\n" );

echo( "\n====================================================================================\n\n" );

//
// Create a document handler.
//
echo( "Create a document handler:\n" );
$documentHandler = new ArangoDocumentHandler( $database_connection );
echo( "\n" );

//
// Create a document.
//
echo( "Create a document:\n" );
$document = [ "date" => 19570728, "name" => "Milko", "surname" => "Škofič" ];
print_r( $document );
$document = ArangoDocument::createFromArray( $document );
print_r( $document );
echo( "\n" );

//
// Add a document without key.
//
echo( "Add a document without key:\n" );
$id = $documentHandler->save( $collection, $document );
echo( "Document: " );
print_r( $document );
echo( "ID: " );
var_dump( $id );
echo( "getId: " );
var_dump( $document->getId() );
echo( "getInternalId: " );
var_dump( $x = $document->getInternalId() );
echo( "getKey: " );
var_dump( $document->getKey() );
echo( "getInternalKey: " );
var_dump( $document->getInternalKey() );
echo( "getRevision: " );
var_dump( $revision = $document->getRevision() );
echo( "getAll: " );
print_r( $document->getAll() );
echo( "\n" );

//
// Create an array of the document.
//
echo( "Create document array:\n" );
echo( '$array = $document->getAll();' . "\n" );
$array = $document->getAll();
print_r( $array );
echo( "\n" );

//
// Add internals.
//
echo( "Add internals:\n" );
if( ! array_key_exists( '_key', $array ) )
	$array[ '_key' ]
		= ( ($key = $document->getKey()) !== NULL )
		? $key
		: $document->getId();
$array[ '_rev' ] = $document->getRevision();
print_r( $array );
echo( "\n" );

//
// Find document.
//
echo( "Find document:\n" );
$result = $documentHandler->getById( $collection, $id );
print_r( $result );
echo( "\n" );


?>
