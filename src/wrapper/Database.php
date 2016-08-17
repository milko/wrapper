<?php

/**
 * Database.php
 *
 * This file contains the definition of the {@link Database} class.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *										Database.php									*
 *																						*
 *======================================================================================*/

use Milko\wrapper\DatabaseServer;
use Milko\wrapper\Collection;

/**
 * <h4>Database class.</h4><p />
 *
 * This <em>abstract</em> class is the ancestor of all classes representing database
 * instances.
 *
 * The class uses its inherited {@link Container} interface to store a list of
 * {@link Container} instances, this is performed by the {@link Container()} method.
 *
 * An abstract method, {@link NewContainer()}, must be implemented by derived concrete
 * classes, its duty is to instantiate the correct type of {@link Container} instance.
 *
 * Finally, a set of protected methods are used to create, {@link containerCreate()}, and
 * forget, {@link containerDestruct()}, {@link Container} instances.
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/06/2016
 *
 * @example
 * <code>
 * // Instantiate database server.
 * $server = new DatabaseServer( "database://user:pass@host?opt=val" );
 *
 * // Get database.
 * $database = $server->Database( "db1" );
 *
 * // Instantiate database.
 * $database = new Database( $server, "protocol://host/db1" );
 * </code>
 */
abstract class Database extends Server
{
	/**
	 * Server.
	 *
	 * This attribute stores the database server.
	 *
	 * @var DatabaseServer
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
	 * We overload the method to handle the database server parameter.
	 *
	 * Objects of this class store their server in the {@link $mServer} object attribute,
	 * this parameter is required; the second parameter is the first parameter of the
	 * server {@linkl Server::__construct()} method.
	 *
	 * @param Server			$theServer			Database server.
	 * @param string			$theConnection		Data source name.
	 *
	 * @example
	 * <code>
	 * // Instantiate database server.
	 * $server = new DatabaseServer( "protocol://user:pass@host?opt=val" );
	 *
	 * // Instantiate database.
	 * $database = new Database( $server, "protocol://host/database" );
	 * </code>
	 */
	public function __construct( Server $theServer, string $theConnection = NULL )
	{
		//
		// Set server reference.
		//
		$this->mServer = $theServer;

		//
		// Call parent constructor.
		//
		parent::__construct( $theConnection );

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
	 * <h4>Return database server.</h4><p />
	 *
	 * This method can be used to retrieve the database server.
	 *
	 * @return DatabaseServer		Database server instance.
	 */
	public function Server()
	{
		return $this->mServer;														// ==>

	} // Server.



/*=======================================================================================
 *																						*
 *							PUBLIC COLLECTION MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Collection																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage collection instances.</h4><p />
	 *
	 * This method can be used to create, retrieve and forget {@link Collection} instances,
	 * it accepts two parameters:
	 *
	 * <ul>
	 * 	<li><b>$theName</b>: The collection name.
	 * 	<li><b>$theOptions</b>: The collection creation options or operation:
	 * 	 <ul>
	 *	 	<li><tt>NULL</tt>: Retrieve collection instance corresponding to provided name.
	 *	 	<li><tt>FALSE</tt>: Forget collection instance corresponding to provided name.
	 *	 	<li><tt>array</tt>: Create an instance of the collection using the provided
	 * 			options.
	 *	 	<li><i>other</i>: Any other value will be ignored.
	 * 	 </ul>
	 * </ul>
	 *
	 * To provide credentials to the collection, use <tt>user</tt> and <tt>password</tt> in
	 * the provided options.
	 *
	 * The method will return a {@link Database} instance, or <tt>NULL</tt> if no database
	 * of the provided name was found.
	 *
	 * @param string				$theName			Database name.
	 * @param mixed					$theOptions			Database options or operation.
	 * @return Database				Database instance or <tt>NULL</tt>.
	 *
	 * @example
	 * <code>
	 * // Add and get a new collection without options.
	 * $db = $object->Collection( "db1", [] );
	 *
	 * // Add and get a new collection with options.
	 * $db = $object->Collection( "db1", [ "opt1" => "val1" ] );
	 *
	 * // Get collection instance.
	 * $db = $object->Collection( "db1" );
	 *
	 * // Forget collection.
	 * $object->Collection( "db1", FALSE );
	 * </code>
	 */
	public function Collection( string $theName, $theOptions = NULL )
	{
		//
		// Return collection instance.
		//
		if( $theOptions === NULL )
			return ( $this->offsetExists( $theName ) )
				 ? $this->offsetGet( $theName )										// ==>
				 : NULL;															// ==>

		//
		// Reset collection instance.
		//
		if( $theOptions === FALSE )
		{
			//
			// Destruct collection.
			//
			if( $this->offsetExists( $theName ) )
				$this->collectionDestruct( $this->offsetGet( $theName ) );

			//
			// Remove instance.
			//
			$this->offsetUnset( $theName );

			return NULL;															// ==>

		} // Reset collection instance.

		//
		// Connect current object.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Create collection.
		//
		$collection = ( is_array( $theOptions ) )
					? $this->NewCollection( $theName, $theOptions )
					: $this->NewCollection( $theName );

		//
		// Set collection.
		//
		$this->offsetSet( $theName, $collection );

		return $collection;															// ==>

	} // Collection.



/*=======================================================================================
 *																						*
 *						PUBLIC COLLECTION INSTANTIATION INTERFACE						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	NewCollection																	*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate collection.</h4><p />
	 *
	 * This method can be used to create a {@link Collection} instance.
	 *
	 * The method expects the collection name as the first parameter and the collection
	 * creation options as the second parameter. The options are an associative array with
	 * the option as key and the option value as value.
	 *
	 * The user name and password can be provided in the options parameter as respectively
	 * {@link kOPTION_USER_CODE} and {@link kOPTION_USER_PASS}.
	 *
	 * If you wish to identify the collection by the name parameter, but want to name the
	 * collection differently, you can provide the collection name in {@link kOPTION_NAME}.
	 *
	 * @param string				$theName			Collection name.
	 * @param array					$theOptions			Creation options.
	 * @return Collection			The {@link Collection} instance.
	 *
	 * @example
	 * <code>
	 * // Create collection "db1".
	 * $db = $object->NewCollection( "col1" );
	 *
	 * // Create collection "col0" named "col2" with credentials.
	 * $db = $object->NewCollection(
	 * 	"col2", [
	 * 		Server::kOPTION_NAME => "col0",
	 * 		Server::kOPTION_USER_CODE => "user",
	 * 		Server::kOPTION_USER_PASS => "password"
	 * ]);
	 * </code>
	 */
	public function NewCollection( string $theName, array $theOptions = [] )
	{
		//
		// Instantiate database.
		//
		$collection = $this->collectionCreate();

		//
		// Parse options.
		//
		$name = $theName;
		if( array_key_exists( self::kOPTION_NAME, $theOptions ) )
		{
			$name = $theOptions[ self::kOPTION_NAME ];
			unset( $theOptions[ self::kOPTION_NAME ] );
		}
		$user = NULL;
		if( array_key_exists( self::kOPTION_USER_CODE, $theOptions ) )
		{
			$user = $theOptions[ self::kOPTION_USER_CODE ];
			unset( $theOptions[ self::kOPTION_USER_CODE ] );
		}
		$pass = NULL;
		if( array_key_exists( self::kOPTION_USER_PASS, $theOptions ) )
		{
			$pass = $theOptions[ self::kOPTION_USER_PASS ];
			unset( $theOptions[ self::kOPTION_USER_PASS ] );
		}

		//
		// Copy attributes from this object.
		//
		$collection->Protocol( $this->Protocol() );
		$collection->Host( $this->Host() );
		$collection->Port( $this->Port() );
		$collection->Path( $this->Path() );

		//
		// Set database specific attributes.
		//
		$collection->Path( $this->Path() . "/$name" );
		if( $user !== NULL )
			$collection->User( $user );
		if( $pass !== NULL )
			$collection->Password( $pass );
		if( count( $theOptions ) )
			$collection->Query( $theOptions );

		return $collection;															// ==>

	} // NewCollection.



/*=======================================================================================
 *																						*
 *								PROTECTED COLLECTION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	collectionCreate																*
	 *==================================================================================*/

	/**
	 * Instantiate collection.
	 *
	 * This method should return an empty {@link Collection} instance.
	 *
	 * The method is abstract to provide derived concrete classes the option to instantiate
	 * the correct type of collection.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @param string				$theName			Collection name.
	 * @param array					$theOptions			Creation options.
	 * @return Collection			The {@link Collection} instance.
	 */
	abstract protected function collectionCreate();


	/*===================================================================================
	 *	collectionDestruct																*
	 *==================================================================================*/

	/**
	 * Close collection connection.
	 *
	 * This method should release the provided {@link Collection} by releasing used
	 * resources. The goal of this method is not to close the connection, since the
	 * collection might be shared, but to release eventual resources.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @param Collection			$theCollection		Collection instance.
	 */
	abstract protected function collectionDestruct( Collection $theCollection );



} // class Database.


?>
