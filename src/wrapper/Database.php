<?php

/**
 * Database.php
 *
 * This file contains the definition of the {@link Database} interface.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *									Database.php										*
 *																						*
 *======================================================================================*/

/**
 * <h4>Database interface.</h4><p />
 *
 * This interface declares the methods that all database concrete instances must implement:
 *
 * <ul>
 * 	<li><b>{@link Drop()}</b>: Drop the database.
 * 	<li><b>{@link Key()}</b>: Return document key property name.
 * 	<li><b>{@link Rev()}</b>: Return document revision property name.
 * </ul>
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		19/06/2016
 */
interface Database
{



/*=======================================================================================
 *																						*
 *									DATABASE METHODS									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Drop																			*
	 *==================================================================================*/

	/**
	 * <h4>Drop database.</h4><p />
	 *
	 * Delete the current database and disconnect it.
	 *
	 * The object clients will still be there, so if you want to remove them you will have
	 * to do so manually.
	 *
	 * Once dropped, you can re-create the database by calling its
	 * <tt>ClientServer::Connect()</tt> method; if you haven't deleted its clients, these
	 * will also be restored, although empty, if the result of the
	 * {@link ClientServer::nestedConnections()} method is <tt>TRUE</tt>.
	 */
	public function Drop();



/*=======================================================================================
 *																						*
 *									STATIC METHODS	    								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Key 																			*
	 *==================================================================================*/

	/**
	 * <h4>Return document key.</h4><p />
	 *
	 * This method should return the property name of the document key.
	 *
	 * We assume all collections share the same key property, although each collection may
	 * set a different data type in it.
	 *
	 * The method must be implemented in derived concrete classes.
	 */
	static function Key();


	/*===================================================================================
	 *	Rev 																			*
	 *==================================================================================*/

	/**
	 * <h4>Return document revision.</h4><p />
	 *
	 * This method should return the property name of the document revision.
	 *
	 * We assume all collections share the same revision property, the actual value of the
	 * property is undefined.
	 *
	 * The method must be implemented in derived concrete classes.
	 */
	static function Rev();



} // interface Database.


?>
