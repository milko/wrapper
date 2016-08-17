<?php

/**
 * Collection.php
 *
 * This file contains the definition of the {@link Collection} class.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *									Collection.php										*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Database;

/**
 * <h4>Collection class.</h4><p />
 *
 * This <em>abstract</em> class is the ancestor of all classes representing collection
 * instances.
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/06/2016
 */
abstract class Collection extends Server
{



/*=======================================================================================
 *																						*
 *								PROTECTED CONNECTION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	connectionCreate																*
	 *==================================================================================*/

	/**
	 * Open connection.
	 *
	 * This method should create the actual connection and return the native connection
	 * object; in this class the method is virtual, it is the responsibility of concrete
	 * derived classes to implement this method.
	 *
	 * This method assumes the caller has checked whether the connection was already open
	 * and if the previously opened connection was closed.
	 *
	 * This method assumes the data source name ({@link URL()} to hold the connection
	 * parameters.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @return mixed				The native connection.
	 */
	protected function connectionCreate()
	{
		return "Is connected";
	
	} // connectionCreate.


	/*===================================================================================
	 *	connectionDestruct																*
	 *==================================================================================*/

	/**
	 * Close connection.
	 *
	 * This method should close the open connection, in this class the method is virtual, it
	 * is the responsibility of concrete classes to implement this method.
	 *
	 * This method assumes the caller has checked whether a connection is open, it should
	 * assume the {@link $mConnection} attribute holds a valid native connection object.
	 *
	 * If the operation fails, the method should raise an exception.
	 */
	protected function connectionDestruct()
	{
		return "Is not connected";
	
	} // connectionDestruct.



} // class Database.


?>
