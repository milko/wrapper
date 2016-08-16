<?php

/**
 * Server.php
 *
 * This file contains the definition of the {@link Server} class.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *									Server.php											*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Url;

/**
 * <h4>Server object.</h4><p />
 *
 * This <em>abstract</em> class is the ancestor of all classes representing server
 * instances.
 *
 * The class is derived from the {@link Url} class and uses its properties to store the list of working databases.
 *
 * The class features two attributes:
 *
 * <ul>
 * 	<li><tt>{@link $mDatasource}</tt>: This attribute contains a {@link Datasource} instance
 * 		which stores the server's data source name.
 * 	<li><tt>{@link $mConnection}</tt>: This attribute contains the server native connection
 * 		object, this is instantiated when the server connects.
 * </ul>
 *
 * The class implements the {@link iDatasource} interface which manages the server's
 * connection parameters and a public interface that takes care of connecting,
 * disconnecting, sleeping and waking the object, the implementation of the connection
 * workflow is delegated to a protected interface which is virtual and must be implemented
 * by concrete derived classes.
 *
 * The sleep and wake workflow ensures that the connection is closed before the object
 * goes to sleep and opened when it wakes, this is to handle native connection objects that
 * cannot be serialised in the session.
 *
 * When a connection is open, none of the {@link Datasource} properties can be modified,
 * attempting to do so will trigger an exception.
 *
 * The class implements the following public interface:
 *
 * <ul>
 * 	<li>Connections:
 * 	 <ul>
 * 		<li><b>{@link Connect()}</b>: Connect the server.
 * 		<li><b>{@link Disconnect()}</b>: Disconnect the server.
 * 	 </ul>
 * 	<li>Connection status:
 * 	 <ul>
 * 		<li><b>{@link Connection()}</b>: Return the current server native connection.
 * 		<li><b>{@link isConnected()}</b>: Return the connection status.
 * 	 </ul>
 * 	<li>Database management:
 * 	 <ul>
 * 		<li><b>{@link NewDatabase()}</b>: Create a {@link Database} instance.
 * 		<li><b>{@link GetDatabase()}</b>: Return an existing {@link Database} instance.
 * 		<li><b>{@link DelDatabase()}</b>: Drop a {@link Database} instance.
 * 		<li><b>{@link ListDatabases()}</b>: List server databases.
 * 	 </ul>
 * 	<li>Working database management:
 * 	 <ul>
 * 		<li><b>{@link ListWorkingDatabases()}</b>: Return working database instances.
 * 		<li><b>{@link ForgetWorkingDatabase()}</b>: Unregister working database.
 * 	 </ul>
 * </ul>
 *
 * The class declares the following protected interface which must be implemented in derived
 * concrete classes:
 *
 * <ul>
 * 	<li>Connections:
 * 	 <ul>
 * 		<li><b>{@link connectionCreate()}</b>: Create a native connection.
 * 		<li><b>{@link connectionDestruct()}</b>: Close a native connection.
 * 	 </ul>
 * 	<li>Database management:
 * 	 <ul>
 * 		<li><b>{@link databaseCreate()}</b>: Create a {@link Database} instance.
 * 		<li><b>{@link databaseRetrieve()}</b>: Return an existing {@link Database} instance.
 * 		<li><b>{@link databaseList()}</b>: List server databases.
 * 	 </ul>
 * </ul>
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		06/02/2016
 *
 *	@example	/test/Server.php
 *	@example
 * <code>
 * $server = new Milko\wrapper\Server( 'protocol://user:pass@host:9090' );
 * $connection = $server->Connect();
 * </code>
 */
abstract class Server extends Url
{
	/**
	 * <h4>Server connection object.</h4><p />
	 *
	 * This data member holds the <i>server connection object</i>, it is the native object
	 * implementing the server connection.
	 *
	 * Before the object goes to sleep ({@link __sleep()}), this attribute will be set to
	 * <tt>TRUE</tt> if a connection was open and to <tt>NULL</tt> if not: this determines
	 * whether a connection should be restored when the object is waken (@link __wakeup()}).
	 *
	 * @var mixed
	 */
	protected $mConnection = NULL;




/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	__destruct																		*
	 *==================================================================================*/

	/**
	 * <h4>Destruct instance.</h4><p />
	 *
	 * In this class we close any open connection before disposing of the object.
	 *
	 * @uses Disconnect()
	 */
	public function __destruct()
	{
		//
		// Disconnect.
		//
		$this->Disconnect();

	} // __destruct.


	/*===================================================================================
	 *	__sleep																			*
	 *==================================================================================*/

	/**
	 * <h4>Put the object to sleep.</h4><p />
	 *
	 * This method will close the connection and replace the connection resource with
	 * <tt>TRUE</tt> if the connection was open, this will be used by the {@link __wakeup()}
	 * method to re-open the connection.
	 *
	 * @uses Disconnect()
	 */
	public function __sleep()
	{
		//
		// Signal there was a connection.
		//
		$this->mConnection = ( $this->Disconnect() ) ? TRUE : NULL;

	} // __sleep.


	/*===================================================================================
	 *	__wakeup																		*
	 *==================================================================================*/

	/**
	 * <h4>Wake the object from sleep.</h4><p />
	 *
	 * This method will re-open the connection if it was closed by the {@link __sleep()}
	 * method.
	 *
	 * @uses Connect()
	 */
	public function __wakeup()
	{
		//
		// Open closed connection.
		//
		if( $this->mConnection === TRUE )
			$this->Connect();

	} // __wakeup.



/*=======================================================================================
 *																						*
 *								PUBLIC ARRAY ACCESS INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * <h4>Set a value at a given offset.</h4><p />
	 *
	 * We overload this method to prevent modifying object properties when the server is
	 * connected.
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value to set at offset.
	 * @throws \RuntimeException
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Check if connected.
		//
		if( $this->isConnected() )
		{
			//
			// Check relevant offsets.
			//
			if( in_array( $theOffset,
				[
					self::PROT, self::USER, self::PASS, self::HOST,
					self::PORT, self::PATH, self::QUERY, self::FRAG
				]) )
				throw new \RuntimeException(
					"Cannot modify properties while server is connected."
				);																// !@! ==>

		} // Server is connected.

		//
		// Call parent method.
		//
		parent::offsetSet( $theOffset, $theValue );

	} // offsetSet.


	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * <h4>Reset a value at a given offset.</h4><p />
	 *
	 * We overload this method to prevent removing object properties when the server is
	 * connected.
	 *
	 * @param string				$theOffset			Offset.
	 * @throws \BadMethodCallException
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Check if connected.
		//
		if( $this->isConnected() )
		{
			//
			// Check relevant offsets.
			//
			if( in_array( $theOffset,
				[
					self::PROT, self::USER, self::PASS, self::HOST,
					self::PORT, self::PATH, self::QUERY, self::FRAG
				]) )
				throw new \RuntimeException(
					"Cannot reset properties while server is connected."
				);																// !@! ==>

		} // Server is connected.

		//
		// Call parent method.
		//
		parent::offsetUnset( $theOffset );

	} // offsetUnset.



/*=======================================================================================
 *																						*
 *							PUBLIC CONNECTION MANAGEMENT INTERFACE						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Connect																			*
	 *==================================================================================*/

	/**
	 * <h4>Open server connection.</h4><p />
	 *
	 * This method should be used to create and open the server connection, if the
	 * connection is already open, the method should do nothing.
	 *
	 * The method should return the native connection object, or raise an exception if
	 * unable to open the connection.
	 *
	 * The method is abstract, concrete derived classes must implement it.
	 *
	 * @return mixed				Native connection object.
	 */
	abstract public function Connect();


	/*===================================================================================
	 *	Disconnect																		*
	 *==================================================================================*/

	/**
	 * <h4>Close server connection.</h4><p />
	 *
	 * This method should be used to close and destruct the server connection, if no
	 * connection was open, the method should do nothing.
	 *
	 * The method should return <tt>TRUE</tt> if it closed a connection
	 *
	 * The method is abstract, concrete derived classes must implement it.
	 *
	 * @return boolean				<tt>TRUE</tt> was connected, <tt>FALSE</tt> wasn't.
	 */
	abstract public function Disconnect();


	/*===================================================================================
	 *	Connection																		*
	 *==================================================================================*/

	/**
	 * <h4>Return native connection object.</h4><p />
	 *
	 * This method will return the native connection object, if a connection is open, or
	 * <tt>NULL</tt> if not.
	 *
	 * @return mixed				Native connection object or <tt>NULL</tt>.
	 */
	public function Connection()
	{
		return $this->mConnection;													// ==>

	} // Connection.


	/*===================================================================================
	 *	isConnected																		*
	 *==================================================================================*/

	/**
	 * <h4>Check if connection is open.</h4><p />
	 *
	 * This method returns a boolean flag indicating whether the connection is open or not.
	 *
	 * @return boolean				<tt>TRUE</tt> is connected.
	 * @throws \RuntimeException
	 */
	public function isConnected()
	{
		return ( $this->mConnection !== NULL );										// ==>

	} // isConnected.




} // class Server.


?>
