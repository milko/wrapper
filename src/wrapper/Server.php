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

/**
 * <h4>Server class.</h4><p />
 *
 * This <em>abstract</em> class is the ancestor of all classes representing server or
 * data source instances.
 *
 * The class uses the {@link Url} trait to implement a data source based to an URL.
 *
 * The class features an attribute, <tt>{@link $mConnection}</tt>, that represents the
 * native server connection object: a protected abstract method, {@link connectionCreate()},
 * must be implemented by derived concrete classes to instantiate the connection.
 *
 * The sleep and wake workflow ensures that the connection is closed before the object
 * goes to sleep and opened when it wakes, this is to handle native connection objects that
 * cannot be serialised in the session.
 *
 * Note that the class doesn't prevent changing the connection parameters while connected,
 * this means that if you do so, the current connection will not reflect the object's
 * attributes.
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
 * </ul>
 *
 * In derived concrete classes you can use the {@link Container} inherited features to
 * implement a list of dependent objects, such as a list of databases for a database server,
 * or a list of collections for a database.
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/06/2016
 *
 * @example
 * <code>
 * // Instantiate empty object.
 * $server = new Server();
 * $server->Protocol( "mongodb" );
 * $server->Host( "localhost" );
 * ...
 * $server->Connect();
 * // Work with server.
 *
 * // Instantiate from data source name.
 * $server = new Server( "mongodb://user:password@localhost:27017?option=value" );
 * $server->Connect();
 * // Work with server.
 * </code>
 */
abstract class Server extends Container
{
	/**
	 * Declare traits.
	 */
	use Url;

	/**
	 * Database name tag.
	 *
	 * This represents the resource name tag provided in options parameters.
	 */
	const kOPTION_NAME = "@name@";

	/**
	 * User code tag.
	 *
	 * This represents the user code tag provided in options parameters.
	 */
	const kOPTION_USER_CODE = "@user@";

	/**
	 * User password tag.
	 *
	 * This represents the user password tag provided in options parameters.
	 */
	const kOPTION_USER_PASS = "@pass@";

	/**
	 * Protocol tag.
	 *
	 * This represents the protocol tag.
	 */
	const kTAG_PROT = "PROT";

	/**
	 * Host tag.
	 *
	 * This represents the host tag.
	 */
	const kTAG_HOST = "HOST";

	/**
	 * Port tag.
	 *
	 * This represents the port tag.
	 */
	const kTAG_PORT = "PORT";

	/**
	 * User tag.
	 *
	 * This represents the user code tag.
	 */
	const kTAG_USER = "USER";

	/**
	 * Password tag.
	 *
	 * This represents the user password tag.
	 */
	const kTAG_PASS = "PASS";

	/**
	 * Path tag.
	 *
	 * This represents the path tag.
	 */
	const kTAG_PATH = "PATH";

	/**
	 * Options tag.
	 *
	 * This represents the options tag.
	 */
	const kTAG_OPTS = "OPTS";

	/**
	 * Fragment tag.
	 *
	 * This represents the fragment tag.
	 */
	const kTAG_FRAG = "FRAG";

	/**
	 * <h4>Server connection object.</h4><p />
	 *
	 * This data member holds the <i>server connection object</i>, it is the native object
	 * implementing the server connection.
	 *
	 * Before the object goes to sleep ({@link __sleep()}), this attribute will be set to
	 * <tt>TRUE</tt> if a connection was open and to <tt>NULL</tt> if not: this determines
	 * whether a connection should be restored when the object is waken {@link __wakeup()}.
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
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate class.</h4><p />
	 *
	 * The object must be instantiated from a data source name which will be passed to the
	 * {@link Url} trait. The native connection options should be passed in the
	 * {@link Query()} part of the URL.
	 *
	 * @param string			$theConnection		Data source name.
	 *
	 * @uses URL()
	 *
	 * @example
	 * <code>
	 * // Set server attributes.
	 * $dsn = new Server();
	 * $dsn->Protocol( 'html' );
	 * $dsn->Host( 'example.net' );
	 * ...
	 *
	 * // Instantiate from data source name.
	 * $dsn = new Server( 'html://user:pass@host:8080/dir/file?arg=val#frag' );
	 * $dsn = new Server( 'protocol://user:password@host1:9090,host2,host3:9191/dir/file?arg=val#frag' );
	 * </code>
	 */
	public function __construct( string $theConnection = NULL )
	{
		//
		// Call parent constructor.
		//
		parent::__construct();

		//
		// Handle connection path.
		//
		if( $theConnection !== NULL )
			$this->URL( $theConnection );

	} // Constructor.


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
	 * <tt>TRUE</tt> if the connection was open, or with <tt>NULL</tt> if not.
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
	 * method, that is, if the {@link $mConnection} attribute is <tt>TRUE</tt>.
	 *
	 * @uses Connect()
	 */
	public function __wakeup()
	{
		//
		// Open closed connection.
		//
		if( $this->mConnection !== NULL )
			$this->Connect();

	} // __wakeup.


	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * <h4>Return data source name</h4><p />
	 *
	 * In this class we consider the data source name as the server's name, when cast to a
	 * string the data source URL will be returned. In derived concrete classes you should
	 * be careful to shadow sensitive data such as user names and passwords.
	 *
	 * @return string
	 *
	 * @uses URL()
	 */
	public function __toString()
	{
		return (string)$this->URL();												// ==>

	} // __toString.



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
	 * This method can be used to create and open the server connection, if the connection
	 * is already open, the method will do nothing.
	 *
	 * The method will return the native connection object, or raise an exception if unable
	 * to open the connection.
	 *
	 * @return mixed				Native connection object.
	 *
	 * @uses URL( )
	 * @uses isConnected( )
	 * @uses connectionCreate()
	 */
	public function Connect()
	{
		//
		// Create connection if not conected.
		//
		if( ! $this->isConnected() )
			$this->mConnection =
				$this->connectionCreate();

		return $this->mConnection;													// ==>

	} // Connect.


	/*===================================================================================
	 *	Disconnect																		*
	 *==================================================================================*/

	/**
	 * <h4>Close server connection.</h4><p />
	 *
	 * This method can be used to close and destruct the server connection, if no connection
	 * was open, the method will do nothing.
	 *
	 * The method will return <tt>TRUE</tt> if it closed a connection
	 *
	 * @return boolean				<tt>TRUE</tt> was connected, <tt>FALSE</tt> wasn't.
	 *
	 * @uses connectionDrop()
	 */
	public function Disconnect()
	{
		return $this->connectionDrop();												// ==>

	} // Disconnect.


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
	 * In this abstract class we simply check whether the native connection attribute is
	 * neither <tt>NULL</tt>, nor <tt>TRUE</tt>.
	 *
	 * @return boolean				<tt>TRUE</tt> is connected.
	 * @throws \RuntimeException
	 */
	public function isConnected()
	{
		return
			! ( ($this->mConnection === NULL) || ($this->mConnection === TRUE) );	// ==>

	} // isConnected.



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
	 * This method should drop the current connection and return <tt>TRUE</tt> if the
	 * current object was connected, or <tt>FALSE</tt> if not.
	 *
	 * The method will set the {@link $mConnection} member to <tt>TRUE</tt> if the object
	 * was connected, or leave it to <tt>NULL</tt> if not.
	 *
	 * @return bool					<tt>TRUE</tt> if it was disconnected.
	 *
	 * @uses isConnected()
	 * @uses connectionDestruct()
	 */
	protected function connectionDrop()
	{
		//
		// Check if connected.
		//
		if( $this->isConnected() )
		{
			//
			// Destruct connection.
			//
			$this->connectionDestruct();

			//
			// Reset native connection attribute.
			//
			$this->mConnection = TRUE;

			return TRUE;															// ==>
		}

		return FALSE;																// ==>

	} // connectionDrop.


	/*===================================================================================
	 *	connectionCreate																*
	 *==================================================================================*/

	/**
	 * <h4>Open connection.</h4><p />
	 *
	 * This method should create the actual connection and return the native connection
	 * object; in this class the method is virtual, it is the responsibility of concrete
	 * derived classes to implement this method.
	 *
	 * This method assumes the caller has checked whether the connection was already open
	 * and if the previously opened connection was closed.
	 *
	 * This method assumes the data source name ({@link URL()} to hold the connection
	 * parameters.
	 *
	 * If the operation fails, the method should raise an exception.
	 *
	 * @return mixed				The native connection.
	 */
	abstract protected function connectionCreate();


	/*===================================================================================
	 *	connectionDestruct																*
	 *==================================================================================*/

	/**
	 * <h4>Close connection.</h4><p />
	 *
	 * This method should close the open connection, in this class the method is virtual, it
	 * is the responsibility of concrete classes to implement this method.
	 *
	 * This method assumes the caller has checked whether a connection is open, it should
	 * assume the {@link $mConnection} attribute holds a valid native connection object.
	 *
	 * If the operation fails, the method should raise an exception.
	 */
	abstract protected function connectionDestruct();




} // class Server.


?>
