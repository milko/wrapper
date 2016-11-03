<?php

/**
 * Document.php
 *
 * This file contains the definition of the {@link Milko\wrapper\Document} class.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *									Document.php	    								*
 *																						*
 *======================================================================================*/

/**
 * <h4>Document base object.</h4><p />
 *
 * This class is the ancestor of all persistent objects, it is derived from the
 * {@link Container} class and adds an interface that handles storing and retrieving an
 * object from a {@link Container} instance.
 *
 *	@package	Core
 *
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		27/10/2016
 */
class Document extends Container
{
	/**
	 * Default status.
	 *
	 * This bitfield value represents the default bitfield value: all bits off.
	 */
	const kSTATUS_DEFAULT = '00000000';

	/**
	 * Dirty.
	 *
	 * This bitfield value indicates that the document has been modified.
	 *
	 * If the flag is not set, it means that the document was not modified.
	 */
	const kFLAG_STATE_DIRTY = '00000001';

	/**
	 * Persistent.
	 *
	 * This bitfield value indicates that the object is persistent, which means that is is
	 * currently stored in its collection.
	 *
	 * If the flag is not set, it means that the document is not stored in a collection.
	 */
	const kFLAG_STATE_PERSISTENT = '00000002';

	/**
	 * Collection.
	 *
	 * This attribute stores the collection in which the document is stored.
	 *
	 * @var Collection
	 */
	protected $mCollection = NULL;

	/**
	 * Status.
	 *
	 * This attribute stores a bitfield value representing the persistence status of the
	 * object.
	 *
	 * @var string
	 */
	protected $mStatus = NULL;




/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate class.</h4>
	 *
	 * We overload the constructor by requiring the collection in which the document should
	 * reside.
	 *
	 * The first <em>required</em> parameter represents the collection in which the document
	 * either currently resides, or the collection in which the new document will be stored.
	 *
	 * The second parameter represents either the contents of the document, or its primary
	 * key:
	 *
	 * <ul>
	 *  <li><tt>array</tt>: If you provide an array, it means that the parameter holds the
	 *      contents of the document. In this case, the parameter is passed to the parent
	 *      constructor.
	 *  <li><tt>scalar</tt>: If you provide a scalar, the method assumes the parameter to be
	 *      the primary key of the document: it will search for it in the collection and
	 *      pass the found data to the parent constructor, or pass <tt>NULL</tt> if not
	 *      found.
	 * </ul>
	 *
	 * The third parameter corresponds to the second parameter of the parent constructor, it
	 * is a flag that determines whether the provided document contents should be converted
	 * to an array.
	 *
	 * The dirty ({@link isDirty()}) status is handled as follows:
	 *
	 * <ul>
	 *  <li><i>Empty document</i>: The state is <tt>FALSE</tt>.
	 *  <li><i>Filled document</i>: The state is <tt>TRUE</tt>.
	 *  <li><i>Filled document from collection</i>: The state is <tt>FALSE</tt>.
	 * </ul>
	 *
	 * The persistent ({@link isPersistent()}) status is handled as follows:
	 *
	 * <ul>
	 *  <li><i>Empty document</i>: The state is <tt>FALSE</tt>.
	 *  <li><i>Filled document</i>: The state is <tt>FALSE</tt>.
	 *  <li><i>Filled document from collection</i>: The state is <tt>TRUE</tt>.
	 * </ul>
	 *
	 * @param Collection			$theCollection		Collection of the document.
	 * @param mixed					$theProperties		Properties or <tt>NULL</tt>.
	 * @param bool					$asArray			<tt>TRUE</tt> convert to array.
	 * @throws \InvalidArgumentException
	 *
	 * @uses isDirty()
	 * @uses isPersistent()
	 *
	 * @example
	 * <code>
	 * // Empty document.
	 * $document = new Document( $collection );
	 * // $document->isDirty() == FALSE;
	 * // $document->isPersistent() == FALSE;
	 *
	 * // New document with contents.
	 * $document = new Document( $collection, [ "name' => "A name" ] );
	 * // $document->isDirty() == TRUE;
	 * // $document->isPersistent() == FALSE;
	 *
	 * // Load a document from the collection.
	 * $document = new Document( $collection, "document ID" );
	 * // $document->isDirty() == FALSE;
	 * // $document->isPersistent() == TRUE (if found);
	 * </code>
	 */
	public function __construct( Collection $theCollection,
											$theProperties = NULL,
								 bool       $asArray = TRUE )
	{
		//
		// Save collection.
		//
		$this->mCollection = $theCollection;

		//
		// Reset status.
		//
		$this->mStatus = hex2bin( self::kSTATUS_DEFAULT );

		//
		// Handle document contents.
		//
		if( ($theProperties === NULL)
		 || is_array( $theProperties )
		 || ($theProperties instanceof Container)
		 || ($theProperties instanceof \ArrayObject) )
		{
			//
			// Call parent constructor.
			//
			parent::__construct( $theProperties, $asArray );

			//
			// Set status.
			//
			$this->isDirty( count( $this->mProperties ) );
			$this->isPersistent( FALSE );

		} // Provided document contents.

		//
		// Load from collection.
		//
		else
		{
			//
			// Find by key.
			//
			$document = $theCollection->GetOne( $theProperties );

			//
			// Instantiate document.
			//
			parent::__construct( $document, $asArray );

			//
			// Set status.
			//
			if( $document !== NULL )
			{
				$this->isDirty( FALSE );
				$this->isPersistent( TRUE );

			} // Document found.

		} // Provided primary key.

	} // Constructor.



/*=======================================================================================
 *																						*
 *								ARRAY ACCESS INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * <h4>Set a value at a given offset.</h4><p />
	 *
	 * We overload this method to set the dirty flag whenever a value is modified.
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value to set at offset.
	 *
	 * @uses isDirty()
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Call parent method.
		//
		parent::offsetSet( $theOffset, $theValue );

		//
		// Set dirty flag.
		//
		$this->isDirty( TRUE );

	} // offsetSet.


	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * <h4>Reset a value at a given offset.</h4><p />
	 *
	 * We overload this method to set the dirty flag whenever a value is deleted.
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @uses isDirty()
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Call parent method.
		//
		parent::offsetUnset( $theOffset );

		//
		// Set dirty flag.
		//
		$this->isDirty( TRUE );

	} // offsetUnset.



/*=======================================================================================
 *																						*
 *								PROTECTED STATUS INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	isDirty 																		*
	 *==================================================================================*/

	/**
	 * <h4>Probe or set dirty status.</h4><p />
	 *
	 * This method can be used to check or indicate whether the document has been modified,
	 * if you provide a value that resolves to <tt>TRUE</tt>, the status will be set; if you
	 * provide a value that resolves to <tt>FALSE</tt> the status will be reset.
	 *
	 * If you omit the parameter or pass <tt>NULL</tt>, the method will return the current
	 * state.
	 *
	 * If the second parameter resolves to <tt>TRUE</tt>, in the event you are modifying the
	 * current state, the method will return the old state.
	 *
	 * @param mixed 				$theState			State to set, or <tt>NULL</tt>.
	 * @param bool					$doOld				<tt>TRUE</tt> return old value.
	 * @return bool                 The current or old dirty state.
	 *
	 * @uses manageBitfieldAttribute()
	 */
	public function isDirty( $theState = NULL, $doOld = FALSE )
	{
		return $this->manageBitfieldAttribute(
			$this->mStatus,
			hex2bin( self::kFLAG_STATE_DIRTY ),
			$theState,
			$doOld);                                                               // ==>

	} // isDirty.


	/*===================================================================================
	 *	isPersistent																	*
	 *==================================================================================*/

	/**
	 * <h4>Probe or set persistent status.</h4><p />
	 *
	 * This method can be used to check or indicate whether the document is stored in its
	 * collection, if you provide a value that resolves to <tt>TRUE</tt>, the status will be
	 * set; if you provide a value that resolves to <tt>FALSE</tt> the status will be reset.
	 *
	 * If you omit the parameter or pass <tt>NULL</tt>, the method will return the current
	 * state.
	 *
	 * If the second parameter resolves to <tt>TRUE</tt>, in the event you are modifying the
	 * current state, the method will return the old state.
	 *
	 * @param mixed 				$theState			State to set, or <tt>NULL</tt>.
	 * @param bool					$doOld				<tt>TRUE</tt> return old value.
	 * @return bool                 The current or old persistent state.
	 *
	 * @uses manageBitfieldAttribute()
	 */
	public function isPersistent( $theState = NULL, $doOld = FALSE )
	{
		return $this->manageBitfieldAttribute(
			$this->mStatus,
			hex2bin( self::kFLAG_STATE_PERSISTENT ),
			$theState,
			$doOld);                                                               // ==>

	} // isPersistent.




} // class Document.


?>
