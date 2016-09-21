<?php

/**
 * Collection.php
 *
 * This file contains the definition of the {@link Milko\wrapper\MongoDB\Collection} class.
 */

namespace Milko\wrapper\MongoDB;

/*=======================================================================================
 *																						*
 *									Collection.php										*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Client;
use Milko\wrapper\Container;

use MongoDB\Model\BSONDocument;

/**
 * <h4>MongoDB collection class.</h4><p />
 *
 * This <em>concrete</em> implementation of the {@link Client} class represents a
 * MongoDB collection instance which implements the {@link \Milko\wrapper\Collection}
 * interface.
 *
 *	@package	Data
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		18/06/2016
 *
 * @example
 * <code>
 * // Instantiate server.
 * $server = new Server( 'mongodb://localhost:27017' );
 *
 * // Instantiate database "Database" and add it to server clients.
 * $database = $server->Client( "Database", [] );
 *
 * // Instantiate collection "Collection" and add it to database clients.
 * $collection = $database->Client( "Collection", [] );
 *
 * // Instantiate server, database and collection retrieving collection.
 * $collection = Server::NewConnection( "mongodb://localhost:27017/database/collection" );
 * </code>
 */
class Collection extends Client
				 implements \Milko\wrapper\Collection
{



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
		return [];																	// ==>

	} // Clients.



/*=======================================================================================
 *																						*
 *						PUBLIC CLIENT INSTANTIATION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	NewClient																		*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate client.</h4><p />
	 *
	 * We overload this method to prevent collections from having clients: if you create a
	 * collection client, this will become a client of the current collection's database.
	 *
	 * This feature can be used to create a set of collections when instantiating a server:
	 * for instance <tt>$server = new Server( "tcp://localhost:8529/db/cl1/cl2/cl3" );</tt>
	 * will create a database named <tt>db</tt> with three collections named <tt>cl1</tt>,
	 * <tt>cl2</tt> and <tt>cl3</tt>. This also means that if you provide this connection
	 * string to the {@link Server::NewConnection()} method, it will return the <tt>cl3</tt>
	 * collection.
	 *
	 * <em>Note that the options parameter will be passed to the database
	 * {@link Database::Client()} method.</tt>
	 *
	 * @param string				$theName			Client name.
	 * @param array					$theOptions			Creation options.
	 * @return Client				The {@link Client} instance.
	 *
	 * @example
	 * <code>
	 * // Instantiate collection.
	 * $collection = $database->NewClient( "Collection" );
	 *
	 * // Add a collection to the database.
	 * $collection2 = $collection->NewClient( "NEW" );
	 * </code>
	 */
	public function NewClient( string $theName, array $theOptions = [] )
	{
		return $this->Server()->NewClient( $theName, $theOptions );					// ==>

	} // NewClient.



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
	 * We implement this method by using the drop() method of the MongoDB collection class
	 * after ensuring the collection is connected.
	 *
	 * @uses isConnected()
	 * @uses Connect()
	 * @uses Connection()
	 * @uses \MongoDB\Collection::drop()
	 *
	 * @example
	 * <code>
	 * // Instantiate server.
	 * $server = new Server( 'mongodb://localhost:27017' );
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
		// Connect object.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Drop collection.
		//
		$this->Connection()->drop();

	} // Drop.


	/*===================================================================================
	 *	Records																			*
	 *==================================================================================*/

	/**
	 * <h4>Return record count.</h4><p />
	 *
	 * We use the count() method of the connection.
	 *
	 * @return int					Collection record count.
	 *
	 * @uses isConnected()
	 * @uses Connect()
	 * @uses Connection()
	 * @uses \MongoDB\Collection::count()
	 */
	public function Records()
	{
		//
		// Connect object.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		return $this->Connection()->count();										// ==>

	} // Records.


	/*===================================================================================
	 *	AddOne																			*
	 *==================================================================================*/

	/**
	 * <h4>Insert a document.</h4><p />
	 *
	 * We use the insertOne() method.
	 *
	 * @param mixed					$theDocument		Document to store.
	 * @return mixed				The document key.
	 *
	 * @uses Connect()
	 * @uses Connection()
	 * @uses Container::convertToArray()
	 * @uses \MongoDB\Collection::insertOne()
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
			$this->Connection()->insertOne( $theDocument )
				 ->getInsertedId();													// ==>

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
	 * In MongoDB the document key is <tt>_id</tt>.
	 *
	 * @return string				The document key property name.
	 */
	static function DocumentKey()
	{
		return '_id';																// ==>

	} // DocumentKey.


	/*===================================================================================
	 *	DocumentRevision																*
	 *==================================================================================*/

	/**
	 * <h4>Return the document revision.</h4><p />
	 *
	 * In MongoDB there is no default revision, we set this to the <tt>_rev</tt> token.
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
	 * We implement this method to create a {@link BSONDocument} instance.
	 *
	 * @param mixed					$theDocument		Document to convert.
	 * @return mixed				The native document.
	 */
	static function ToNativeDocument( $theDocument )
	{
		//
		// Skip native documents.
		//
		if( $theDocument instanceof BSONDocument )
			return $theDocument;													// ==>

		//
		// Flatten to array.
		//
		Container::convertToArray( $theDocument );

		return new BSONDocument( $theDocument );									// ==>

	} // ToNativeDocument.



/*=======================================================================================
 *																						*
 *								PROTECTED CONNECTION INTERFACE							*
 *																						*
 *======================================================================================*/



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
	 * @return Collection			The native connection.
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

		return
			$this->Server()
				->Connection()
				->selectCollection(
					$this->Path(),
					$options
				);																	// ==>

	} // connectionCreate.


	/*===================================================================================
	 *	connectionDestruct																*
	 *==================================================================================*/

	/**
	 * <h4>Close connection.</h4><p />
	 *
	 * In this method we do nothing.
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
 *								PROTECTED SERVER INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	serverCreate																	*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate server.</h4><p />
	 *
	 * We implement this method to instantiate a MongoDB server and a database instance
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
