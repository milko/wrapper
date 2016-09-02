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
 * 	<li>Static methods:
 *	 <ul>
 *	 	<li><b>{@link DocumentKey()}</b>: Return the document key property name.
 *	 	<li><b>{@link DocumentRevision()}</b>: Return the document revision property name.
 *	 	<li><b>{@link ToNativeDocument()}</b>: Convert provided data to native document.
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
	 * Delete the current collection and disconnect it.
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
	 *	AddOne																			*
	 *==================================================================================*/

	/**
	 * <h4>Insert a document.</h4><p />
	 *
	 * Insert a single document and return document identifier.
	 *
	 * The provided document must be either an <tt>array</tt>, an <tt>ArrayObject</tt> or
	 * a {@link Container}.
	 *
	 * If the inserted document already exists, the method should raise an exception.
	 *
	 * @param mixed					$theDocument		Document to store.
	 * @return mixed				The document key.
	 */
	public function AddOne( $theDocument );



/*=======================================================================================
 *																						*
 *									STATIC METHODS										*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	DocumentKey																		*
	 *==================================================================================*/

	/**
	 * <h4>Return the document key.</h4><p />
	 *
	 * This method should return the document key property name, this property is generally
	 * defined at the database engine level and represents the unique identifier of the
	 * document within its collection.
	 *
	 * @return string				The document key property name.
	 */
	static function DocumentKey();


	/*===================================================================================
	 *	DocumentRevision																*
	 *==================================================================================*/

	/**
	 * <h4>Return the document revision.</h4><p />
	 *
	 * This method should return the document revision property name, this property is
	 * generally defined at the database engine level and represents the value that defines
	 * the document revision.
	 *
	 * @return string				The document revision property name.
	 */
	static function DocumentRevision();


	/*===================================================================================
	 *	ToNativeDocument																*
	 *==================================================================================*/

	/**
	 * <h4>Convert to a native document.</h4><p />
	 *
	 * Convert the provided object to a native document.
	 *
	 * The method expects an <tt>array</tt>, <tt>ArrayObject</tt> or
	 * <tt>{@Link Container}</tt>.
	 *
	 * @param mixed					$theDocument		Document to convert.
	 * @return mixed				The native document.
	 */
	static function ToNativeDocument( $theDocument );



} // interface Collection.


?>
