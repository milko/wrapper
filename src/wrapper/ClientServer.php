<?php

/**
 * ClientServer.php
 *
 * This file contains the definition of the {@link ClientServer} class.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *									ClientServer.php									*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Server;
use Milko\wrapper\Client;

/**
 * <h4>Client server class.</h4><p />
 *
 * This <em>abstract</em> class is the ancestor of server objects that serve client
 * objects. A concrete instance of such a class could be a database server that manages a
 * list of databases.
 *
 * The class implements the {@link Server} functionality to manage the native connection
 * object and it uses its inherited {@link Container} array interface to manage a list of
 * client objects.
 *
 * The class features the following public interface:
 *
 * <ul>
 * 	<li><tt>Client()</tt>: This method can be used to add, retrieve and forget client
 * 		objects residing in the classe's clients list.
 * 	<li><tt>Clients()</tt>: This <em>abstract</em> method can be used to get the list of
 * 		client names of the current server.
 * 	<li><tt>NewClient()</tt>: This <em>abstract</em> method can be used to create a client
 * 		instance.
 * </ul>
 *
 * The class features the following protected interface:
 *
 * <ul>
 * 	<li><tt>clientCreate()</tt>: This method is used to instantiate an empty client of the
 * 		correct type that will be outfitted by the {@link NewClient()} method.
 * 	<li><tt>clientDestruct()</tt>: This <em>abstract</em> method can be used to release any
 * 		pending resource before removing clients from the list.
 * </ul>
 *
 * The {@link Path()} part of the data source name determines the client names and their
 * nesting levels.
 *
 * To have examples of concrete implementations of this class please refer to the unit tests
 * or to concrete classes such as the {@link Milko\wrapper\MongoDB\Server} and
 * {@link Milko\wrapper\ArangoDB\Server} classes.
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		18/06/2016
 *
 * @example
 * <code>
 * // Note that you need to derive the class to use it.
 *
 * // Instantiate client server.
 * $server = new ClientServer( "protocol://user:pass@host?opt=val" );
 *
 * // Connect server.
 * $server->Connect();
 *
 * // List available clients.
 * $list = $server->Clients();
 *
 * // Add anew client.
 * $client = $server->Client( "Client1", [] );
 *
 * // Add new client with custom name and credentials.
 * $client =
 * 	$server->Client(
 * 		"Client2", [
 * 			self::kOPTION_NAME => "Custom",
 * 			self::kOPTION_USER_CODE => "user",
 * 			self::kOPTION_USER_PASS => "pass"
 * 		]
 * 	);
 *
 * // Retrieve "Client1".
 * $client = $server->Client( "Client1" );
 *
 * // Instantiate server with nested clients.
 * $server = new ClientServer( "protocol://user:pass@host/Database/Collection/Document?opt=val" );
 * // Will create a server containing client "Database",
 * $database = $server->Client( "Database" );
 * $database = $server[ "Database" ];
 * // client "Database" will contain client "Collection",
 * $collection = $database->Client( "Collection" );
 * $collection = $database[ "Collection" ];
 * // client "Collection" will contain client "Document".
 * $document = $collection->Client( "Document" );
 * $document = $collection[ "Document" ];
 *
 * // Access nested client.
 * $document = $server[ "Database" ][ "Collection" ][ "Document" ];
 *
 * // Create server, database and collection retrieving collection.
 * $collection = ClientServer::NewConnection( "protocol://server/database/collection" );
 * </code>
 */
abstract class ClientServer extends Server
{
	/**
	 * Client name tag.
	 *
	 * This represents the client name tag provided in options parameters, this will be
	 * used when the reference name should be different from the actual client name.
	 */
	const kOPTION_NAME = "@name@";

	/**
	 * User code tag.
	 *
	 * This represents the user code tag provided in options parameters, this will be used
	 * to implement credentials when instantiating clients.
	 */
	const kOPTION_USER_CODE = "@user@";

	/**
	 * User password tag.
	 *
	 * This represents the user password tag provided in options parameters, this will be
	 * used to implement credentials when instantiating clients.
	 */
	const kOPTION_USER_PASS = "@pass@";




/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate class.</h4><p />
	 *
	 * We overload the method to handle eventual clients in the path component of the data
	 * source name.
	 *
	 * When providing the path component of the data source name to the constructor, it will
	 * be used to instantiate and add clients to the list. Path elements represent client
	 * names and these will be used to recursively instantiate clients and add them to the
	 * respective clients lists. The path will then be removed from the current object.
	 *
	 * @param string			$theConnection		Data source name.
	 *
	 * @uses Path()
	 * @uses Database()
	 *
	 * @example
	 * <code>
	 * // Instantiate client server.
	 * $server = new ConcreteClientServer( "protocol://user:pass@host?opt=val" );
	 *
	 * // Instantiate client server and nested clients.
	 * $server = new DatabaseServer( "protocol://user:pass@host/Directory/File?opt=val" );
	 * // Get "Directory".
	 * $directory = $server->Client( "Directory" );
	 * $directory = $server[ "Directory" ];
	 * // Get "File".
	 * $file = $directory->Client( "File" );
	 * $file = $server[ "Directory" ][ "File" ];
	 * </code>
	 */
	public function __construct( string $theConnection = NULL )
	{
		//
		// Call parent constructor.
		//
		Server::__construct( $theConnection );

		//
		// Handle path.
		//
		if( ($path = $this->Path()) !== NULL )
		{
			//
			// Remove current path.
			//
			$this->Path( FALSE );

			//
			// Iterate path components.
			//
			$tmp = explode( '/', $path );
			$client = $this;
			foreach( $tmp as $name )
				$client = $client->Client( $name, [] );

		} // Has path.

	} // Constructor.



/*=======================================================================================
 *																						*
 *							PUBLIC CONNECTION MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Connect																			*
	 *==================================================================================*/

	/**
	 * <h4>Open server connection.</h4><p />
	 *
	 * We overload this method to propagate the operation to clients, if the result of the
	 * {@link nestedConnections()} method is <tt>TRUE</tt>.
	 *
	 * Note that the clients will be only affected if the current object is disconnected.
	 *
	 * @return mixed				Native connection object.
	 *
	 * @uses isConnected( )
	 * @uses nestedConnections( )
	 */
	public function Connect()
	{
		//
		// Get current state.
		//
		$connected = $this->isConnected();

		//
		// Call parent method.
		//
		$connection = Server::Connect();

		//
		// Cascade to clients.
		//
		if( $this->nestedConnections()
		 && (! $connected) )
		{
			//
			// Connect clients.
			//
			foreach( $this as $client )
				$client->Connect();

		} // Has nested connections.

		return $connection;															// ==>

	} // Connect.


	/*===================================================================================
	 *	Disconnect																		*
	 *==================================================================================*/

	/**
	 * <h4>Close server connection.</h4><p />
	 *
	 * We overload this method to propagate the operation to clients, if the result of the
	 * {@link nestedConnections()} method is <tt>TRUE</tt>.
	 *
	 * Note that the clients will be only affected if the current object is connected.
	 *
	 * @return boolean				<tt>TRUE</tt> was connected, <tt>FALSE</tt> wasn't.
	 *
	 * @uses nestedConnections()
	 */
	public function Disconnect()
	{
		//
		// Drop current connection.
		//
		$connected = Server::Disconnect();

		//
		// Cascade to clients.
		//
		if( $this->nestedConnections()
		 && $connected )
		{
			//
			// Connect clients.
			//
			foreach( $this as $client )
				$client->Disconnect();

		} // Has nested connections.

		return $connected;															// ==>

	} // Disconnect.



/*=======================================================================================
 *																						*
 *							PUBLIC CLIENT MANAGEMENT INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Client																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage client instances.</h4><p />
	 *
	 * This method can be used to create, retrieve and forget {@link Client} instances, it
	 * accepts two parameters:
	 *
	 * <ul>
	 * 	<li><b>$theName</b>: The client name.
	 * 	<li><b>$theOptions</b>: The client creation options or operation:
	 * 	 <ul>
	 *	 	<li><tt>NULL</tt>: Retrieve client instance corresponding to provided name.
	 *	 	<li><tt>FALSE</tt>: Forget client instance corresponding to provided name.
	 *	 	<li><tt>array</tt>: Create an instance of the client using the provided options.
	 *	 	<li><i>other</i>: Any other value will be ignored.
	 * 	 </ul>
	 * </ul>
	 *
	 * To provide credentials when creating the client, use
	 * <tt>{@link kOPTION_USER_CODE}</tt> and <tt>{@link kOPTION_USER_PASS}</tt> in the
	 * provided options to respectively indicate the user code and password.
	 *
	 * The method will return a {@link Client} instance, or <tt>NULL</tt> if no client
	 * of the provided name was found.
	 *
	 * @param string				$theName			Client name.
	 * @param mixed					$theOptions			Client options or operation.
	 * @return Client				Client instance or <tt>NULL</tt>.
	 *
	 * @uses clientDestruct()
	 * @uses NewClient()
	 *
	 * @example
	 * <code>
	 * // Add new client.
	 * $client = $server->Client( "Client1", [] );
	 *
	 * // Add new client with custom name and credentials.
	 * $client =
	 * 	$server->Client(
	 * 		"Client2", [
	 * 			self::kOPTION_NAME => "Custom",
	 * 			self::kOPTION_USER_CODE => "user",
	 * 			self::kOPTION_USER_PASS => "pass"
	 * 		]
	 * 	);
	 *
	 * // Retrieve client.
	 * $client = $server->Client( "Client1" );
	 * // Will retrieve the client with internal name "Client1".
	 * $client = $server->Client( "Client2" );
	 * // Will retrieve the client with internal name "Custom".
	 *
	 * // Forget client.
	 * $server->Client( "Client1", FALSE );
	 * </code>
	 */
	public function Client( string $theName, $theOptions = NULL )
	{
		//
		// Return client by name.
		//
		if( $theOptions === NULL )
			return ( $this->offsetExists( $theName ) )
				 ? $this->offsetGet( $theName )										// ==>
				 : NULL;															// ==>

		//
		// Reset client instance.
		//
		if( $theOptions === FALSE )
		{
			//
			// Destruct database.
			//
			if( $this->offsetExists( $theName ) )
				$this->clientDestruct( $this->offsetGet( $theName ) );

			//
			// Remove instance.
			//
			$this->offsetUnset( $theName );

			return NULL;															// ==>

		} // Reset client instance.

		//
		// Create client.
		//
		$client = ( is_array( $theOptions ) )
				? $this->NewClient( $theName, $theOptions )
				: $this->NewClient( $theName );

		//
		// Set client in list.
		//
		$this->offsetSet( $theName, $client );

		return $client;																// ==>

	} // Client.


	/*===================================================================================
	 *	Clients																			*
	 *==================================================================================*/

	/**
	 * <h4>Get available clients info.</h4><p />
	 *
	 * This method can be used to retrieve information regarding the available clients of
	 * the current object.
	 *
	 * The returned list should include all clients that belong to the current object,
	 * including those that are not yet stored in the clients list. A concrete example
	 * could be a database server, this method should return the list of database names
	 * hosted by the server, including those not yet registered with the {@link Client()}
	 * method; the latter list can be obtained by iterating the current object.
	 *
	 * The returned list should feature the client names in the array keys and native client
	 * information in the array values, the nature of the data depends on the concrete
	 * implementation of the derived class.
	 *
	 * The method must be implemented in derived concrete classes.
	 *
	 * @return array				List of clients info.
	 */
	abstract public function Clients();



/*=======================================================================================
 *																						*
 *						PUBLIC CLIENT INSTANTIATION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	NewClient																		*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate client.</h4><p />
	 *
	 * This method can be used to create a {@link Client} instance.
	 *
	 * The method expects the client name as the first parameter and the creation options as
	 * the second parameter. The options are an associative array with the option name as
	 * key and the option value as value.
	 *
	 * The user name and password can be provided in the options parameter as respectively
	 * {@link kOPTION_USER_CODE} and {@link kOPTION_USER_PASS}.
	 *
	 * If you wish to identify the client by the provided name, but name the client
	 * differently, provide the client name in the options with {@link kOPTION_NAME}.
	 *
	 * @param string				$theName			Client name.
	 * @param array					$theOptions			Creation options.
	 * @return Client				The {@link Client} instance.
	 *
	 * @uses isConnected()
	 * @uses Connect()
	 * @uses clientCreate()
	 * @uses Protocol()
	 * @uses Host()
	 * @uses Port()
	 *
	 * @example
	 * <code>
	 * // Instantiate new client.
	 * $client = $server->NewClient( "Client1" );
	 *
	 * // Instantiate new client with custom name and credentials.
	 * $client =
	 * 	$server->NewClient(
	 * 		"Client2", [
	 * 			self::kOPTION_NAME => "Custom",
	 * 			self::kOPTION_USER_CODE => "user",
	 * 			self::kOPTION_USER_PASS => "pass"
	 * 		]
	 * 	);
	 * </code>
	 */
	public function NewClient( string $theName, array $theOptions = [] )
	{
		//
		// Connect object.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Instantiate empty client.
		//
		$client = $this->clientCreate();

		//
		// Set options.
		//
		$options = [
			self::kOPTION_NAME => NULL,
			self::kOPTION_USER_CODE => NULL,
			self::kOPTION_USER_PASS => NULL
		];

		//
		// Parse options.
		//
		foreach( array_keys( $options ) as $option )
		{
			//
			// Match option.
			//
			if( array_key_exists( $option, $theOptions ) )
			{
				$options[ $option ] = $theOptions[ $option ];
				unset( $theOptions[ $option ] );
			}
		}

		//
		// Copy attributes from this object.
		//
		$client->Protocol( $this->Protocol() );
		$client->Host( $this->Host() );
		$client->Port( $this->Port() );

		//
		// Set client path.
		//
		$client->Path(
			( $options[ self::kOPTION_NAME ] !== NULL )
				? $options[ self::kOPTION_NAME ]
				: $theName );

		//
		// Set client credentials.
		//
		if( $options[ self::kOPTION_USER_CODE ] !== NULL )
			$client->User( $options[ self::kOPTION_USER_CODE ] );
		if( $options[ self::kOPTION_USER_PASS ] !== NULL )
			$client->Password( $options[ self::kOPTION_USER_PASS ] );

		//
		// Set client options.
		//
		if( count( $theOptions ) )
			$client->Options( $theOptions );

		return $client;																// ==>

	} // NewClient.



/*=======================================================================================
 *																						*
 *						STATIC CONNECTION CREATION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	NewConnection																	*
	 *==================================================================================*/

	/**
	 * <h4>Create a connection.</h4><p />
	 *
	 * This static method can be used to instantiate a server hierarchy and return the child
	 * object reference.
	 *
	 * The connection URI used in the constructor uses the {@link Path()) property to
	 * indicate the hierarchy to be created: for instance a <tt>database/collection</tt>
	 * path will add a <tt>database</tt> client to the server and a <tt>collection</tt>
	 * client to the database, but these client objects can only be accessed if their names
	 * are known. This method will return the leaf object of the hierarchy while accessing
	 * the parent objects can be done through the {@link Client::Server()} method.
	 *
	 * This method expects the same connection string that is passed to the constructor and
	 * will return the leaf object which can be a derived instance of this class, or of the
	 * {@link Client} class, depending whether the path was provided in the connection
	 * string.
	 *
	 * If along the hierarchy any client is not found, the method will raise an exception;
	 * this is because the called constructor is supposed to instantiate the hierarchy.
	 *
	 * @param string			$theConnection		Data source name.
	 * @return ClientServer		The server or leaf client instance.
	 * @throws \RuntimeException
	 *
	 * @example
	 * <code>
	 * // Get server.
	 * $server = ClientServer::NewConnection( "protocol://user:pass@host?opt=val" );
	 *
	 * // Get database
	 * $database = ClientServer::NewConnection( "protocol://user:pass@host/database" );
	 *
	 * // Get collection
	 * $collection = ClientServer::NewConnection( "protocol://user:pass@host/database/collection" );
	 * // Get database.
	 * $database = $collection->Server();
	 * </code>
	 */
	static function NewConnection( string $theConnection = NULL )
	{
		//
		// Get class name.
		//
		$class = get_called_class();

		//
		// Extract path.
		//
		$path = explode( '/', parse_url( $theConnection, PHP_URL_PATH ) );
		if( count( $path ) )
			array_shift( $path );

		//
		// Instantiate server.
		//
		$client = new $class( $theConnection );

		//
		// Traverse to leaf.
		//
		foreach( $path as $element )
		{
			//
			// Get client.
			//
			$client = $client->Client( $element );
			if( $client === NULL )
				throw new \RuntimeException(
					"Unable to create client [$element]." );					// !@! ==>

		} // Traversing hierarchy.

		return $client;																// ==>

	} // NewConnection.



/*=======================================================================================
 *																						*
 *								PROTECTED CONNECTION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	nestedConnections																*
	 *==================================================================================*/

	/**
	 * <h4>Nested connections flag.</h4><p />
	 *
	 * This method should return <tt>TRUE</tt> if opening and closing connections should be
	 * propagated to clients, this means that when the current object connection is closed,
	 * all client connections should be closed; this is valid also when opening connections.
	 *
	 * If the flag is off, the clients will remain untouched.
	 *
	 * I tried doing this with a static data member, but when subclassed and accessed with
	 * the <tt>static::</tt> prefix it still returned the parent value; I probably don't
	 * understand how static data members work in PHP. So this is why I developed this
	 * protected method.
	 *
	 * By default the flag is <tt>OFF</tt>.
	 *
	 * @return bool					<tt>TRUE</tt> to cascade connections and disconnectons.
	 */
	protected function nestedConnections()
	{
		return FALSE;																// ==>

	} // nestedConnections.



/*=======================================================================================
 *																						*
 *								PROTECTED CLIENT INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	clientCreate																	*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate empty client.</h4><p />
	 *
	 * This method should return an empty {@link Client} instance.
	 *
	 * The method is abstract to provide derived concrete classes the option to instantiate
	 * the correct type of client.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @return Client				The {@link Client} instance.
	 */
	abstract protected function clientCreate();


	/*===================================================================================
	 *	clientDestruct																	*
	 *==================================================================================*/

	/**
	 * <h4>Close client connection.</h4><p />
	 *
	 * This method should release the provided {@link Client} by releasing used resources.
	 * The goal of this method is not to close the connection, since the client might be
	 * shared, but to release eventual resources.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @param Client				$theClient			Client instance.
	 */
	abstract protected function clientDestruct( Client $theClient );




} // class ClientServer.


?>
