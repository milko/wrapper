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
 * The class uses the inherited {@link Container) interface to manage a list of
 * {@link Container} connections, the {@link Container()} method can be used to manage this
 * list.
 *
 * The class declares the following abstract protected methods that have the duty of
 * creating and releasing the actual container connections:
 *
 * <ul>
 * 	<li><b>{@link containerCreate()}</b>: Create a {@link Container} connection.
 * 	<li><b>{@link containerDestruct()}</b>: Close a container connection.
 * </ul>
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/06/2016
 */
abstract class Database extends Server
{



/*=======================================================================================
 *																						*
 *							PUBLIC COLLECTION MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Collection																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage collection instance.</h4><p />
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
	 * The method will return a {@link Collection} instance, or <tt>NULL</tt> if no
	 * collection of the provided name was found.
	 *
	 * @param string				$theName			Collection name.
	 * @param mixed					$theOptions			Collection options or operation.
	 * @return Collection			Collection instance or <tt>NULL</tt>.
	 *
	 * @example
	 * <code>
	 * // Add and get a new collection without options.
	 * $db = $object->Collection( "col1", [] );
	 *
	 * // Add and get a new collection with options.
	 * $db = $object->Collection( "col1", [ "opt1" => "val1" ] );
	 *
	 * // Get collection instance.
	 * $db = $object->Collection( "col1" );
	 *
	 * // Forget collection.
	 * $object->Collection( "col1", FALSE );
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
			// Destruct database.
			//
			$this->collectionDestruct( $theName );

			//
			// Remove instance.
			//
			$this->offsetUnset( $theName );

			return NULL;															// ==>

		} // Reset collection instance.

		//
		// Connect database.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Create collection.
		//
		$collection = ( is_array( $theOptions ) )
					? $this->collectionCreate( $theName, $theOptions )
					: $this->collectionCreate( $theName );

		//
		// Connect collection.
		//
		$collection->Connect();

		//
		// Set collection.
		//
		$this->offsetSet( $theName, $collection );

		return $collection;															// ==>

	} // Collection.



/*=======================================================================================
 *																						*
 *								PROTECTED COLLECTION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	collectionCreate																*
	 *==================================================================================*/

	/**
	 * Create collection connection.
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
	 *	collectionDestruct																*
	 *==================================================================================*/

	/**
	 * Close collection connection.
	 *
	 * This method should close the collection connection identified by the provided name,
	 * the method will not handle the current object's collections list, it is only
	 * concerned with releasing eventual resources before the caller removes the connection.
	 *
	 * The method assumes the provided name exists in the collections list.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @param string				$theName			Collection name.
	 */
	abstract protected function collectionDestruct( string $theName );



} // class Database.


?>
