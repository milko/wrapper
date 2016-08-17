<?php

/**
 * DatabaseServer.php
 *
 * This file contains the definition of the {@link DatabaseServer} class.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *									DatabaseServer.php									*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Server;
use Milko\wrapper\Database;

/**
 * <h4>Database server class.</h4><p />
 *
 * This <em>abstract</em> class is the ancestor of all classes representing database server
 * instances.
 *
 * The class uses its inherited {@link Container} interface to store a list of
 * {@link Database} instances, this is performed by the {@link Database()} method.
 *
 * An abstract method, {@link NewDatabase()}, must be implemented by derived concrete
 * classes, its duty is to instantiate the correct type of {@link Database} instance.
 *
 * Finally, a set of protected methods are used to create, {@link databaseCreate()}, and
 * forget, {@link databaseDestruct()}, {@link Database} instances.
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
 * $sr = new DatabaseServer( "database://user:pass@host?opt=val" );
 *
 * // Get database.
 * $db = $sr->Database( "db1", [ "opt1" => "val1" ] );
 * // work with database
 * </code>
 */
abstract class DatabaseServer extends Server
{



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
	 * We overload the method to handle eventual database and collection in the data source
	 * name.
	 *
	 * @param string			$theConnection		Data source name.
	 */
	public function __construct( string $theConnection = NULL )
	{
		//
		// Call parent constructor.
		//
		parent::__construct( $theConnection );

		//
		// Handle path.
		//
		if( ($path = $this->Path()) !== NULL )
		{
			//
			// Get path components.
			//
			$tmp = explode( '/', $path );
			$database = $tmp[ 1 ];
			if( count( $tmp ) > 2 )
				$collection = $tmp[ 2 ];

			//
			// Remove current path.
			//
			$this->Path( FALSE );

			//
			// Add database.
			//
			$database = $this->Database( $database );

			//
			// Add collection.
			//
			if( count( $tmp ) > 2 )
				$database->Collection( $collection );

		} // Has path.

	} // Constructor.



/*=======================================================================================
 *																						*
 *							PUBLIC DATABASE MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Database																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage database instances.</h4><p />
	 *
	 * This method can be used to create, retrieve and forget {@link Database} instances, it
	 * accepts two parameters:
	 *
	 * <ul>
	 * 	<li><b>$theName</b>: The database name.
	 * 	<li><b>$theOptions</b>: The database creation options or operation:
	 * 	 <ul>
	 *	 	<li><tt>NULL</tt>: Retrieve database instance corresponding to provided name.
	 *	 	<li><tt>FALSE</tt>: Forget database instance corresponding to provided name.
	 *	 	<li><tt>array</tt>: Create an instance of the database using the provided
	 * 			options.
	 *	 	<li><i>other</i>: Any other value will be ignored.
	 * 	 </ul>
	 * </ul>
	 *
	 * To provide credentials to the database, use <tt>user</tt> and <tt>password</tt> in
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
	 * // Add and get a new database without options.
	 * $db = $object->Database( "db1", [] );
	 *
	 * // Add and get a new database with options.
	 * $db = $object->Database( "db1", [ "opt1" => "val1" ] );
	 *
	 * // Get database instance.
	 * $db = $object->Database( "db1" );
	 *
	 * // Forget database.
	 * $object->Database( "db1", FALSE );
	 * </code>
	 */
	public function Database( string $theName, $theOptions = NULL )
	{
		//
		// Return database instance.
		//
		if( $theOptions === NULL )
			return ( $this->offsetExists( $theName ) )
				 ? $this->offsetGet( $theName )										// ==>
				 : NULL;															// ==>

		//
		// Reset database instance.
		//
		if( $theOptions === FALSE )
		{
			//
			// Destruct database.
			//
			if( $this->offsetExists( $theName ) )
				$this->databaseDestruct( $this->offsetGet( $theName ) );

			//
			// Remove instance.
			//
			$this->offsetUnset( $theName );

			return NULL;															// ==>

		} // Reset database instance.

		//
		// Connect current object.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Create database.
		//
		$database = ( is_array( $theOptions ) )
				  ? $this->NewDatabase( $theName, $theOptions )
				  : $this->NewDatabase( $theName );

		//
		// Set database.
		//
		$this->offsetSet( $theName, $database );

		return $database;															// ==>

	} // Database.



/*=======================================================================================
 *																						*
 *						PUBLIC DATABASE INSTANTIATION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	NewDatabase																		*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate database.</h4><p />
	 *
	 * This method can be used to create a {@link Database} instance.
	 *
	 * The method expects the database name as the first parameter and the database creation
	 * options as the second parameter. The options are an associative array with the option
	 * as key and the option value as value.
	 *
	 * The user name and password can be provided in the options parameter as respectively
	 * {@link kOPTION_USER_CODE} and {@link kOPTION_USER_PASS}.
	 *
	 * If you wish to identify the database by the name parameter, but want to name the
	 * database differently, you can provide the database name in {@link kOPTION_NAME}.
	 *
	 * @param string				$theName			Database name.
	 * @param array					$theOptions			Creation options.
	 * @return Database				The {@link Database} instance.
	 *
	 * @example
	 * <code>
	 * // Create database "db1".
	 * $db = $object->NewDatabase( "db1" );
	 *
	 * // Create database "db0" named "db2" with credentials.
	 * $db = $object->NewDatabase(
	 * 	"db2", [
	 * 		Server::kOPTION_NAME => "db0",
	 * 		Server::kOPTION_USER_CODE => "user",
	 * 		Server::kOPTION_USER_PASS => "password"
	 * ]);
	 * </code>
	 */
	public function NewDatabase( string $theName, array $theOptions = [] )
	{
		//
		// Instantiate database.
		//
		$database = $this->databaseCreate();

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
		$database->Protocol( $this->Protocol() );
		$database->Host( $this->Host() );
		$database->Port( $this->Port() );

		//
		// Set database specific attributes.
		//
		$database->Path( "/$name" );
		if( $user !== NULL )
			$database->User( $user );
		if( $pass !== NULL )
			$database->Password( $pass );
		if( count( $theOptions ) )
			$database->Query( $theOptions );

		return $database;															// ==>

	} // NewDatabase.



/*=======================================================================================
 *																						*
 *								PROTECTED DATABASE INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	databaseCreate																	*
	 *==================================================================================*/

	/**
	 * Instantiate database.
	 *
	 * This method should return an empty {@link Database} instance.
	 *
	 * The method is abstract to provide derived concrete classes the option to instantiate
	 * the correct type of database.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @param string				$theName			Database name.
	 * @param array					$theOptions			Creation options.
	 * @return Database				The {@link Database} instance.
	 */
	abstract protected function databaseCreate();


	/*===================================================================================
	 *	databaseDestruct																*
	 *==================================================================================*/

	/**
	 * Close database connection.
	 *
	 * This method should release the provided {@link Database} by releasing used resources.
	 * The goal of this method is not to close the connection, since the database might be
	 * shared, but to release eventual resources.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @param Database				$theDatabase		Database instance.
	 */
	abstract protected function databaseDestruct( Database $theDatabase );




} // class DatabaseServer.


?>
