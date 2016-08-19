<?php

/**
 * Client.php
 *
 * This file contains the definition of the {@link Client} class.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *										Client.php										*
 *																						*
 *======================================================================================*/

use Milko\wrapper\ClientServer;

/**
 * <h4>Client class.</h4><p />
 *
 * This <em>abstract</em> class is the ancestor of all classes representing client
 * instances. A client is instantiated by a concrete instance of the {@link ClientServer}
 * class, or by a concrete Client instance. A practical implementation of this scheme could
 * be a database server derived from {@link ClientServer}, a database object derived by this
 * class and a collection object also derived from this class.
 *
 * The class implements the {@link Server} functionality to manage the client native
 * connection object and it uses its inherited {@link Container} array interface to manage a
 * list of sub-client objects.
 *
 * Instances of this class add a data member that references the {@link ClientServer} or
 * Client object that instantiated the current instance.
 *
 * The class features a public method, {@link Server()}, that can be used to retrieve the
 * current object's creator.
 *
 * To have examples of concrete implementations of this class please refer to the unit tests
 * or to concrete classes such as the {@link Milko\wrapper\MongoDB\Database} and
 * {@link Milko\wrapper\ArangoDB\Database} classes.
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
 * // Instantiate empty client with existing server.
 * $client = new Client( NULL, $server );
 *
 * // Instantiate client including server.
 * $client = new Client( "protocol://user:pass@host/Client1?opt=val" );
 * // A client instance named "Client1" is created,
 * // and a server instance will be created with the client as its element:
 * // its server can be accessed with $parent = $client->Server();
 * // Note that $parent is not the same instance as $server.
 *
 * // Instantiate client providing server.
 * $client = new Client( "protocol://user:pass@host/Client1?opt=val", $server );
 * // A client instance named "Client1" is created with custom options,
 * // and $server will have the client as its element.
 *
 * // Will raise an exception.
 * // $client = new Client();
 *
 * // Connect client (server was automatically connected).
 * $client->Connect();
 *
 * // List available sub-clients.
 * $list = $client->Clients();
 *
 * // Add anew sub-client.
 * $subclient = $client->Client( "Client1", [] );
 *
 * // Add new client with custom name and credentials.
 * $subclient =
 * 	$client->Client(
 * 		"Client2", [
 * 			self::kOPTION_NAME => "Custom",
 * 			self::kOPTION_USER_CODE => "user",
 * 			self::kOPTION_USER_PASS => "pass"
 * 		]
 * 	);
 *
 * // Retrieve "Client1".
 * $subclient = $client->Client( "Client1" );
 *
 * // Instantiate client with nested subclients.
 * $client = new Client( "protocol://user:pass@host/First/Second/Third?opt=val", $server );
 * // Will create a client with name "First",
 * // client "First" will contain "Second",
 * $second = $client->Client( "Second" );
 * $second = $client[ "Second" ];
 * // client "Second" will contain client "Third".
 * $third = $second->Client( "Third" );
 * $third = $second[ "Third" ];
 * $third = $first[ "Second" ][ "Third" ];
 * // Client "Third" can also be accessed by the server.
 * $third = $server[ "First" ][ "Second" ][ "Third" ];
 * </code>
 */
abstract class Client extends ClientServer
{
	/**
	 * Server.
	 *
	 * This attribute stores the client creator.
	 *
	 * @var ClientServer|Client
	 */
	protected $mServer = NULL;




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
	 * The first parameter represents the data source name, as provided to the parent
	 * constructor.
	 *
	 * The second parameter holds a reference to the object that created the current client
	 * and that represents its server.
	 *
	 * <ul>
	 * 	<li><em>Both parameters are <tt>NULL</tt></em>: Will raise an exception: a client
	 * 		requires a server.
	 * 	<li><em>Provided first parameter only</em>: Instantiates an object according to the
	 * 		data source name and will create an instance of the server object by using the
	 * 		{@link serverCreate()} protected method.
	 * 	<li><em>Provided both parameters</em>: Instantiates an object according to the data
	 * 		source name and will set the server with the second parameter without calling
	 * 		the {@link serverCreate()} method.
	 * 	<li><em>Provided second parameter only</em>: Instantiates an empty object with the
	 * 		server reference from the second parameter.
	 * </ul>
	 *
	 * If the first parameter is provided, the {@link Path()} component must have at least
	 * one element, the client name, or an exception will be triggered.
	 *
	 * If the provided path has more than one element, the elements after the first one will
	 * be used to recursively instantiate and register client objects following the path
	 * levels.
	 *
	 * @param ClientServer|Client	$theServer		Client server.
	 * @param string				$theConnection	Data source name.
	 * @throws \InvalidArgumentException
	 *
	 * @uses Path()
	 * @uses serverCreate()
	 *
	 * @example
	 * <code>
	 * // Note that you need to derive the class to use it.
	 *
	 * // Instantiate client server.
	 * $server = new ClientServer( "protocol://user:pass@host?opt=val" );
	 *
	 * // Instantiate empty client with existing server.
	 * $client = new Client( NULL, $server );
	 *
	 * // Instantiate client including server.
	 * $client = new Client( "protocol://user:pass@host/Client1?opt=val" );
	 * // A client instance named "Client1" is created,
	 * // and a server instance will be created with the client as its element:
	 * // its server can be accessed with $parent = $client->Server();
	 * // Note that $parent is not the same instance as $server.
	 *
	 * // Instantiate client providing server.
	 * $client = new Client( "protocol://user:pass@host/Client1?opt=val", $server );
	 * // A client instance named "Client1" is created with custom options,
	 * // and $server will have the client as its element.
	 *
	 * // Will raise an exception.
	 * $client = new Client();
	 * </code>
	 */
	public function __construct( string $theConnection = NULL, $theServer = NULL )
	{
		//
		// Call ancestor constructor.
		// To prevent parsing the path.
		//
		Server::__construct( $theConnection );

		//
		// Set server reference.
		//
		if( $theServer !== NULL )
			$this->mServer = $theServer;

		//
		// Create server reference.
		//
		elseif( $theConnection !== NULL )
		{
			//
			// Check path.
			//
			if( $this->mPath !== NULL )
			{
				//
				// Create server reference.
				//
				$this->mServer = $this->serverCreate();

				//
				// Explode path.
				//
				$tmp = explode( '/', $this->Path() );

				//
				// Set current object's path.
				//
				$this->Path( array_shift( $tmp ) );

				//
				// Add current object to server.
				//
				$this->mServer[ $this->Path() ] = $this;

				//
				// Iterate path components.
				//
				$client = $this;
				foreach( $tmp as $name )
					$client = $client->Client( $name, [] );

			} // Provided path.

			else
				throw new \InvalidArgumentException(
					"Missing path in data source name."
				);																// !@! ==>

		} // Provided data source name.

		else
			throw new \InvalidArgumentException(
				"Unable to determine client server."
			);																	// !@! ==>

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
	 * We overload this method to ensure parent objects are also connected.
	 *
	 * @return mixed				Native connection object.
	 *
	 * @uses URL( )
	 * @uses isConnected( )
	 * @uses connectionCreate()
	 */
	public function Connect()
	{
		//
		// Connect parent objects.
		//
@@@		$this->Server()->Connect();

		//
		// Create connection if not conected.
		//
		if( ! $this->isConnected() )
			$this->mConnection =
				$this->connectionCreate();

		return $this->mConnection;													// ==>

	} // Connect.



/*=======================================================================================
 *																						*
 *							PUBLIC MEMBER ACCESSOR INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Server																			*
	 *==================================================================================*/

	/**
	 * <h4>Return client server.</h4><p />
	 *
	 * This method can be used to retrieve the client server.
	 *
	 * @return ClientServer|Client	Client server or Client instance.
	 */
	public function Server()
	{
		return $this->mServer;														// ==>

	} // Server.



/*=======================================================================================
 *																						*
 *								PROTECTED SERVER INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	serverCreate																	*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate server.</h4><p />
	 *
	 * This method should return either a {@link ClientServer} or {@link Client} instance
	 * according to the current object attributes.
	 *
	 * The goal of this method is to allow instantiating clients without needing to
	 * instantiate their parent object beforehand.
	 *
	 * This method is called by the constructor when no server instance has been provided,
	 * in this step the current object's path contains all the original elements: the duty
	 * of this method is also to determine which path element represents the path of the
	 * current object, which elements represent the parent object names and which elements
	 * represent the names of the nested sub-clients.
	 *
	 * The method is abstract to provide derived concrete classes the option to instantiate
	 * the correct type of object.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @return ClientServer|Client	The parent instance.
	 */
	abstract protected function serverCreate();



} // class Client.


?>
