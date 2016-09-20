<?php

/**
 * Collection.php
 *
 * This file contains the definition of the {@link Milko\wrapper\ArangoDB\Collection} class.
 */

namespace Milko\wrapper\ArangoDB;

/*=======================================================================================
 *																						*
 *									Collection.php										*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Client;
use Milko\wrapper\Container;

use triagens\ArangoDb\Collection as ArangoCollection;
use triagens\ArangoDb\Document as ArangoDocument;
use triagens\ArangoDb\DocumentHandler as ArangoDocumentHandler;
use triagens\ArangoDb\CollectionHandler as ArangoCollectionHandler;

/**
 * <h4>ArangoDB collection class.</h4><p />
 *
 * This <em>concrete</em> implementation of the {@link Client} class represents a
 * ArangoDB collection instance which implements the {@link \Milko\wrapper\Collection}
 * interface.
 *
 *	@package	Data
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		18/06/2016
 */
class Collection extends Client
				 implements \Milko\wrapper\Collection
{
	/**
	 * <h4>Document handler.</h4>
	 *
	 * This data member holds the document handler.
	 *
	 * @var ArangoDocumentHandler
	 */
	protected $mDocumentHandler = NULL;

	/**
	 * <h4>Collection handler.</h4>
	 *
	 * This data member holds the collection handler.
	 *
	 * @var ArangoCollectionHandler
	 */
	protected $mCollectionHandler = NULL;




/*=======================================================================================
 *																						*
 *							PUBLIC CLIENT MANAGEMENT INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Clients																			*
	 *==================================================================================*/

	/**
	 * <h4>Get client names.</h4><p />
	 *
	 * TO BE DEVELOPED OR SHADED.
	 *
	 * @return array				List of client names.
	 */
	public function Clients()
	{
		//
		// Connect object.
		//
		$this->Connect();

		return [];																	// ==>

	} // Clients.



/*=======================================================================================
 *																						*
 *									COLLECTION INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Drop																			*
	 *==================================================================================*/

	/**
	 * <h4>Drop collection.</h4><p />
	 *
	 * We implement this method by using the drop() method of the collection handler class
	 * after ensuring the collection is connected.
	 *
	 * We then disconnect the collection.
	 *
	 * @uses isConnected()
	 * @uses Connection()
	 * @uses Path()
	 * @uses Disconnect()
	 * @uses ArangoCollectionHandler::drop()
	 *
	 * @example
	 * <code>
	 * // Instantiate server.
	 * $server = new Server( 'tcp://localhost:8529?createCollection=1' );
	 *
	 * // Instantiate database "Database".
	 * $database = $server->Client( "Database", [] );
	 *
	 * // Add collection.
	 * $collection = $database->Client( "Collection", [] );
	 *
	 * // Write some data.
	 *
	 * // Drop the collection.
	 * $collection->Drop();
	 *
	 * // Restore empty collection.
	 * $collection->Connect();
	 *
	 * // Now you have an empty collection.
	 * </code>
	 */
	public function Drop()
	{
		//
		// Check if connected.
		//
		if( $this->isConnected() )
		{
			//
			// Drop collection.
			//
			if( $this->Connection()->getId() !== NULL )
				$this->mCollectionHandler->drop( $this->Path() );

			//
			// Disconnect collection.
			//
			$this->Disconnect();

		} // Is connected.

	} // Drop.


	/*===================================================================================
	 *	Records																			*
	 *==================================================================================*/

	/**
	 * <h4>Return record count.</h4><p />
	 *
	 * We use the count() method of the collection handler.
	 *
	 * @return int					Collection record count.
	 *
	 * @uses isConnected()
	 * @uses Connect()
	 * @uses Connection()
	 * @uses Connection()
	 * @uses ArangoCollectionHandler::count()
	 */
	public function Records()
	{
		//
		// Connect object.
		//
		$this->Connect();

		return $this->mCollectionHandler->count(
			$this->Connection()->getName()
		);																			// ==>

	} // Records.



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
	 * We use the document handler save() method.
	 *
	 * @param mixed					$theDocument		Document to store.
	 * @return mixed				The document key.
	 *
	 * @uses Connect()
	 * @uses Connection()
	 * @uses Container::convertToArray()
	 * @uses ArangoDocumentHandler::save()
	 */
	public function AddOne( $theDocument )
	{
		//
		// Connect object.
		//
		$this->Connect();

		//
		// Flatten to array.
		//
		Container::convertToArray( $theDocument );

		return
			$this->mDocumentHandler
				->save( $this->Connection(), $theDocument );						// ==>

	} // AddOne.



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
	 * In ArangoDB the document key is <tt>_key</tt>.
	 *
	 * @return string				The document key property name.
	 */
	static function DocumentKey()
	{
		return '_key';																// ==>

	} // DocumentKey.


	/*===================================================================================
	 *	DocumentRevision																*
	 *==================================================================================*/

	/**
	 * <h4>Return the document revision.</h4><p />
	 *
	 * In ArangoDB the document revision is <tt>_rev</tt>.
	 *
	 * @return string				The document revision property name.
	 */
	static function DocumentRevision()
	{
		return '_rev';																// ==>

	} // DocumentRevision.


	/*===================================================================================
	 *	ToNativeDocument																*
	 *==================================================================================*/

	/**
	 * <h4>Convert to a native document.</h4><p />
	 *
	 * We implement this method to create a {@link triagens\ArangoDb\Document} instance, we
	 * also ensure the internal key and revision properties of the native document are set.
	 *
	 * @param mixed					$theDocument		Document to convert.
	 * @return mixed				The native document.
	 */
	static function ToNativeDocument( $theDocument )
	{
		//
		// Skip native documents.
		//
		if( $theDocument instanceof ArangoDocument )
			return $theDocument;													// ==>

		//
		// Flatten to array.
		//
		Container::convertToArray( $theDocument );

		//
		// Create native document.
		//
		$document = ArangoDocument::createFromArray( $theDocument );

		//
		// Set key.
		//
		if( ($document->getKey() === NULL)
		 && array_key_exists( static::DocumentKey(), $theDocument ) )
			$document->setInternalKey( $theDocument[ static::DocumentKey() ] );

		//
		// Set revision.
		//
		if( ($document->getRevision() === NULL)
		 && array_key_exists( static::DocumentRevision(), $theDocument ) )
			$document->setRevision( $theDocument[ static::DocumentRevision() ] );

		return $document;															// ==>

	} // ToNativeDocument.



/*=======================================================================================
 *																						*
 *								PROTECTED CONNECTION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	connectionDrop																	*
	 *==================================================================================*/

	/**
	 * <h4>Drop connection.</h4><p />
	 *
	 * We overload this method to clear the document and collection handlers.
	 *
	 * @return bool					<tt>TRUE</tt> if it was disconnected.
	 */
	protected function connectionDrop()
	{
		//
		// Clear handlers.
		//
		if( parent::connectionDrop() )
		{
			//
			// Reset handlers.
			//
			$this->mDocumentHandler = NULL;
			$this->mCollectionHandler = NULL;

			return TRUE;															// ==>

		} // Was disconnected.

		return FALSE;																// ==>

	} // connectionDrop.


	/*===================================================================================
	 *	connectionCreate																*
	 *==================================================================================*/

	/**
	 * <h4>Open connection.</h4><p />
	 *
	 * We implement this method by using the current object's {@link URL()} data source
	 * name as the connection string, stripped from the options that are sent to the native
	 * {@link Client} constructor.
	 *
	 * @return ArangoCollection		The native connection.
	 *
	 * @uses Client::__construct()
	 */
	protected function connectionCreate()
	{
		//
		// Normalise options.
		//
		$options = $this->Options();
		if( $options === NULL )
			$options = [];

		//
		// Store document handler.
		//
		$this->mDocumentHandler
			= new ArangoDocumentHandler( $this->Server()->Connection() );

		//
		// Store collection handler.
		//
		$this->mCollectionHandler
			= new ArangoCollectionHandler( $this->Server()->Connection() );

		//
		// Return existing collection.
		//
		if( $this->mCollectionHandler->has( $this->Path() ) )
			return $this->mCollectionHandler->get( $this->Path() );					// ==>

		return
			$this->mCollectionHandler->get(
				$this->mCollectionHandler->create( $this->Path(), $options ) );		// ==>

	} // connectionCreate.


	/*===================================================================================
	 *	connectionDestruct																*
	 *==================================================================================*/

	/**
	 * <h4>Close connection.</h4><p />
	 *
	 * We overload this method to reset the document and collection handlers.
	 */
	protected function connectionDestruct()	{}


	/*===================================================================================
	 *	nestedConnections																*
	 *==================================================================================*/

	/**
	 * <h4>Nested connections flag.</h4><p />
	 *
	 * We reset the flag to <tt>OFF</tt> for ArangoDB collection clients.
	 *
	 * @return bool					<tt>TRUE</tt> to cascade connections and disconnectons.
	 */
	protected function nestedConnections()
	{
		return FALSE;																// ==>

	} // nestedConnections.



/*=======================================================================================
 *																						*
 *								PROTECTED CLIENT INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	clientCreate()																	*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate client.</h4><p />
	 *
	 * We implement this method to return a {@link Database} instance.
	 *
	 * @return Client				The {@link Client} instance.
	 */
	protected function clientCreate()
	{
		return new Collection( $this );												// ==>

	} // clientCreate.


	/*===================================================================================
	 *	clientDestruct																	*
	 *==================================================================================*/

	/**
	 * <h4>Close client connection.</h4><p />
	 *
	 * In this method we do nothing.
	 *
	 * @param Client				$theClient			Client instance.
	 */
	protected function clientDestruct( Client $theClient )	{}



/*=======================================================================================
 *																						*
 *								PROTECTED DATABASE INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	serverCreate																	*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate server.</h4><p />
	 *
	 * We implement this method to instantiate an ArangoDB server and a database instance
	 * according to the current object's {@link Path()}.
	 *
	 * The method will first instantiate the {@link Server} instance, it will then use the
	 * first element of the {@link Path()} to instantiate the {@link Database} and strip
	 * that element from the current path; any subsequent path elements will be handled by
	 * the constructor to add sub-clients to the current object.
	 *
	 * If the current object has less than 2 elements in the path, the method will raise an
	 * exception.
	 *
	 * @return Client				The database instance.
	 * @throws \RuntimeException
	 */
	protected function serverCreate()
	{
		//
		// Check path.
		//
		if( $this->mPath !== NULL )
		{
			//
			// Get elements.
			//
			$tmp = explode( '/', $this->mPath );
			if( count( $tmp ) > 1 )
			{
				//
				// Instantiate anonymous server.
				//
				$server =
					new Server(
						$this->URL(
							NULL,
							[
								self::kTAG_USER,
								self::kTAG_PATH,
								self::kTAG_OPTS,
								self::kTAG_FRAG
							]
						)
					);

				//
				// Instantiate database.
				// We also strip the name from the path list.
				//
				$database = $server->Client( array_shift( $tmp ) );

				//
				// Update path excluding the database name.
				//
				$this->Path( implode( '/', $tmp ) );

				return $database;													// ==>

			} // Has at least two elements.

			throw new \RuntimeException(
				"Missing database name in path."
			);																	// !@! ==>

		} // Has path.

		throw new \RuntimeException(
			"Missing path."
		);																		// !@! ==>

	} // serverCreate.




} // class Collection.


?>
