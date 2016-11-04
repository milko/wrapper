<?php

/**
 * ArangoCollectionTest.php
 *
 * This file contains the unit tests of the {@link Milko\wrapper\ArangoDB\Collection} class.
 *
 *	@package	Test
 *
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		27/10/2016
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

use Milko\wrapper\ArangoDB\Collection;

/**
 * Arango Collection unit tests
 *
 * We overload the parent class by implementing the abstract protected interface.
 *
 *	@covers		Collection
 *
 *	@package	Test
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		04/11/2016
 */
class ArangoCollectionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Server.
	 *
	 * This attribute stores the server instance.
	 *
	 * @var \Milko\wrapper\ArangoDB\Server
	 */
	public $mServer = NULL;

	/**
	 * Database.
	 *
	 * This attribute stores the database instance.
	 *
	 * @var \Milko\wrapper\ArangoDB\Database
	 */
	public $mDatabase = NULL;

	/**
	 * Collection.
	 *
	 * This attribute stores the collection instance.
	 *
	 * @var Collection
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

		$result = $this->mObject->Disconnect();
		$this->assertTrue( $result, "Disconnect() == TRUE" );

	} // testConnection.


	/*===================================================================================
	 *	testRoot    																	*
	 *==================================================================================*/

	/**
	 * Test Root()
	 *
	 * @covers       Collection::Root()
	 */
	public function testRoot()
	{
		/**
		 * Get root object.
		 */
		$root = $this->mObject->Root();

		/**
		 * Check root.
		 */
		$this->assertSame(
			$root,
			$this->mServer,
			'$root === $this->mServer'
		);

	} // testRoot.


	/*===================================================================================
	 *	testOperations 																	*
	 *==================================================================================*/

	/**
	 * Test read/write operations
	 *
	 * @covers       Collection::AddOne()
	 * @covers       Collection::DocumentKey()
	 * @covers       Collection::GetOne()
	 * @covers       Collection::Records()
	 * @covers       Collection::Drop()
	 */
	public function testOperations()
	{
		/*
		 * Test document.
		 */
		$document = [
			Collection::DocumentKey() => "KEY",
			"name" => "Milko",
			"surname" => "Skofic" ];

		//
		// Test to native document.
		//
		$result = Collection::ToNativeDocument( [ "A" => "B" ] );
		$this->assertInstanceOf(
			"triagens\\ArangoDb\\Document",
			$result,
			'Collection::ToNativeDocument( [ "A" => "B" ] ) instanceof "triagens\\ArangoDb\\Document"'
		);

		//
		// Test to container.
		//
		$result = Collection::ToContainer( $result );
		$this->assertInstanceOf(
			"\\Milko\\wrapper\\Container",
			$result,
			'Collection::ToContainer( $result ) instanceof "\\Milko\\wrapper\\Container"'
		);
		$result = Collection::ToContainer( NULL );
		$this->assertNull(
			$result,
			'Collection::ToContainer( NULL )'
		);

		/*
		 * Drop collection
		 */
		$this->mObject->Drop();

		/*
		 * Check no records.
		 */
		$this->assertSame(
			$this->mObject->Records(),
			0,
			'$this->mObject->Records() === 0'
		);

		/*
		 * Add record
		 */
		$id = $this->mObject->AddOne( $document );

		/*
		 * Check if worked.
		 */
		$this->assertNotNull(
			$id,
			'$this->mObject->AddOne( $document ) !== NULL'
		);
		$this->assertSame(
			$id,
			"KEY",
			'$id === "KEY"'
		);

		/*
		 * Check one record.
		 */
		$this->assertSame(
			$this->mObject->Records(),
			1,
			'$this->mObject->Records() === 1'
		);

		/*
		 * Get one record.
		 */
		$result = $this->mObject->GetOne( $id );

		/*
		 * Check if worked.
		 */
		$this->assertNotNull(
			$result,
			'$result !== NULL'
		);
		$this->assertInstanceOf(
			"triagens\\ArangoDb\\Document",
			$result,
			'$result instanceof "triagens\\ArangoDb\\Document"'
		);

		/*
		 * Convert result to array.
		 */
		$result = Collection::ToContainer( $result );

		/*
		 * Other tests.
		 */
		$this->assertNotNull(
			$result[ Collection::DocumentRevision() ],
			'$result[ Collection::DocumentRevision() ] !== NULL'
		);
		$this->assertSame(
			$result[ Collection::DocumentKey() ],
			$document[ Collection::DocumentKey() ],
			'$result[ Collection::DocumentKey() ] === $document[ Collection::DocumentKey() ]'
		);
		$this->assertSame(
			$result[ "name" ],
			$document[ "name" ],
			'$result[ "name" ] === $document[ "name" ]'
		);
		$this->assertSame(
			$result[ "surname" ],
			$document[ "surname" ],
			'$result[ "surname" ] === $document[ "surname" ]'
		);

		/*
		 * Drop collection
		 */
		$this->mObject->Drop();

		/*
		 * Check no records.
		 */
		$this->assertSame(
			$this->mObject->Records(),
			0,
			'$this->mObject->Records() === 0'
		);

	} // testOperations.



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
		// Instantiate server.
		//
		$this->mServer = new test_ArangoServer(
			'tcp://UnitTests:testuser@localhost:8529?createCollection=1'
		);

		//
		// Instantiate database.
		//
		$this->mDatabase = $this->mServer->Client( "UnitTests", [] );

		//
		// Instantiate collection.
		//
		$this->mObject = $this->mDatabase->Client( "test_collection", [] );

	} // testConstructor.




} // class ArangoCollectionTest.


?>
