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
 * The class uses the inherited {@link Container) interface to manage a list of
 * {@link Database} connections, the {@link Database()} method can be used to manage this
 * list.
 *
 * The class declares the following abstract protected methods that have the duty of
 * creating and releasing the actual database connections:
 *
 * <ul>
 * 	<li><b>{@link databaseCreate()}</b>: Create a {@link Database} connection.
 * 	<li><b>{@link databaseDestruct()}</b>: Close a database connection.
 * </ul>
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/06/2016
 */
abstract class DatabaseServer extends Server
{



/*=======================================================================================
 *																						*
 *							PUBLIC DATABASE MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Database																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage database instance.</h4><p />
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
			$this->databaseDestruct( $theName );

			//
			// Remove instance.
			//
			$this->offsetUnset( $theName );

			return NULL;															// ==>

		} // Reset database instance.

		//
		// Connect database server.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Create database.
		//
		$database = ( is_array( $theOptions ) )
				  ? $this->databaseCreate( $theName, $theOptions )
				  : $this->databaseCreate( $theName );

		//
		// Connect database.
		//
		$database->Connect();

		//
		// Set database.
		//
		$this->offsetSet( $theName, $database );

		return $database;															// ==>

	} // Database.



/*=======================================================================================
 *																						*
 *								PROTECTED DATABASE INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	databaseCreate																	*
	 *==================================================================================*/

	/**
	 * Create database connection.
	 *
	 * This method should create the actual database connection and return the
	 * {@link Database} instance; in this class the method is virtual, it is the
	 * responsibility of concrete derived classes to implement this method.
	 *
	 * The first parameter represents the database name, the second optional parameter
	 * represents the creation options.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @param string				$theName			Database name.
	 * @param array					$theOptions			Creation options.
	 * @return mixed				The {@link Database} instance.
	 */
	abstract protected function databaseCreate( string $theName, array $theOptions = [] );


	/*===================================================================================
	 *	databaseDestruct																*
	 *==================================================================================*/

	/**
	 * Close database connection.
	 *
	 * This method should close the database connection identified by the provided name, the
	 * method will not handle the current object's databases list, it is only concerned with
	 * releasing eventual resources before the caller removes the connection.
	 *
	 * The method assumes the provided name exists in the databases list.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @param string				$theName			Database name.
	 */
	abstract protected function databaseDestruct( string $theName );




} // class DatabaseServer.


?>
