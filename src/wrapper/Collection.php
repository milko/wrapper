<?php

/**
 * Collection.php
 *
 * This file contains the definition of the {@link Collection} interface.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *									Database.php										*
 *																						*
 *======================================================================================*/

/**
 * <h4>Collection interface.</h4><p />
 *
 * This interface declares the methods that all collection concrete instances must
 * implement:
 *
 * <ul>
 * 	<li>Collection methods:
 *	 <ul>
 * 		<li><b>{@link Drop()}</b>: Drop the collection.
 * 		<li><b>{@link Records()}</b>: Return total records count.
 *	 </ul>
 * 	<li>Document methods:
 *	 <ul>
 *	 	<li><b>{@link SetOne()}</b>: Store a single document.
 *	 </ul>
 * </ul>
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		19/06/2016
 */
interface Collection
{



/*=======================================================================================
 *																						*
 *									COLLECTION METHODS									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Drop																			*
	 *==================================================================================*/

	/**
	 * <h4>Drop collection.</h4><p />
	 *
	 * Delete the current database.
	 */
	public function Drop();


	/*===================================================================================
	 *	Records																			*
	 *==================================================================================*/

	/**
	 * <h4>Return record count.</h4><p />
	 *
	 * Return the nimber of records in the collection.
	 *
	 * @return int					Collection record count.
	 */
	public function Records();



/*=======================================================================================
 *																						*
 *									DOCUMENT METHODS									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	SetOne																			*
	 *==================================================================================*/

	/**
	 * <h4>Store a document.</h4><p />
	 *
	 * Store a single document and return document identifier.
	 *
	 * The provided document must be either an <tt>array</tt>, an <tt>ArrayObject</tt> or
	 * a {@link Container}.
	 *
	 * @param mixed					$theDocument		Document to store.
	 * @return mixed				The document key.
	 */
	public function SetOne( $theDocument );



} // interface Collection.


?>
