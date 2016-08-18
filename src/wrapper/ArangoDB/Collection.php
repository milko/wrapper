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
 */
class Collection extends Client
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




} // class Collection.


?>
