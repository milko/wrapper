<?php

/**
 * MongoCollectionTest.php
 *
 * This file contains the unit tests of the {@link Milko\wrapper\MongoDB\Collection} class.
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
require_once(dirname(__DIR__) . "/TestMongoServerClass.php");
require_once(dirname(__DIR__) . "/TestMongoDatabaseClass.php");
require_once(dirname(__DIR__) . "/TestMongoCollectionClass.php");

use Milko\wrapper\MongoDB\Collection;

/**
 * Mongo Collection unit tests
 *
 * We overload the parent class by implementing the abstract protected interface.
 *
 *	@covers		Collection
 *
 *	@package	Test
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		27/10/2016
 */
class MongoCollectionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Server.
	 *
	 * This attribute stores the server instance.
	 *
	 * @var \Milko\wrapper\MongoDB\Server
	 */
	public $mServer = NULL;

	/**
	 * Database.
	 *
	 * This attribute stores the database instance.
	 *
	 * @var \Milko\wrapper\MongoDB\Database
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
			"\\MongoDB\\Model\\BSONDocument",
			$result,
			'$result instanceof "\MongoDB\Model\BSONDocument"'
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
		$this->mServer = new test_MongoServer(
			'mongodb://localhost:27017'
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




} // class MongoCollectionTest.


?>
