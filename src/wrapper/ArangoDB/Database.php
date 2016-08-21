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

use triagens\ArangoDb\Database as ArangoDatabase;
use triagens\ArangoDb\Connection as ArangoConnection;
use triagens\ArangoDb\CollectionHandler as ArangoCollectionHandler;
use triagens\ArangoDb\ConnectionOptions as ArangoConnectionOptions;

/**
 * <h4>ArangoDB database class.</h4><p />
 *
 * This <em>concrete</em> implementation of the {@link Client} class represents an
 * ArangoDB database instance, it implements an object that manages a list of ArangoDB
 * collections wrapped around the {@link Milko\PHPLib\ArangoDB\Database} class.
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
 * $server = new Server( 'tcp://localhost:8529?createCollection=1' );
 *
 * // Instantiate database "Database".
 * $database = new Database( $server, "tcp://localhost:8529/Database?createCollection=1" );
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
	 * @uses Connection()
	 * @uses Client::listDatabases()
	 */
	public function Clients()
	{
		//
		// Connect object.
		//
		$this->Connect();

		//
		// Instantiate collection handler.
		//
		$handler = new ArangoCollectionHandler( $this->Connection() );

		return
			array_keys(
				$handler->getAllCollections(
					[
						'excludeSystem' => TRUE,
						'keys' => 'names'
					]
				)
			);																		// ==>

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
	 * We implement this method by using the drop() method of the ArangoDB database class
	 * after ensuring the database is connected.
	 *
	 * We then disconnect the database.
	 *
	 * @uses isConnected()
	 * @uses Connect()
	 * @uses Connection()
	 * @uses Path()
	 * @uses Server()
	 * @uses ArangoDatabase::delete()
	 */
	public function Drop()
	{
		//
		// Connect object.
		//
		$this->Connect();

		//
		// Drop database.
		//
		ArangoDatabase::delete( $this->Server()->Connection(), $this->Path() );

		//
		// Disconnect database.
		//
		$this->Disconnect();

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
	 * @uses Client::__construct()
	 */
	protected function connectionCreate()
	{
		//
		// Create connection.
		//
		$name = $this->Path();
		$options = $this->Server()->ConnectionOptions();
		$options[ ArangoConnectionOptions::OPTION_DATABASE ] = '_system';
		$connection = new ArangoConnection( $options );

		//
		// Create database.
		//
		if( ! in_array( $name, ArangoDatabase::listUserDatabases( $connection )[ 'result' ] ) )
			ArangoDatabase::create( $connection, $name );

		//
		// Add database to connection.
		//
		$connection->setDatabase( $name );

		return $connection;															// ==>

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
	 * current object components.
	 *
	 * @param string				$theConnection		Data source name.
	 * @return Server				The parent instance.
	 */
	protected function serverCreate( string $theConnection = NULL )
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
