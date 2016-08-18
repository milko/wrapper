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

use triagens\ArangoDb\CollectionHandler as ArangoCollectionHandler;
use triagens\ArangoDb\Database as ArangoDatabase;
use triagens\ArangoDb\Connection as ArangoConnection;

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
 */
class Database extends Client
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
		// Init local storage.
		//
		$name = $this->Path();
		$clients = $this->Server()->Clients();
		$connection = new ArangoConnection( $this->Server()->ConnectionOptions() );

		//
		// Create database.
		//
		if( ! in_array( $name, $clients ) )
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
	protected function connectionDestruct()
	{

	} // connectionDestruct.



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
	protected function clientDestruct( Client $theClient )
	{

	} // clientDestruct.




} // class Database.


?>
