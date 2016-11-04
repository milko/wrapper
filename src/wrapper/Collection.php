<?php

/**
 * Collection.php
 *
 * This file contains the definition of the {@link Collection} interface.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *									Collection.php										*
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
	 * The provided parameter will be left unchanged, the document key an other internal
	 * properties should not be copied to the parameter.
	 *
	 * If the inserted document already exists, the method should raise an exception.
	 *
	 * @param mixed					$theDocument		Document to store.
	 * @return mixed				The document key.
	 */
	public function AddOne( $theDocument );


	/*===================================================================================
	 *	GetOne																			*
	 *==================================================================================*/

	/**
	 * <h4>Retrieve a document.</h4><p />
	 *
	 * Retrieve a document corresponding to the provided document identifier.
	 *
	 * The method will query the collection searching for a document matching the provided
	 * primary key, if found, it will return the native document, or <tt>NULL</tt> if not
	 * found.
	 *
	 * The provided key <em>must be a scalar</em>.
	 *
	 * @param mixed					$theIdentifier		Document identifier.
	 * @return mixed				The native document or <tt>NULL</tt>.
	 */
	public function GetOne( $theIdentifier );



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
	 * This method should return the document key property <em>name</em>, this property is
	 * generally defined at the database engine level and represents the unique identifier
	 * of the document within its collection.
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
	 * Convert the provided object to a native document instance.
	 *
	 * The method expects an <tt>array</tt>, <tt>ArrayObject</tt> or
	 * <tt>{@Link Container}</tt> and will return a native document instance with the
	 * contents of the provided object.
	 *
	 * Concrete classes may need to add internal or private properties to the native
	 * document, so when converting back to container you may have to handle these values.
	 *
	 * The method should only create a new instance if the provided document is not
	 * already a native document, so if you need a copy, you should make it beforehand.
	 *
	 * @param mixed					$theDocument		Document to convert.
	 * @return mixed				The native document.
	 */
	static function ToNativeDocument( $theDocument );


	/*===================================================================================
	 *	ToContainer                														*
	 *==================================================================================*/

	/**
	 * <h4>Convert to a {@link Container}.</h4><p />
	 *
	 * Convert the provided native object into a {@link Container} instance.
	 *
	 * The method expects a database native document and will return a {@link Container}
	 * instance with the contents of the provided document.
	 *
	 * Concrete classes may need to add internal or private properties to the container, so
	 * when converting back to native documents you may have to handle these values.
	 *
	 * The method should only create a new instance if the provided document is not
	 * already a container, so if you need a copy, you should make it beforehand.
	 *
	 * The method expects a native document, so, when implementing it you <em>must</em>
	 * throw an exception if the provided parameter is not of the correct type; the
	 * exception is if you provide <tt>NULL</tt>: in that case you should return
	 * <tt>NULL</tt>.
	 *
	 * @param mixed         		$theDocument		Document to convert.
	 * @return Container			The {@link Container} instance.
	 * @throws \BadMethodCallException
	 */
	static function ToContainer( $theDocument );



} // interface Collection.


?>
