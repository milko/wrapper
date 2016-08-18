<?php

/**
 * ClientServer.php
 *
 * This file contains the definition of the {@link ClientServer} class.
 */

namespace Milko\wrapper\MongoDB;

/*=======================================================================================
 *																						*
 *									ClientServer.php									*
 *																						*
 *======================================================================================*/

use Milko\wrapper\ClientServer;
use MongoDB\Client;

/**
 * <h4>MongoDB client class.</h4><p />
 *
 * This <em>concrete</em> implementation of the {@link ClientServer} class represents a
 * MongoDB database server, it implements an object that manages a list of MongoDB databases
 * wrapped around the {@link Milko\PHPLib\MongoDB\Client} class.
 *
 *	@package	Data
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		18/06/2016
 */
class Server extends ClientServer
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
	 * We implement this method to return the list of databases hosted by the current
	 * server.
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
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Init local storage.
		//
		$databases = [];

		//
		// Ask client for list.
		//
		$list = $this->Connection()->listDatabases();
		foreach( $list as $element )
			$databases[ $element->getName() ] = $element;

		return $databases;															// ==>

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
	 * @uses \MongoDB\Client::__construct()
	 */
	protected function connectionCreate()
	{
		//
		// Normalise options.
		//
		$options = $this->Options();
		if( $options === NULL )
			$options = [];

		return new Client(
			$this->URL(
				NULL,
				[ self::kTAG_PATH, self::kTAG_OPTS, self::kTAG_FRAG ]
			),
			$options
		);																			// ==>

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
	 * @param string					$theName	Client name.
	 * @param array						$theOptions	Creation options.
	 * @return \Milko\wrapper\Client	The {@link Client} instance.
	 */
	protected function clientCreate()
	{
		return new Database( $this );												// ==>

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
	protected function clientDestruct( \Milko\wrapper\Client $theClient )
	{

	} // clientDestruct.




} // class Server.


?>
