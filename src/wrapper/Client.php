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
	 * The first parameter holds a reference to the object that created the current client
	 * and that represents its server.
	 *
	 * The second parameter represents the data source name, as provided to the parent
	 * constructor.
	 *
	 * We overload the constructor to store the client server object and to use the first
	 * element of the path as the current object's path; unlike {@link ClientServer} objects
	 * that do not feature a path.
	 *
	 * If the provided path has more than one element, the elements after the first one will
	 * be used to recursively instantiate and register client objects following the path
	 * levels.
	 *
	 * @param ClientServer|Client	$theServer		Client server.
	 * @param string				$theConnection	Data source name.
	 *
	 * @example
	 * <code>
	 * // Instantiate client server.
	 * $server = new ConcreteClientServer( "protocol://user:pass@host?opt=val" );
	 *
	 * // Instantiate client.
	 * $client = new ConcreteClient( $server, "protocol://host/name" );
	 * // Will create a client with path "name".
	 *
	 * // Instantiate nested clients.
	 * $client = new ConcreteClient( $server, "protocol://host/first/second/third" );
	 * // Will create a client with path "first",
	 * // that will contain a client with path "second",
	 * // that will contain a client with path "third".
	 * </code>
	 */
	public function __construct( $theServer, string $theConnection = NULL )
	{
		//
		// Set server reference.
		//
		$this->mServer = $theServer;

		//
		// Call ancestor constructor.
		// To prevent parsing the path.
		//
		Server::__construct( $theConnection );

		//
		// Handle path.
		//
		if( ($path = $this->Path()) !== NULL )
		{
			//
			// Explode path.
			//
			$tmp = explode( '/', $path );

			//
			// Set current object's path.
			//
			$this->Path( array_shift( $tmp ) );

			//
			// Iterate path components.
			//
			$client = $this;
			foreach( $tmp as $name )
				$client = $client->Client( $name, [] );

		} // Has path.

	} // Constructor.



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



} // class Client.


?>
