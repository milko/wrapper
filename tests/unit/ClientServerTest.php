<?php

/**
 * ClientServerTest.php
 *
 * This file contains the unit tests of the {@link Milko\wrapper\ClientServer} class.
 *
 *	@package	Test
 *
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		18/08/2016
 */

/**
 * Include local definitions.
 */
require_once(dirname( dirname(__DIR__) ) . "/includes.local.php");

/**
 * Include test classes.
 */
require_once(dirname(__DIR__) . "/TestClientClass.php");
require_once(dirname(__DIR__) . "/TestClientServerClass.php");

use Milko\wrapper\ClientServer;

/**
 * ClientServer unit tests
 *
 * We overload the parent class by implementing the abstract protected interface.
 *
 *	@covers		Milko\wrapper\Container
 *
 *	@package	Test
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		18/08/2016
 */
class ClientServerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Data.
	 *
	 * This attribute stores the test object instance.
	 *
	 * @var object
	 */
	public $mObject = NULL;




/*=======================================================================================
 *																						*
 *								PUBLIC TEST INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	testConnection																	*
	 *==================================================================================*/

	/**
	 * Test Connect()
	 *
	 * @covers       ClientServer::Connect()
	 * @covers       ClientServer::Disconnect()
	 * @covers       ClientServer::isConnected()
	 * @covers       ClientServer::createConnection()
	 * @covers       ClientServer::destructConnection()
	 */
	public function testConnection()
	{
		//
		// Make tests.
		//
		$this->assertFalse( $this->mObject->isConnected(), "isConnected() == FALSE" );
		$this->assertNull( $this->mObject->Connection(), "Connection() === NULL" );

		$result = $this->mObject->Connect();
		$this->assertTrue( $this->mObject->isConnected(), "isConnected() == TRUE" );
		$this->assertSame(
			"ClientServer is connected",
			$this->mObject->Connection(),
			"Connection() == 'ClientServer is connected'"
		);
		$this->assertSame(
			"ClientServer is connected",
			$result,
			"Connection() == 'ClientServer is connected'"
		);

		$result = $this->mObject->Disconnect();
		$this->assertTrue( $result, "Disconnect() == TRUE" );
		$this->assertFalse( $this->mObject->isConnected(), "isConnected() == FALSE" );
		$this->assertTrue( $this->mObject->Connection(), "Conection() === TRUE" );

	} // testConnection.


	/*===================================================================================
	 *	testClient																		*
	 *==================================================================================*/

	/**
	 * Test Client()
	 *
	 * @covers       ClientServer::Client()
	 */
	public function testClient()
	{
		/**
		 * Test adding single client "Directory".
		 */
		$result = $this->mObject->Client( "Directory", [] );
		$this->assertTrue(
			$this->mObject->offsetExists( "Directory" ),
			'$this->mObject->Client( "Directory", [] ) => offsetExists( "Directory" )'
		);
		$this->assertSame(
			$result,
			$this->mObject->Client( "Directory" ),
			'$this->mObject->Client( "Directory", [] ) == $result'
		);
		$this->assertSame(
			$this->mObject,
			$result->Server(),
			'$result->Server() == $this->mObject'
		);
		$this->assertSame(
			"ClientServer is connected",
			$this->mObject->Connection(),
			'$this->mObject->Connection() == "ClientServer is connected"'
		);
		$this->assertNull(
			$result->Connection(),
			'$result->Connection() === NULL'
		);
		$this->assertSame(
			$this->mObject->Protocol(),
			$result->Protocol(),
			'$this->mObject->Protocol() == $result->Protocol()'
		);
		$this->assertSame(
			$this->mObject->Host(),
			$result->Host(),
			'$this->mObject->Host() == $result->Host()'
		);
		$this->assertSame(
			$this->mObject->Port(),
			$result->Port(),
			'$this->mObject->Port() == $result->Port()'
		);
		$this->assertSame(
			"Directory",
			$result->Path(),
			'$this->mObject->Path() == $result->Path()'
		);

		/**
		 * Test adding single client "File" with options.
		 */
		$result = $this->mObject->Client(
			"File",
			[
				ClientServer::kOPTION_NAME => "TheFileName",
				ClientServer::kOPTION_USER_CODE => "TheUserCode",
				ClientServer::kOPTION_USER_PASS => "TheUserPass",
				"opt1" => "val1",
				"opt2" => "val2"
			]
		);
		$this->assertTrue(
			$this->mObject->offsetExists( "File" ),
			'$this->mObject->Client( "File", [ ... ] ) => offsetExists( "File" )'
		);
		$this->assertSame(
			$result,
			$this->mObject->Client( "File" ),
			'$this->mObject->Client( "File", [] ) == $result'
		);
		$this->assertSame(
			$this->mObject,
			$result->Server(),
			'$result->Server() == $this->mObject'
		);
		$this->assertSame(
			"ClientServer is connected",
			$this->mObject->Connection(),
			'$this->mObject->Connection() == "ClientServer is connected"'
		);
		$this->assertNull(
			$result->Connection(),
			'$result->Connection() === NULL'
		);
		$this->assertSame(
			$this->mObject->Protocol(),
			$result->Protocol(),
			'$this->mObject->Protocol() == $result->Protocol()'
		);
		$this->assertSame(
			$this->mObject->Host(),
			$result->Host(),
			'$this->mObject->Host() == $result->Host()'
		);
		$this->assertSame(
			$this->mObject->Port(),
			$result->Port(),
			'$this->mObject->Port() == $result->Port()'
		);
		$this->assertSame(
			"TheFileName",
			$result->Path(),
			'"TheFileName" == $result->Path()'
		);
		$this->assertSame(
			"TheUserCode",
			$result->User(),
			'"TheUserCode" == $result->User()'
		);
		$this->assertSame(
			"TheUserPass",
			$result->Password(),
			'"TheUserPass" == $result->Password()'
		);
		$this->assertSame(
			[
				"opt1" => "val1",
				"opt2" => "val2"
			],
			$result->Options(),
			'[ ... ] == $result->Options()'
		);

	} // testClient.


	/*===================================================================================
	 *	testNewClient																	*
	 *==================================================================================*/

	/**
	 * Test NewClient()
	 *
	 * @covers       ClientServer::NewClient()
	 */
	public function testNewClient()
	{
		/**
		 * Test instantiating from NewClient().
		 */
		$result = $this->mObject->NewClient(
			"Test",
			[
				ClientServer::kOPTION_NAME => "TheFileName",
				ClientServer::kOPTION_USER_CODE => "TheUserCode",
				ClientServer::kOPTION_USER_PASS => "TheUserPass",
				"opt1" => "val1",
				"opt2" => "val2"
			]
		);
		$this->assertFalse(
			$this->mObject->offsetExists( "Test" ),
			'$this->mObject->offsetExists( "Test" ) === FALSE'
		);
		$this->assertSame(
			$this->mObject,
			$result->Server(),
			'$this->mObject == $result->Server()'
		);
		$this->assertTrue(
			$this->mObject->isConnected(),
			'$this->mObject->isConnected() === TRUE'
		);
		$this->assertFalse(
			$result->isConnected(),
			'$result->isConnected() === FALSE'
		);
		$this->assertSame(
			$this->mObject->Protocol(),
			$result->Protocol(),
			'$this->mObject->Protocol() == $result->Protocol()'
		);
		$this->assertSame(
			$this->mObject->Host(),
			$result->Host(),
			'$this->mObject->Host() == $result->Host()'
		);
		$this->assertSame(
			$this->mObject->Port(),
			$result->Port(),
			'$this->mObject->Port() == $result->Port()'
		);
		$this->assertSame(
			"TheFileName",
			$result->Path(),
			'"TheFileName" == $result->Path()'
		);
		$this->assertSame(
			"TheUserCode",
			$result->User(),
			'"TheUserCode" == $result->User()'
		);
		$this->assertSame(
			"TheUserPass",
			$result->Password(),
			'"TheUserPass" == $result->Password()'
		);
		$this->assertSame(
			[
				"opt1" => "val1",
				"opt2" => "val2"
			],
			$result->Options(),
			'[ ... ] == $result->Options()'
		);

	} // testNewClient.


	/*===================================================================================
	 *	testConstruct																	*
	 *==================================================================================*/

	/**
	 * Test __construct()
	 *
	 * @covers       ClientServer::__construct()
	 */
	public function testConstruct()
	{
		/**
		 * Test nested instantiating.
		 */
		$object = new test_ClientServer(
			'protocol://user:password@host:80/Database/Collection?key=val#frag'
		);

		/**
		 * Check client server object.
		 */
		$this->assertTrue(
			$object->offsetExists( "Database" ),
			'$object->offsetExists( "Database" ) === TRUE'
		);
		$this->assertTrue(
			$object->isConnected(),
			'$object->isConnected() === TRUE'
		);
		$this->assertSame(
			"ClientServer is connected",
			$object->Connection(),
			'$object->Connection() == "ClientServer is connected"'
		);
		$this->assertSame(
			"protocol",
			$object->Protocol(),
			'$object->Protocol() == "protocol'
		);
		$this->assertSame(
			"host",
			$object->Host(),
			'$object->Host() == "host'
		);
		$this->assertSame(
			80,
			$object->Port(),
			'$object->Port() == 80'
		);
		$this->assertNull(
			$object->Path(),
			'$object->Path() === NULL'
		);
		$this->assertSame(
			"user",
			$object->User(),
			'$object->User() == "user"'
		);
		$this->assertSame(
			"password",
			$object->Password(),
			'$object->Password() == "password"'
		);
		$this->assertSame(
			[ "key" => "val" ],
			$object->Options(),
			'$object->Options() == [ "key" => "val" ]'
		);

		/**
		 * Get database.
		 */
		$database = $object->Client( "Database" );

		/**
		 * Test first level client.
		 */
		$this->assertSame(
			$object,
			$database->Server(),
			'$object == $database->Server()'
		);
		$this->assertSame(
			"Client is connected",
			$database->Connection(),
			'"Client is connected" == $database->Connection()'
		);
		$this->assertTrue(
			$database->offsetExists( "Collection" ),
			'$database->offsetExists( "Collection" ) === TRUE'
		);
		$this->assertSame(
			$object->Protocol(),
			$database->Protocol(),
			'$object->Protocol() == $database->Protocol()'
		);
		$this->assertSame(
			$object->Host(),
			$database->Host(),
			'$object->Host() == $database->Host()'
		);
		$this->assertSame(
			$object->Port(),
			$database->Port(),
			'$object->Port() == $database->Port()'
		);
		$this->assertSame(
			"Database",
			$database->Path(),
			'"Database" == $database->Path()'
		);
		$this->assertNull(
			$database->User(),
			'$database->User() === NULL'
		);
		$this->assertNull(
			$database->Password(),
			'$database->Password() === NULL'
		);
		$this->assertNull(
			$database->Options(),
			'$database->Options() === NULL'
		);

		/**
		 * Get collection.
		 */
		$collection = $database->Client( "Collection" );

		/**
		 * Test first level client.
		 */
		$this->assertSame(
			$database,
			$collection->Server(),
			'$database == $collection->Server()'
		);
		$this->assertNull(
			$collection->Connection(),
			'$collection->Connection() === NULL'
		);
		$this->assertSame(
			$database->Protocol(),
			$collection->Protocol(),
			'$database->Protocol() == $collection->Protocol()'
		);
		$this->assertSame(
			$database->Host(),
			$collection->Host(),
			'$database->Host() == $collection->Host()'
		);
		$this->assertSame(
			$database->Port(),
			$collection->Port(),
			'$database->Port() == $collection->Port()'
		);
		$this->assertSame(
			"Collection",
			$collection->Path(),
			'"Collection" == $collection->Path()'
		);
		$this->assertNull(
			$collection->User(),
			'$collection->User() === NULL'
		);
		$this->assertNull(
			$collection->Password(),
			'$collection->Password() === NULL'
		);
		$this->assertNull(
			$collection->Options(),
			'$collection->Options() === NULL'
		);

		/**
		 * Get "Collection" by offset.
		 */
		$nested = $object[ "Database" ][ "Collection" ];
		$this->assertSame(
			$nested,
			$collection,
			'$collection == $object[ "Database" ][ "Collection" ]'
		);

	} // testConstruct.



/*=======================================================================================
 *																						*
 *								PROTECTED SETUP INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	setUp																			*
	 *==================================================================================*/

	/**
	 * Set up test object
	 */
	protected function setUp()
	{
		//
		// Instantiate object.
		//
		$this->mObject = new test_ClientServer(
			'protocol://user:password@host:80?key=val#frag'
		);

	} // testConstructor.




} // class ClientServerTest.


?>
