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



} // interface Database.


?>
