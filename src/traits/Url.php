<?php

/**
 * Url.trait.php
 *
 * This file contains the definition of the Url trait.
 */

/*=======================================================================================
 *																						*
 *									Url.trait.php										*
 *																						*
 *======================================================================================*/

use Milko\wrapper\Container;

/**
 * <h4>URL trait.</h4><p />
 *
 * This trait implements a data source identified by an URL and stores its elements into the
 * class attributes:
 *
 * <ul>
 *	<li><tt>{@link $mProtocol}</tt>: The protocol.
 *	<li><tt>{@link $mHost}</tt>: The host.
 *	<li><tt>{@link $mPort}</tt>: The port.
 *	<li><tt>{@link $mUserCode}</tt>: The user name.
 *	<li><tt>{@link $mUserPass}</tt>: The user password.
 *	<li><tt>{@link $mPath}</tt>: The path.
 *	<li><tt>{@link $mQuery}</tt>: The query parameters.
 *	<li><tt>{@link $mFragment}</tt>: The fragment.
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
 * When setting the individual elements with the member accessor methods, there is no value
 * checking: it is the responsibility of the caller to ensure the provided data is valid.
 *
 *	@package	Core
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/08/2016
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
trait Url
{
	/**
	 * Protocol.
	 *
	 * This attribute contains the URL scheme or data source protocol.
	 *
	 * @var string
	 */
	protected $mProtocol = NULL;

	/**
	 * Host.
	 *
	 * This attribute contains the URL host, or hosts list.
	 *
	 * @var string|array
	 */
	protected $mHost = NULL;

	/**
	 * Port.
	 *
	 * This attribute contains the URL port.
	 *
	 * @var int|array
	 */
	protected $mPort = NULL;

	/**
	 * User.
	 *
	 * This attribute contains the URL user code.
	 *
	 * @var string
	 */
	protected $mUserCode = NULL;

	/**
	 * Password.
	 *
	 * This attribute contains the URL user password.
	 *
	 * @var string
	 */
	protected $mUserPass = NULL;

	/**
	 * Path.
	 *
	 * This attribute contains the URL path.
	 *
	 * @var string
	 */
	protected $mPath = NULL;

	/**
	 * Query.
	 *
	 * This attribute contains the URL query parameters, it is an array featuring the key
	 * as the array key and the value as the array value.
	 *
	 * @var array
	 */
	protected $mQuery = NULL;

	/**
	 * Fragment.
	 *
	 * This attribute contains the parameter provided after the hashmark <tt>#</tt>.
	 *
	 * @var string
	 */
	protected $mFragment = NULL;




/*=======================================================================================
 *																						*
 *							PUBLIC ATTRiBUTE ACCESSOR INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Protocol																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage protocol.</h4><p />
	 *
	 * We implement this method by using {@link manageAttribute()} with the
	 * {@link $mProtocol} attribute.
	 *
	 * We prevent resetting the protocol, since it is required in a well formed URL.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string			The data source protocol.
	 * @throws BadMethodCallException
	 *
	 * @uses Container::manageAttribute()
	 *
	 * @example
	 * <code>
	 * // Set protocol to html.
	 * $dsn->Protocol( 'html' );
	 *
	 * // Retrieve current protocol.
	 * $test = $dsn->Protocol();
	 * // 'html'
	 *
	 * // Raises an exception!
	 * $dsn->Protocol( FALSE );
	 * </code>
	 */
	public function Protocol( $theValue = NULL )
	{
		//
		// Prevent resetting the attribute.
		//
		if( $theValue === FALSE )
			throw new BadMethodCallException(
				"The data source protocol is required."
			);																	// !@! ==>

		return Container::manageAttribute( $this->mProtocol, $theValue );			// ==>

	} // Protocol.


	/*===================================================================================
	 *	Host																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage host.</h4><p />
	 *
	 * We implement this method by using {@link manageAttribute()} with the {@link $mHost}
	 * attribute.
	 *
	 * We prevent resetting the host, since it is required in a well formed URL.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string|array		The data source host(s).
	 * @throws BadMethodCallException
	 *
	 * @uses Container::manageAttribute()
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
		//
		// Prevent resetting the attribute.
		//
		if( $theValue === FALSE )
			throw new BadMethodCallException(
				"The data source host is required."
			);																	// !@! ==>

		return Container::manageAttribute( $this->mHost, $theValue );				// ==>

	} // Host.


	/*===================================================================================
	 *	Port																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage port.</h4><p />
	 *
	 * We implement this method by using {@link manageAttribute()} with the {@link $mPort}
	 * attribute.
	 *
	 * If you provide a value that cannot be converted to an integer, the method will raise
	 * an exception.
	 *
	 * If you provided a list of hosts, you must provide a list of ports of the same count
	 * as the list of hosts. The ports must all either be integers, or <tt>NULL</tt>: you
	 * cannot mix default with specific ports.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return int|array		Data source port.
	 * @throws InvalidArgumentException
	 *
	 * @uses Container::manageAttribute()
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
	 * $test = $dsn->Port( [ 8080, "baba", "", 8080 ] );
	 * </code>
	 */
	public function Port( $theValue = NULL )
	{
		//
		// Check port value.
		//
		if( ($theValue !== NULL)
		 && ($theValue !== FALSE) )
		{
			//
			// Handle scalar value.
			//
			if( ! is_array( $theValue ) )
			{
				//
				// Assert numeric.
				//
				if( (! strlen( $theValue ))
				 || (! is_numeric( $theValue )) )
					throw new \InvalidArgumentException (
						"The port must be a numeric value."
					);															// !@! ==>

				//
				// Cast port.
				//
				$theValue = (int)$theValue;

			} // Scalar port.

			//
			// Handle list of values.
			//
			else
			{
				//
				// Iterate values.
				//
				foreach( $theValue as $key => $value )
				{
					//
					// Assert numeric.
					//
					if( (! strlen( $value ))
					 || (! is_numeric( $value )) )
						throw new \InvalidArgumentException (
							"The port must be a numeric value."
						);														// !@! ==>

					//
					// Cast port.
					//
					$theValue[ $key ] = (int)$value;

				} // Iterating ports.

			} // List of ports.

		} // New value.

		return Container::manageAttribute( $this->mPort, $theValue );				// ==>

	} // Port.


	/*===================================================================================
	 *	User																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage user name.</h4><p />
	 *
	 * We implement this method by using {@link manageAttribute()} with the
	 * {@link $mUserCode} attribute.
	 *
	 * If you reset the attribute, the user password will also be reset.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string			Data source credentials user.
	 *
	 * @uses Container::manageAttribute()
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
		//
		// Handle reset.
		//
		if( $theValue === FALSE )
			$this->Password( FALSE );

		return Container::manageAttribute( $this->mUserCode, $theValue );			// ==>

	} // User.


	/*===================================================================================
	 *	Password																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage user password.</h4><p />
	 *
	 * We implement this method by using {@link manageAttribute()} with the
	 * {@link $mUserPass} attribute.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string			Data source credentials password.
	 *
	 * @uses Container::manageAttribute()
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
		return Container::manageAttribute( $this->mUserPass, $theValue );			// ==>

	} // Password.


	/*===================================================================================
	 *	Path																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage path.</h4><p />
	 *
	 * We implement this method by using {@link manageAttribute()} with the {@link $mPath}
	 * attribute.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string			Data source path.
	 *
	 * @uses Container::manageAttribute()
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
		return Container::manageAttribute( $this->mPath, $theValue );				// ==>

	} // Path.


	/*===================================================================================
	 *	Query																			*
	 *==================================================================================*/

	/**
	 * <h4>Manage query.</h4><p />
	 *
	 * We implement this method by using {@link manageAttribute()} with the {@link $mQuery}
	 * attribute.
	 *
	 * New values can be provided as arrays in which the key represents the query key and
	 * the value the query value, or as a string.
	 *
	 * If the query is invalid, the method will raise an exception.
	 *
	 * The method will always return an array.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return array			Data source query or options.
	 * @throws InvalidArgumentException
	 *
	 * @uses Container::manageAttribute()
	 *
	 * @example
	 * <code>
	 * // Set query by array.
	 * $test = $dsn->Query( [ 'arg' => 'val' ] );
	 *
	 * // Set query by string.
	 * $test = $dsn->Query( 'arg=val' );
	 *
	 * // Invalid query.
	 * $test = $dsn->Query( '=value' );
	 * // Will raise an exception.
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
						throw new InvalidArgumentException(
							"Invalid data source query." );						// !@! ==>

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

		return Container::manageAttribute( $this->mQuery, $theValue );				// ==>

	} // Query.


	/*===================================================================================
	 *	Fragment																		*
	 *==================================================================================*/

	/**
	 * <h4>Manage fragment.</h4><p />
	 *
	 * We implement this method by using {@link manageAttribute()} with the
	 * {@link $mFragment} attribute.
	 *
	 * @param mixed				$theValue			Value or operation.
	 * @return string			Data source fragment.
	 *
	 * @uses Container::manageAttribute()
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
		return Container::manageAttribute( $this->mFragment, $theValue );			// ==>

	} // Fragment.



/*=======================================================================================
 *																						*
 *							PUBLIC DATA SOURCE INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	URL																				*
	 *==================================================================================*/

	/**
	 * <h4>Set or return the URL.</h4><p />
	 *
	 * This method can be used to set the object attributes from an URL, or retrieve the
	 * URL from the object attributes.
	 *
	 * The method accepts a single parameter that represents the URL to load or the
	 * operation: if you provide <tt>NULL</tt> it is assumed you want to retrieve the URL
	 * based on the current object attributes; if you provide a string, it is assumed you
	 * want to load the object attributes by parsing the provided URL.
	 *
	 * The method will return the URL string.
	 *
	 * @param string			$theUrl				URL to load or <tt>NULL</tt>.
	 * @return string			The URL.
	 * @throws InvalidArgumentException
	 *
	 * @uses Container::manageAttribute()
	 *
	 * @example
	 * <code>
	 * // Load URL.
	 * $dsn = $test->URL( 'protocol://user:pass@host:9090/dir/file?arg=val#frag' );
	 * $host = $test->Host();
	 * ...
	 *
	 * // Retrieve URL.
	 * $test->Host( 'newHost' );
	 * $dsn = $test->URL();
	 * // 'protocol://user:pass@newHost:9090/dir/file?arg=val#frag'
	 * </code>
	 */
	public function URL( string $theUrl = NULL )
	{
		//
		// Load URL.
		//
		if( $theUrl !== NULL )
		{
			//
			// Handle connection string.
			//
			if( strlen( $theUrl = trim( $theUrl ) ) )
			{
				//
				// Check URL.
				//
				if( parse_url( $theUrl ) === FALSE )
					throw new InvalidArgumentException(
						"Invalid URL string."
					);															// !@! ==>

				//
				// Reset components.
				//
				$this->mProtocol = NULL;
				$this->mHost = NULL;
				$this->mPort = NULL;
				$this->mUserCode = NULL;
				$this->mUserPass = NULL;
				$this->mPath = NULL;
				$this->mQuery = NULL;
				$this->mFragment = NULL;

				//
				// Load components.
				//
				$this->Protocol( parse_url( $theUrl, PHP_URL_SCHEME ) );
				$this->Host( parse_url( $theUrl, PHP_URL_HOST ) );
				$this->Port( parse_url( $theUrl, PHP_URL_PORT ) );
				$this->User( parse_url( $theUrl, PHP_URL_USER ) );
				$this->Password( parse_url( $theUrl, PHP_URL_PASS ) );
				$this->Path( parse_url( $theUrl, PHP_URL_PATH ) );
				$this->Query( parse_url( $theUrl, PHP_URL_QUERY ) );
				$this->Fragment( parse_url( $theUrl, PHP_URL_FRAGMENT ) );

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
				throw new InvalidArgumentException(
					"Empty URL string."
				);																// !@! ==>

		} // Load URL.

		//
		// Init local storage.
		//
		$dsn = '';

		//
		// Set protocol.
		//
		if( $this->mProtocol !== NULL )
			$dsn .= ($this->mProtocol.'://');

		//
		// Handle credentials.
		//
		if( $this->mUserCode !== NULL )
		{
			//
			// Set user.
			//
			$dsn .= $this->mUserCode;

			//
			// Set password.
			//
			if( $this->mUserPass !== NULL )
				$dsn .= ":$this->mUserPass";

			//
			// Close credentials.
			//
			$dsn .= '@';

		} // Has user.

		//
		// Add host and port.
		//
		if( $this->mHost !== NULL )
		{
			//
			// Add hosts.
			//
			if( is_array( $this->mHost ) )
			{
				//
				// Add hosts and ports.
				//
				$list = [];
				foreach( $this->mHost as $key => $value )
					$list[] = ( $this->mPort[ $key ] !== NULL )
							? ($this->mHost[ $key ] . ':' . $this->mPort[ $key ])
							: $this->mHost[ $key ];

				$dsn .= implode( ',', $list );
			}
			else
			{
				//
				// Add host.
				//
				$dsn .= $this->mHost;

				//
				// Add port.
				//
				if( $this->mPort !== NULL )
					$dsn .= ":$this->mPort";
			}
		}

		//
		// Handle path.
		// We add a leading slash
		// if the parameter does not start with one.
		//
		if( $this->mPath !== NULL )
		{
			if( ! (substr( $this->mPath, 0, 1 ) == '/') )
				$dsn .= '/';
			$dsn .= $this->mPath;
		}

		//
		// Set options.
		//
		if( $this->mQuery !== NULL )
		{
			//
			// Format query.
			//
			$query = [];
			foreach( $this->mQuery as $key => $value )
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
		if( $this->mFragment !== NULL )
			$dsn .= "#$this->mFragment";

		return $dsn;																// ==>

	} // URL.



} // trait Url.


?>
