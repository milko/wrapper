<?php

/**
 * Url.php
 *
 * This file contains the definition of the {@link milko\wrapper\Url} class.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *										Url.php											*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Container;

/**
 * <h4>URL object.</h4><p />
 *
 * This class implements an URL and stores its elements into the class properties:
 *
 * <ul>
 *	<li><tt>{@link PROT}</tt>: The protocol.
 *	<li><tt>{@link HOST}</tt>: The host.
 *	<li><tt>{@link PORT}</tt>: The port.
 *	<li><tt>{@link USER}</tt>: The user name.
 *	<li><tt>{@link PASS}</tt>: The user password.
 *	<li><tt>{@link PATH}</tt>: The path.
 *	<li><tt>{@link QUERY}</tt>: The query parameters.
 *	<li><tt>{@link FRAG}</tt>: The fragment.
 * </ul>
 *
 * There is also a set of member accessor methods that can be used to manage the object
 * properties:
 *
 * <ul>
 *	<li><tt>{@link Protocol()}</tt>: The protocol.
 *	<li><tt>{@link Host()}</tt>: The host.
 *	<li><tt>{@link Port()}</tt>: The port.
 *	<li><tt>{@link User()}</tt>: The user name.
 *	<li><tt>{@link Password()}</tt>: The user password.
 *	<li><tt>{@link Path()}</tt>: The path.
 *	<li><tt>{@link Query()}</tt>: The query parameters.
 *	<li><tt>{@link Fragment}</tt>: The fragment.
 *	<li><tt>{@link URL()}</tt>: The URL.
 * </ul>
 *
 * Casting the object to a string will return the URL.
 *
 * When setting the individual elements with the member accessor methods, there is no value
 * checking: it is the responsibility of the caller to ensure the provided data is valid.
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		05/02/2016
 *
 *	@example
 * <code>
 * // Instantiate empty Object.
 * $dsn = new Url();
 *
 * // Set protocol.
 * $dsn->Protocol( "MySQL" );
 * $dsn[ Url::PROT ] = "MySQL";
 *
 * // Set host.
 * $dsn->Host( "localhost" );
 * $dsn[ Url::HOST ] = "localhost";
 *
 * // Set port.
 * $dsn->Port( 3306 );
 * $dsn[ Url::PORT ] = 3306;
 *
 * // Set database and table.
 * $dsn->Path( "Database/Table" );
 * $dsn[ Url::PATH ] = "Database/Table";
 *
 * // Set user.
 * $dsn->User( "user" );
 * $dsn[ Url::USER ] = "user";
 *
 * // Set password.
 * $dsn->Password( "password" );
 * $dsn[ Url::PASS ] = "password";
 *
 * // Milko\wrapper\Url Object
 * // (
 * //     [mProperties:protected] => Array
 * //         (
 * //             [prot] => MySQL
 * //             [host] => localhost
 * //             [port] => 3306
 * //             [path] => Database/Table
 * //             [user] => user
 * //             [pass] => password
 * //         )
 * // )
 *
 * $url = $dsn->URL();
 * $url = (string)$dsn;
 * // string(51) "MySQL://user:password@localhost:3306/Database/Table"
 *
 * // Instantiate from URL.
 * $dsn = new Url( 'protocol://user:pass@host:9090/dir/file?arg=val#frag' );
 * // Milko\wrapper\Datasource Object
 * // (
 * //     [mProperties:protected] => Array
 * //         (
 * //             [prot] => protocol
 * //             [host] => host
 * //             [port] => 9090
 * //             [user] => user
 * //             [pass] => pass
 * //             [path] => /dir/file
 * //             [quer] => Array
 * //                 (
 * //                     [arg] => val
 * //                 )
 * //             [frag] => frag
 * //         )
 * // )
 *
 * // Get protocol.
 * $result = $dsn->Protocol();
 * $result = $dsn[ Url::PROT ];
 * // string(8) "protocol"
 *
 * // Get host.
 * $result = $dsn->Host();
 * $result = $dsn[ Url::HOST ];
 * // string(4) "host"
 *
 * // Get port.
 * $result = $dsn->Port();
 * $result = $dsn[ Url::PORT ];
 * // int(9090)
 *
 * // Get user.
 * $result = $dsn->User();
 * $result = $dsn[ Url::USER ];
 * // string(4) "user"
 *
 * // Get password.
 * $result = $dsn->Password();
 * $result = $dsn[ Url::PASS ];
 * // string(4) "pass"
 *
 * // Get path.
 * $result = $dsn->Path();
 * $result = $dsn[ Url::PATH ];
 * // string(9) "/dir/file"
 *
 * // Get fragment.
 * $result = $dsn->Password();
 * $result = $dsn[ Url::FRAG ];
 * // string(4) "frag"
 *
 * // Get query.
 * $result = $dsn->Query();
 * $result = $dsn[ Url::QUERY ];
 * // Array
 * // (
 * //	[arg] => val
 * // )
 *
 * // Get URL.
 * $result = $dsn->URL();
 * $result = (string)$dsn;
 * // string(52) "protocol://user:pass@host:9090/dir/file?arg=val#frag"
 *
 * // Change protocol.
 * $dsn->Protocol( "MySQL" );
 * $dsn[ Url::PROT ] = "MySQL";
 *
 * // Remove user.
 * $dsn->User( FALSE );
 * $dsn[ Url::USER ] = FALSE;
 *
 * // Remove port.
 * $dsn->Port( FALSE );
 * $dsn[ Url::PORT ] = NULL;
 *
 * // Get URL.
 * $result = $dsn->URL();
 * // string(35) "MySQL://@host/dir/file?arg=val#frag"
 *
 * $dsn = new Url( 'protocol://user:pass@host1:9090,host2:8080,host3:8181/dir/file?arg=val#frag' );
 * // Milko\wrapper\Datasource Object
 * // (
 * //     [mProperties:protected] => Array
 * //         (
 * //             [prot] => protocol
 * //             [host] => Array
 * //                 (
 * //                     [0] => host1
 * //                     [1] => host2
 * //                     [2] => host3
 * //                 )
 * //             [port] => Array
 * //                 (
 * //                     [0] => 9090
 * //                     [1] => 8080
 * //                     [2] => 8181
 * //                 )
 * //             [user] => user
 * //             [pass] => pass
 * //             [path] => /dir/file
 * //             [quer] => Array
 * //                 (
 * //                     [arg] => val
 * //                 )
 * //             [frag] => frag
 * //         )
 * // )
 *
 * // Get host.
 * $result = $dsn->Host();
 * $result = $dsn[ Url::HOST ];
 * // Array
 * // (
 * //	[0] => host1
 * //	[1] => host2
 * //	[2] => host3
 * // )
 *
 * // Get port.
 * $result = $dsn->Port();
 * $result = $dsn[ Url::PORT ];
 * // Array
 * // (
 * //	[0] => 9090
 * //	[1] => NULL
 * //	[2] => 8080
 * // )
 * </code>
 */
class Url extends Container
{
	/**
	 * Protocol.
	 *
	 * This offset constant refers to the data source scheme or protocol.
	 *
	 * @var string
	 */
	const PROT = 'prot';

	/**
	 * Host.
	 *
	 * This offset constant refers to the data source host name.
	 *
	 * @var string
	 */
	const HOST = 'host';

	/**
	 * Port.
	 *
	 * This offset constant refers to the data source port.
	 *
	 * @var string
	 */
	const PORT = 'port';

	/**
	 * User.
	 *
	 * This offset constant refers to the data source user name.
	 *
	 * @var string
	 */
	const USER = 'user';

	/**
	 * Password.
	 *
	 * This offset constant refers to the data source user password.
	 *
	 * @var string
	 */
	const PASS = 'pass';

	/**
	 * Path.
	 *
	 * This offset constant refers to the data source path.
	 *
	 * @var string
	 */
	const PATH = 'path';

	/**
	 * Query.
	 *
	 * This offset constant refers to the data source query parameters.
	 *
	 * @var string
	 */
	const QUERY = 'quer';

	/**
	 * Fragment.
	 *
	 * This offset constant refers to the parameter provided after the hashmark <tt>#</tt>..
	 *
	 * @var string
	 */
	const FRAG = 'frag';



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
	 * You can instantiate an empty object, or instantiate an object from an URL, if the
	 * provided connection string is invalid, or if either the protocol or the host are
	 * missing, the method will raise an exception.
	 *
	 * The constructor relies on the <tt>parse_url</tt> function to determine whether the
	 * provided connection string is valid. There is a special behaviour you should be aware
	 * of: when providing multiple hosts, either provide the port to all or to none, it will
	 * fail if the last host does not have a port, but a previous does; if a middle host
	 * does not have the port, it strangely works.
	 *
	 * @param string			$theConnection		Data source name or properties.
	 * @throws \InvalidArgumentException
	 *
	 * @uses Protocol()
	 * @uses Host()
	 * @uses Port()
	 * @uses User()
	 * @uses Password()
	 * @uses Query()
	 * @uses Fragment()
	 *
	 * @example
	 * <code>
	 * // Instantiate empty URL.
	 * $dsn = new Url();
	 * $dsn[ Url::PROT ] = "protocol";
	 * // ...
	 *
	 * $dsn = new Url( 'html://user:pass@host:8080/dir/file?arg=val#frag' );
	 * // Milko\wrapper\Url Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [prot] => protocol
	 * //             [host] => host
	 * //             [port] => 9090
	 * //             [user] => user
	 * //             [pass] => pass
	 * //             [path] => /dir/file
	 * //             [quer] => Array
	 * //                 (
	 * //                     [arg] => val
	 * //                 )
	 * //             [frag] => frag
	 * //         )
	 * // )
	 *
	 * $dsn = new Url( 'protocol://user:password@host1:9090,host2:8080,host3:9191/dir/file?arg=val#frag' );
	 * // Milko\wrapper\Url Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [prot] => protocol
	 * //             [host] => Array
	 * //                 (
	 * //                     [0] => host1
	 * //                     [1] => host2
	 * //                     [2] => host3
	 * //                 )
	 * //             [port] => Array
	 * //                 (
	 * //                     [0] => 9090
	 * //                     [1] => 8080
	 * //                     [2] => 8080
	 * //                 )
	 * //             [user] => user
	 * //             [pass] => pass
	 * //             [path] => /dir/file
	 * //             [quer] => Array
	 * //                 (
	 * //                     [arg] => val
	 * //                 )
	 * //             [frag] => frag
	 * //         )
	 * // )
	 * </code>
	 */
	public function __construct( string $theConnection = NULL )
	{
		//
		// Handle parameter.
		//
		if( $theConnection !== NULL )
		{
			//
			// Handle connection string.
			//
			if( strlen( $theConnection = trim( $theConnection ) ) )
			{
				//
				// Check URL.
				//
				if( parse_url( $theConnection ) === FALSE )
					throw new \InvalidArgumentException(
						"Unable to instantiate data source: " .
						"invalid data source string."
					);															// !@! ==>

				//
				// Call parent constructor.
				//
				parent::__construct();

				//
				// Load components.
				//
				$this->Protocol( parse_url( $theConnection, PHP_URL_SCHEME ) );
				$this->Host( parse_url( $theConnection, PHP_URL_HOST ) );
				$this->Port( parse_url( $theConnection, PHP_URL_PORT ) );
				$this->User( parse_url( $theConnection, PHP_URL_USER ) );
				$this->Password( parse_url( $theConnection, PHP_URL_PASS ) );
				$this->Path( parse_url( $theConnection, PHP_URL_PATH ) );
				$this->Query( parse_url( $theConnection, PHP_URL_QUERY ) );
				$this->Fragment( parse_url( $theConnection, PHP_URL_FRAGMENT ) );

				//
				// Handle multiple hosts.
				//
				if( strpos( ($list = $this->Host()), ',' ) !== FALSE )
				{
					//
					// Split hosts.
					//
					$hosts = $ports = [];
					$parts = explode( ',', $list );
					foreach( $parts as $part )
					{
						$sub = explode( ':', $part );
						$hosts[] = trim( $sub[ 0 ] );
						$ports[] = ( count( $sub ) > 1 )
							? (int)$sub[ 1 ]
							: NULL;
					}

					//
					// Split last port.
					//
					if( ($tmp = $this->Port()) !== NULL )
						$ports[ count( $ports ) - 1 ] = (int)$tmp;

					//
					// Set hosts and ports.
					//
					$this->Host( $hosts );
					$this->Port( $ports );

				} // Has multiple hosts.

			} // Data source name is not empty.

			//
			// Handle empty connection string.
			//
			else
				throw new \InvalidArgumentException(
					"Unable to instantiate data source: " .
					"empty connection string."
				);																// !@! ==>

		} // Provided parameter.

	} // Constructor.


	/*===================================================================================
	 *	__toString																		*
	 *==================================================================================*/

	/**
	 * <h4>Return Url string</h4><p />
	 *
	 * In this class we consider the URL as the string representation og the object; in
	 * derived classes you should be careful to shadow sensitive data.
	 *
	 * <em>Note that this method cannot return the <tt>NULL</tt> value, which means that it
	 * cannot be used until the required object properties have been set.</em>
	 *
	 * @return string
	 *
	 * @uses URL()
	 */
	public function __toString()
	{
		return $this->URL();														// ==>

	} // __toString.



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
	 * We overload this method to check the port value: it can either be an integer or an
	 * array of integers, if that is not the case, an exception will be raised.
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value to set at offset.
	 * @throws \InvalidArgumentException
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Handle port.
		//
		if( $theOffset == self::PORT )
		{
			//
			// Check value.
			//
			if( $theValue !== NULL )
			{
				//
				// Assert numeric or array.
				//
				if( (! is_numeric( $theValue ))
				 && (! is_array( $theValue )) )
					throw new \InvalidArgumentException (
						"The port must be a numeric value."
					);															// !@! ==>

				//
				// Check array elements.
				//
				if( is_array( $theValue ) )
				{
					//
					// Iterate ports.
					//
					foreach( $theValue as $key => $value )
					{
						//
						// Cast empty string to NULL.
						//
						if( ! strlen( $value ) )
							$theValue[ $key ] = $value = NULL;

						//
						// Check value.
						//
						elseif( ($value !== NULL)
						 	 && (! is_numeric( $value )) )
							throw new \InvalidArgumentException (
								"The port must be a numeric value."
							);													// !@! ==>

						//
						// Cast array element.
						//
						$theValue[ $key ] = (int)$value;

					} // Iterating ports.

				} // Provided list of ports.

				//
				// Cast scalar value.
				//
				else
					$theValue = (int)$theValue;

			} // Not delete operation.

		} // Setting port.

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
	 * We overload this method to prevent deleting the host and protocol.
	 * We also delete the password when deleting the user.
	 *
	 * @param string				$theOffset			Offset.
	 * @throws \BadMethodCallException
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Prevent deleting protocol and host.
		//
		if( ($theOffset == self::HOST)
		 || ($theOffset == self::PROT) )
			throw new \BadMethodCallException(
				"The data source protocol and host are required."
			);																	// !@! ==>

		//
		// Delete password along with user.
		//
		if( $theOffset == self::USER )
			$this->offsetUnset( self::PASS );

		//
		// Call parent method.
		//
		parent::offsetUnset( $theOffset );

	} // offsetUnset.



/*=======================================================================================
 *																						*
 *							PUBLIC MEMBER ACCESSOR INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Protocol																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage protocol.</h4><p />
	 *
	 * We implement this method by using {@link manageProperty()} with the {@link PROT}
	 * offset.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string
	 *
	 * @uses manageProperty()
	 *
	 * @example
	 * <code>
	 * // Set protocol to html.
	 * $dsn->Protocol( 'html' );
	 *
	 * // Retrieve current protocol.
	 * $test = $dsn->Protocol();
	 *
	 * // Raises an exception!
	 * $dsn->Protocol( FALSE );
	 * </code>
	 */
	public function Protocol( $theValue = NULL )
	{
		return $this->manageProperty( self::PROT, $theValue );						// ==>

	} // Protocol.


	/*===================================================================================
	 *	Host																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage host.</h4><p />
	 *
	 * We implement this method by using {@link manageProperty()} with the {@link HOST}
	 * offset.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string|array
	 *
	 * @uses manageProperty()
	 *
	 * @example
	 * <code>
	 * // Set host.
	 * $test = $dsn->Host( 'example.net' );
	 *
	 * // Set hosts.
	 * $test = $dsn->Host( [ 'host1', 'host2', 'host3' ] );
	 *
	 * // Retrieve current host.
	 * $test = $dsn->Host();
	 *
	 * // Raises an exception!
	 * $test = $dsn->Host( FALSE );
	 * </code>
	 */
	public function Host( $theValue = NULL )
	{
		return $this->manageProperty( self::HOST, $theValue );						// ==>

	} // Host.


	/*===================================================================================
	 *	Port																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage port.</h4><p />
	 *
	 * We implement this method by using {@link manageProperty()} with the {@link PORT}
	 * offset.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return int
	 *
	 * @uses manageProperty()
	 *
	 * @example
	 * <code>
	 * // Set port.
	 * $test = $dsn->Port( 8080 );
	 *
	 * // Set ports.
	 * $test = $dsn->Port( [ 8080, NULL, 8080 ] );
	 *
	 * // Retrieve current port.
	 * $test = $dsn->Port();
	 *
	 * // Remove port.
	 * $test = $dsn->Port( FALSE );
	 *
	 * // Raises an exception!
	 * $test = $dsn->Port( "string" );
	 * $test = $dsn->Port( [ 8080, "baba", 8080 ] );
	 * </code>
	 */
	public function Port( $theValue = NULL )
	{
		return $this->manageProperty( self::PORT, $theValue );						// ==>

	} // Port.


	/*===================================================================================
	 *	User																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage user name.</h4><p />
	 *
	 * We implement this method by using {@link manageProperty()} with the {@link USER}
	 * offset.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string
	 *
	 * @uses manageProperty()
	 *
	 * @example
	 * <code>
	 * // Set user name.
	 * $test = $dsn->User( 'admin' );
	 *
	 * // Retrieve current user name.
	 * $test = $dsn->User();
	 *
	 * // Remove user and password.
	 * $test = $dsn->User( FALSE );
	 * </code>
	 */
	public function User( $theValue = NULL )
	{
		return $this->manageProperty( self::USER, $theValue );						// ==>

	} // User.


	/*===================================================================================
	 *	Password																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage user password.</h4><p />
	 *
	 * We implement this method by using {@link manageProperty()} with the {@link PASS}
	 * offset.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string
	 *
	 * @uses manageProperty()
	 *
	 * @example
	 * <code>
	 * // Set password.
	 * $test = $dsn->Password( "secret" );
	 *
	 * // Retrieve current password.
	 * $test = $dsn->Password();
	 *
	 * // Remove password.
	 * $test = $dsn->Password( FALSE );
	 * </code>
	 */
	public function Password( $theValue = NULL )
	{
		return $this->manageProperty( self::PASS, $theValue );						// ==>

	} // Password.


	/*===================================================================================
	 *	Path																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage path.</h4><p />
	 *
	 * We implement this method by using {@link manageProperty()} with the {@link PATH}
	 * offset.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @param boolean			$asArray			<tt>TRUE</tt> return as array.
	 * @return string
	 *
	 * @uses manageProperty()
	 *
	 * @example
	 * <code>
	 * // Set path.
	 * $test = $dsn->Path( "/dir/file" );
	 *
	 * // Retrieve current path.
	 * $test = $dsn->Path();
	 *
	 * // Remove path.
	 * $test = $dsn->Path( FALSE );
	 * </code>
	 */
	public function Path( $theValue = NULL )
	{
		return $this->manageProperty( self::PATH, $theValue );						// ==>

	} // Path.


	/*===================================================================================
	 *	Query																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage query.</h4><p />
	 *
	 * We implement this method by using {@link manageProperty()} with the {@link QUERY}
	 * offset.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return array
	 * @throws \InvalidArgumentException
	 *
	 * @uses manageProperty()
	 *
	 * @example
	 * <code>
	 * // Set query by array.
	 * $test = $dsn->Query( [ 'arg' => 'val' ] );
	 *
	 * // Set query by string.
	 * $test = $dsn->Query( 'arg=val' );
	 *
	 * // Retrieve current query.
	 * $test = $dsn->Query();
	 *
	 * // Remove query.
	 * $test = $dsn->Query( FALSE );
	 * </code>
	 */
	public function Query( $theValue = NULL )
	{
		//
		// Compile query.
		//
		if( ($theValue !== NULL)
			&& ($theValue !== FALSE)
			&& (! is_array( $theValue )) )
		{
			//
			// Split parameter groups.
			//
			$params = explode( '&', (string)$theValue );
			if( count( $params ) )
			{
				//
				// Init local storage.
				//
				$list = [];

				//
				// Iterate query elements.
				//
				foreach( $params as $param )
				{
					//
					// Parse query element.
					//
					$elements = explode( '=', $param );

					//
					// Check parameter name.
					//
					if( ! strlen( $name = trim( $elements[ 0 ] ) ) )
						throw new \InvalidArgumentException(
							"Invalid data source query." );					// !@! ==>

					//
					// Check parameter value.
					//
					$value = ( count( $elements ) > 1 )
						? $elements[ 1 ]
						: NULL;

					//
					// Set parameter.
					//
					$list[ $name ] = $value;

				} // Iterating query elements.

				//
				// Set converted value.
				//
				$theValue = ( count( $list ) )
					? $list
					: NULL;

			} // Has parameters.

		} // Provided a string.

		return $this->manageProperty( self::QUERY, $theValue );						// ==>

	} // Query.


	/*===================================================================================
	 *	Fragment																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage fragment.</h4><p />
	 *
	 * We implement this method by using {@link manageProperty()} with the {@link FRAG}
	 * offset.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string
	 *
	 * @uses manageProperty()
	 *
	 * @example
	 * <code>
	 * // Set fragment.
	 * $test = $dsn->Fragment( "frag" );
	 *
	 * // Retrieve current fragment.
	 * $test = $dsn->Fragment();
	 *
	 * // Remove fragment.
	 * $test = $dsn->Fragment( FALSE );
	 * </code>
	 */
	public function Fragment( $theValue = NULL )
	{
		return $this->manageProperty( self::FRAG, $theValue );						// ==>

	} // Fragment.



/*=======================================================================================
 *																						*
 *								PUBLIC URI UTILITIES									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	toURL																			*
	 *==================================================================================*/

	/**
	 * <h4>Return the URL.</h4><p />
	 *
	 * This method can be used to return an URL from the current object properties.
	 *
	 * The method accepts a parameter that can be used to exclude specific elements from the
	 * returned URL: provide an array with the offsets you wish to exclude.
	 *
	 * @param array				$theExcluded		List of excluded offsets.
	 * @return string
	 *
	 * @uses manageProperty()
	 *
	 * @example
	 * <code>
	 * // Instantiate from URL.
	 * $dsn = new Url( 'protocol://user:pass@host:9090/dir/file?arg=val#frag' );
	 *
	 * // Return full URL.
	 * $test = $dsn->URL();
	 * // string(52) "protocol://user:pass@host:9090/dir/file?arg=val#frag"
	 *
	 * // Return URL without path.
	 * $test = $dsn->URL( [ Url::PATH ] );
	 * // string(43) "protocol://user:pass@host:9090?arg=val#frag"
	 * </code>
	 */
	public function URL( $theExcluded = [] )
	{
		//
		// Init local storage.
		//
		$dsn = '';

		//
		// Set protocol.
		//
		if( (! in_array( self::PROT, $theExcluded ))
			&& (($tmp = $this->offsetGet( self::PROT )) !== NULL) )
			$dsn .= ($tmp.'://');

		//
		// Handle credentials.
		//
		if( (! in_array( self::USER, $theExcluded ))
			&& (($tmp = $this->offsetGet( self::USER )) !== NULL) )
		{
			//
			// Set user.
			//
			$dsn .= $tmp;

			//
			// Set password.
			//
			if( (! in_array( self::PASS, $theExcluded ))
				&& (($tmp = $this->offsetGet( self::PASS )) !== NULL) )
				$dsn .= ":$tmp";

			//
			// Close credentials.
			//
			$dsn .= '@';

		} // Has user.

		//
		// Add host and port.
		//
		if( (! in_array( self::HOST, $theExcluded ))
			&& (($tmp = $this->offsetGet( self::HOST )) !== NULL) )
		{
			//
			// Init local storage.
			//
			$do_port = ! in_array( self::PORT, $theExcluded );

			//
			// Add hosts.
			//
			if( is_array( $tmp ) )
			{
				//
				// Add hosts and ports.
				//
				$list = [];
				$ports = $this->Port();
				foreach( $tmp as $key => $value )
				{
					if( $do_port )
						$list[] = ( $ports[ $key ] !== NULL )
							? ($tmp[ $key ] . ':' . $ports[ $key ])
							: $tmp[ $key ];
					else
						$list[] = $tmp[ $key ];
				}

				$dsn .= implode( ',', $list );
			}
			else
			{
				//
				// Add host.
				//
				$dsn .= $tmp;

				//
				// Add port.
				//
				if( $do_port
					&& (($tmp = $this->offsetGet( self::PORT )) !== NULL) )
					$dsn .= ":$tmp";
			}
		}

		//
		// Handle path.
		// We add a leading slash
		// if the parameter does not start with one.
		//
		if( (! in_array( self::PATH, $theExcluded ))
			&& (($tmp = $this->offsetGet( self::PATH )) !== NULL) )
		{
			if( ! (substr( $tmp, 0, 1 ) == '/') )
				$dsn .= '/';
			$dsn .= $tmp;
		}

		//
		// Set options.
		//
		if( (! in_array( self::QUERY, $theExcluded ))
			&& (($tmp = $this->offsetGet( self::QUERY )) !== NULL) )
		{
			//
			// Format query.
			//
			$query = [];
			foreach( $tmp as $key => $value )
				$query[] = ( $value !== NULL )
					? "$key=$value"
					: $key;

			//
			// Set query.
			//
			$dsn .= ('?'.implode( '&', $query ));
		}

		//
		// Set fragment.
		//
		if( (! in_array( self::FRAG, $theExcluded ))
			&& (($tmp = $this->offsetGet( self::FRAG )) !== NULL) )
			$dsn .= "#$tmp";

		return $dsn;																// ==>

	} // URL.



} // class Url.


?>
