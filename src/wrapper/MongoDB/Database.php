<?php

/**
 * Database.php
 *
 * This file contains the definition of the {@link Database} class.
 */

namespace Milko\wrapper\MongoDB;

/*=======================================================================================
 *																						*
 *									Database.php										*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Client;
use Milko\wrapper\ClientServer;

/**
 * <h4>MongoDB database class.</h4><p />
 *
 * This <em>concrete</em> implementation of the {@link Client} class represents a
 * MongoDB database instance, it implements an object that manages a list of MongoDB
 * collections wrapped around the {@link Milko\PHPLib\MongoDB\Database} class.
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
 * // Instantiate database "Database".
 * $database = new Database( $server, "mongodb://localhost:27017/Database" );
 *
 * // Instantiate database "Database" and add it to server clients.
 * $database = $server->Client( "Database", [] );
 *
 * // Instantiate server and database.
 * $server = new Server( 'mongodb://localhost:27017/Database' );
 * $database = $server->Client( "Database" );
 * $database = $server[ "Database" ];
 * </code>
 */
class Database extends Client
			   implements \Milko\wrapper\Database
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
	 * We implement this method to return the list of collections hosted by the current
	 * database.
	 *
	 * @return array				List of client names.
	 *
	 * @uses isConnected()
	 * @uses Connect()
	 * @uses Connection()
	 * @uses \MongoDB\Database::listCollections()
	 */
	public function Clients()
	{
		//
		// Connect object.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Init local storage.
		//
		$collections = [];

		//
		// Ask client for list.
		//
		$list = $this->Connection()->listCollections();
		foreach( $list as $element )
			$collections[ $element->getName() ] = $element;

		return $collections;														// ==>

	} // Clients.



/*=======================================================================================
 *																						*
 *								PUBLIC DATABASE INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Drop																			*
	 *==================================================================================*/

	/**
	 * <h4>Drop database.</h4><p />
	 *
	 * We implement this method by using the drop() method of the MongoDB database class
	 * after ensuring the database is connected.
	 *
	 * We don't disconnect the database because this is not needed.
	 *
	 * @uses isConnected()
	 * @uses Connect()
	 * @uses Connection()
	 * @uses \MongoDB\Database::drop()
	 */
	public function Drop()
	{
		//
		// Connect object.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Drop database.
		//
		$this->Connection()->drop();

	} // Drop.



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
	 * We implement this method by using the current object's {@link URL()} data source
	 * name as the connection string, stripped from the options that are sent to the native
	 * {@link Client} constructor.
	 *
	 * @return mixed				The native connection.
	 *
	 * @uses Options()
	 * @uses Server()
	 * @uses \MongoDB\Client::selectDatabase()
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
				->selectDatabase(
					$this->Path(),
					$options
				);																	// ==>

	} // connectionCreate.


	/*===================================================================================
	 *	connectionDestruct																*
	 *==================================================================================*/

	/**
	 * Close connection.
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
	 *	clientCreate																	*
	 *==================================================================================*/

	/**
	 * Instantiate client.
	 *
	 * We implement this method to return a {@link Database} instance.
	 *
	 * @return Client				The {@link Client} instance.
	 */
	protected function clientCreate()
	{
		return new Collection( NULL, $this );										// ==>

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
	 * We implement this method to instantiate a MongoDB server instance according to the
	 * current object attributes.
	 *
	 * We provide the current object's data source name, excluding the credentials, path,
	 * options and fragment, to the {@link Server} constructor.
	 *
	 * @return Server				The server instance.
	 */
	protected function serverCreate()
	{
		return new Server(
			$this->URL(
				NULL,
				[
					self::kTAG_USER,
					self::kTAG_PATH,
					self::kTAG_OPTS,
					self::kTAG_FRAG
				]
			)
		);																			// ==>

	} // serverCreate.




} // class Database.


?>
