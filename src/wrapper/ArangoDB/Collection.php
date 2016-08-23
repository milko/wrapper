<?php

/**
 * Database.php
 *
 * This file contains the definition of the {@link Database} class.
 */

namespace Milko\wrapper\ArangoDB;

/*=======================================================================================
 *																						*
 *									Database.php										*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Client;
use Milko\wrapper\Container;
use Milko\wrapper\ClientServer;

use triagens\ArangoDb\Collection as ArangoCollection;
use triagens\ArangoDb\DocumentHandler as ArangoDocumentHandler;
use triagens\ArangoDb\CollectionHandler as ArangoCollectionHandler;
use triagens\ArangoDb\ServerException as ArangoServerException;

/**
 * <h4>ArangoDB database class.</h4><p />
 *
 * This <em>concrete</em> implementation of the {@link Client} class represents a
 * ArangoDB database instance, it implements an object that manages a list of ArangoDB
 * collections wrapped around the {@link Milko\PHPLib\ArangoDB\Database} class.
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
 *								PUBLIC COLLECTION INTERFACE								*
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
	 * @uses Connect()
	 * @uses Connection()
	 * @uses ArangoCollectionHandler::drop()
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
				$this->mCollectionHandler->drop( $this->Connection()->getName() );

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


	/*===================================================================================
	 *	SetOne																			*
	 *==================================================================================*/

	/**
	 * <h4>Store a document.</h4><p />
	 *
	 * We use the document handler replaceById() method for existing objects, or the
	 * save() method for new ones.
	 *
	 * @param mixed					$theDocument		Document to store.
	 * @return mixed				The document key.
	 */
	public function SetOne( $theDocument )
	{
		//
		// Connect object.
		//
		$this->Connect();

		//
		// Init local storage.
		//
		$name = $this->Connection()->getName();
		Container::convertToArray( $theDocument );

		//
		// Check key.
		//
		if( array_key_exists( '_key', $theDocument ) )
		{
			//
			// Check if it exists.
			//
			if( $this->mDocumentHandler->has( $name, $theDocument[ '_key' ] ) )
			{
				$this->mDocumentHandler
					->replaceById( $name, $theDocument[ '_key' ], $theDocument );

				return $theDocument[ '_key' ];										// ==>

			} // Document exists.

		} // Has key.

		return
			$this->mDocumentHandler->save( $this->Connection(), $theDocument );		// ==>

	} // SetOne.



/*=======================================================================================
 *																						*
 *								PROTECTED CONNECTION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	connectionDrop																	*
	 *==================================================================================*/

	/**
	 * Drop connection.
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
	 * Open connection.
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
	 * Close connection.
	 *
	 * We overload this method to reset the document and collection handlers.
	 */
	protected function connectionDestruct()	{}



/*=======================================================================================
 *																						*
 *								PROTECTED DATABASE INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	clientCreate()																	*
	 *==================================================================================*/

	/**
	 * Instantiate client.
	 *
	 * We implement this method to return a {@link Database} instance.
	 *
	 * @param string				$theName			Client name.
	 * @param array					$theOptions			Creation options.
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
	 * Close client connection.
	 *
	 * In this method we do nothing.
	 *
	 * @param Client				$theClient			Client instance.
	 */
	protected function clientDestruct( Client $theClient )	{}



/*=======================================================================================
 *																						*
 *								PROTECTED SERVER INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	serverCreate																	*
	 *==================================================================================*/

	/**
	 * Instantiate server.
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
