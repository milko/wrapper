<?php

/**
 * ArangoServerTest.php
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
require_once(dirname(__DIR__) . "/TestArangoServerClass.php");
require_once(dirname(__DIR__) . "/TestArangoDatabaseClass.php");
require_once(dirname(__DIR__) . "/TestArangoCollectionClass.php");

use Milko\wrapper\ArangoDB\Server;

/**
 * Arango Server unit tests
 *
 * We overload the parent class by implementing the abstract protected interface.
 *
 *	@covers		Server
 *
 *	@package	Test
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		18/08/2016
 */
class ArangoServerTest extends PHPUnit_Framework_TestCase
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
	 * @covers       \Milko\wrapper\ClientServer::Connect()
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
		$this->assertInstanceOf(
			\triagens\ArangoDb\Connection::class,
			$this->mObject->Connection(),
			"Connection() == \\triagens\\ArangoDb\\Connection::class"
		);
		$this->assertInstanceOf(
			\triagens\ArangoDb\Connection::class,
			$result,
			'$result == \\triagens\\ArangoDb\\Connection::class'
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
		 * Test adding database "UnitTests".
		 */
		$result = $this->mObject->Client( "UnitTests", [] );
		$this->assertTrue(
			$this->mObject->offsetExists( "UnitTests" ),
			'$this->mObject->Client( "Directory", [] ) => offsetExists( "UnitTests" )'
		);
		$this->assertSame(
			$result,
			$this->mObject->Client( "UnitTests" ),
			'$this->mObject->Client( "UnitTests", [] ) == $result'
		);
		$this->assertSame(
			$this->mObject,
			$result->Server(),
			'$result->Server() == $this->mObject'
		);
		$this->assertInstanceOf(
			triagens\ArangoDb\Connection::class,
			$this->mObject->Connection(),
			'$this->mObject->Connection() == \\triagens\\ArangoDb\\Connection::class'
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
			"UnitTests",
			$result->Path(),
			'$this->mObject->Path() == $result->Path()'
		);

		/**
		 * Test adding database "UnitTests" with options.
		 */
		$result = $this->mObject->Client(
			"UnitTests",
			[
				test_ArangoServer::kOPTION_NAME => "TheFileName",
				test_ArangoServer::kOPTION_USER_CODE => "TheUserCode",
				test_ArangoServer::kOPTION_USER_PASS => "TheUserPass"
			]
		);
		$this->assertTrue(
			$this->mObject->offsetExists( "UnitTests" ),
			'$this->mObject->Client( "UnitTests", [ ... ] ) => offsetExists( "UnitTests" )'
		);
		$this->assertSame(
			$result,
			$this->mObject->Client( "UnitTests" ),
			'$this->mObject->Client( "UnitTests", [] ) == $result'
		);
		$this->assertSame(
			$this->mObject,
			$result->Server(),
			'$result->Server() == $this->mObject'
		);
		$this->assertInstanceOf(
			triagens\ArangoDb\Connection::class,
			$this->mObject->Connection(),
			'$this->mObject->Connection() == \\triagens\\ArangoDb\\Connection::class'
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
			"UnitTests",
			[
				test_ArangoServer::kOPTION_NAME => "TheFileName",
				test_ArangoServer::kOPTION_USER_CODE => "TheUserCode",
				test_ArangoServer::kOPTION_USER_PASS => "TheUserPass"
			]
		);
		$this->assertFalse(
			$this->mObject->offsetExists( "UnitTests" ),
			'$this->mObject->offsetExists( "UnitTests" ) === FALSE'
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
		$object = new test_ArangoServer(
			'tcp://UnitTests:testuser@localhost:8529/UnitTests/Collection?createCollection=1'
		);

		/**
		 * Check client server object.
		 */
		$this->assertTrue(
			$object->offsetExists( "UnitTests" ),
			'$object->offsetExists( "UnitTests" ) === TRUE'
		);
		$this->assertTrue(
			$object->isConnected(),
			'$object->isConnected() === TRUE'
		);
		$this->assertInstanceOf(
			triagens\ArangoDb\Connection::class,
			$object->Connection(),
			"Connection() == \\triagens\\ArangoDb\\Connection::class"
		);
		$this->assertSame(
			"tcp",
			$object->Protocol(),
			'$object->Protocol() == "tcp'
		);
		$this->assertSame(
			"localhost",
			$object->Host(),
			'$object->Host() == "localhost'
		);
		$this->assertSame(
			8529,
			$object->Port(),
			'$object->Port() == 8529'
		);
		$this->assertNull(
			$object->Path(),
			'$object->Path() === NULL'
		);

		/**
		 * Get database.
		 */
		$database = $object->Client( "UnitTests" );

		/**
		 * Test first level client.
		 */
		$this->assertSame(
			$object,
			$database->Server(),
			'$object == $database->Server()'
		);
		$this->assertInstanceOf(
			\triagens\ArangoDb\Connection::class,
			$database->Connection(),
			"Connection() == \\triagens\\ArangoDb\\Connection::class"
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
			"UnitTests",
			$database->Path(),
			'"UnitTests" == $database->Path()'
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
		$nested = $object[ "UnitTests" ][ "Collection" ];
		$this->assertSame(
			$nested,
			$collection,
			'$collection == $object[ "UnitTests" ][ "Collection" ]'
		);

		/**
		 * Check collection connection.
		 */
		$this->assertFalse(
			$collection->isConnected(),
			'$collection->isConnected()'
		);
		$collection->Connect();
		$this->assertTrue(
			$collection->isConnected(),
			'$collection->isConnected()'
		);

		/**
		 * Drop database.
		 */
		$database->Drop();

		/**
		 * Connect database.
		 */
		$database->Connect();

		/**
		 * Write stuff to the collection.
		 */
		$collection->SetOne( [ "name" => "test" ] );
		$this->assertGreaterThan(
			0,
			$collection->Records(),
			'$collection->count()'
		);

		/**
		 * Check databases list.
		 */
		$this->assertContains(
			"UnitTests",
			array_keys( $object->Clients() ),
			'Database "UnitTests" exists'
		);

	} // testConstruct.


	/*===================================================================================
	 *	testClients																		*
	 *==================================================================================*/

	/**
	 * Test Clients()
	 *
	 * @covers       Server::Clients()
	 */
	public function testClients()
	{
		/**
		 * Test clients list.
		 */
		$result = $this->mObject->Clients();
		$this->assertInternalType(
			"array",
			$result,
			'$result is array'
		);

	} // testClients.



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
		$this->mObject = new test_ArangoServer(
			'tcp://UnitTests:testuser@localhost:8529?createCollection=1'
		);

	} // testConstructor.




} // class ArangoServerTest.


?>
