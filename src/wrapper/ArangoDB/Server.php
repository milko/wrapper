<?php

/**
 * Server.php
 *
 * This file contains the definition of the ArangoDB {@link Server} class.
 */

namespace Milko\wrapper\ArangoDB;

/*=======================================================================================
 *																						*
 *										Server.php										*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Client;
use Milko\wrapper\ClientServer;

use triagens\ArangoDb\Database as ArangoDatabase;
use triagens\ArangoDb\Connection as ArangoConnection;
use triagens\ArangoDb\UpdatePolicy as ArangoUpdatePolicy;
use triagens\ArangoDb\ConnectionOptions as ArangoConnectionOptions;

/**
 * <h4>ArangoDB client class.</h4><p />
 *
 * This <em>concrete</em> implementation of the {@link ClientServer} class represents an
 * ArangoDB database server, it implements an object that manages a list of ArangoDB
 * databases wrapped around the {@link triagens\ArangoDb\Connection} class.
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
 * // List databases.
 * $list = $server->Clients();
 *
 * // Instantiate database.
 * $database = $server->Client( "DatabaseName", [] );
 * // Start working with database...
 * </code>
 */
class Server extends ClientServer
{
	/**
	 * <h4>Nested connections flag.</h4><p />
	 *
	 * We set the flag on to cascade connections and disconnections.
	 *
	 * See {@link ClientServer::$mNestedConnections}.
	 *
	 * @var bool
	 */
	static $mNestedConnections = TRUE;




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
	 * @uses ConnectionOptions()
	 * @uses ArangoDatabase::listUserDatabases()
	 * @uses ArangoDatabase::getInfo()
	 */
	public function Clients()
	{
		//
		// Connect object.
		//
		if( ! $this->isConnected() )
			$this->Connect();

		//
		// Get user databases list.
		//
		$list = ArangoDatabase::listUserDatabases( $this->Connection() )[ 'result' ];

		//
		// Prepare result.
		//
		$clients = [];
		$options = $this->ConnectionOptions();
		foreach( $list as $name )
			$clients[ $name ] =
				ArangoDatabase::getInfo(
					new ArangoConnection(
						array_merge(
							$options,
							[ ArangoConnectionOptions::OPTION_DATABASE => $name ]
						)
					)
				)[ 'result' ];

		return $clients;															// ==>

	} // Clients.



/*=======================================================================================
 *																						*
 *							PUBLIC OPTIONS MANAGEMENT INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	ConnectionOptions																*
	 *==================================================================================*/

	/**
	 * <h4>Return connection options.</h4>
	 *
	 * This method is used to return an array of connection options in ArangoDB format
	 * according to the current object's settings.
	 *
	 * This method is public because there is the need to clone the server connection.
	 *
	 * @return array				Connection options.
	 *
	 * @uses Options()
	 * @uses Protocol()
	 * @uses Host()
	 * @uses Port()
	 * @uses User()
	 * @uses Password()
	 */
	public function ConnectionOptions()
	{
		//
		// Init local storage.
		//
		$options = ( is_array( $tmp = $this->Options() ) )
			? $tmp
			: [];

		//
		// Set endpoint.
		//
		$endpoint = $this->Protocol() . '://' . $this->Host();
		if( ($tmp = $this->Port()) !== NULL )
			$endpoint .= ":$tmp";
		$options[ ArangoConnectionOptions::OPTION_ENDPOINT ]
			= $this->URL(
			NULL,
			[
				self::kTAG_USER,
				self::kTAG_PATH,
				self::kTAG_OPTS,
				self::kTAG_FRAG
			]
		);

		//
		// Set authorisation type.
		//
		if( ! array_key_exists( ArangoConnectionOptions::OPTION_AUTH_TYPE, $options ) )
			$options[ ArangoConnectionOptions::OPTION_AUTH_TYPE ]
				= 'Basic';

		//
		// Set user.
		//
		if( ($tmp = $this->User()) !== NULL )
			$options[ ArangoConnectionOptions::OPTION_AUTH_USER ] = $tmp;

		//
		// Set password.
		//
		if( ($tmp = $this->Password()) !== NULL )
			$options[ ArangoConnectionOptions::OPTION_AUTH_PASSWD ] = $tmp;

		//
		// Set connection persistence.
		//
		if( ! array_key_exists( ArangoConnectionOptions::OPTION_CONNECTION, $options ) )
			$options[ ArangoConnectionOptions::OPTION_CONNECTION ]
				= 'Keep-Alive';

		//
		// Set connection time-out.
		//
		$options[ ArangoConnectionOptions::OPTION_TIMEOUT ]
			= ( array_key_exists( ArangoConnectionOptions::OPTION_TIMEOUT, $options ) )
			? (int)$options[ ArangoConnectionOptions::OPTION_TIMEOUT ]
			: 3;

		//
		// Set time-out reconnect.
		//
		$options[ ArangoConnectionOptions::OPTION_RECONNECT ]
			= ( array_key_exists( ArangoConnectionOptions::OPTION_RECONNECT, $options ) )
			? (bool)$options[ ArangoConnectionOptions::OPTION_RECONNECT ]
			: TRUE;

		//
		// Set creation option.
		//
		$options[ ArangoConnectionOptions::OPTION_CREATE ]
			= ( array_key_exists( ArangoConnectionOptions::OPTION_CREATE, $options ) )
			? (bool)$options[ ArangoConnectionOptions::OPTION_CREATE ]
			: TRUE;

		//
		// Set update policy.
		//
		if( ! array_key_exists( ArangoConnectionOptions::OPTION_UPDATE_POLICY, $options ) )
			$options[ ArangoConnectionOptions::OPTION_UPDATE_POLICY ]
				= ArangoUpdatePolicy::LAST;

		return $options;															// ==>

	} // ConnectionOptions.



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
	 * @uses ConnectionOptions()
	 * @uses ArangoConnection::__construct()
	 */
	protected function connectionCreate()
	{
		//
		// Set connection options.
		//
		$options = $this->ConnectionOptions();
		$options[ ArangoConnectionOptions::OPTION_DATABASE ] = '_system';

		return new ArangoConnection( $options );									// ==>

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
		return new Database( NULL, $this );											// ==>

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




} // class Server.


?>
