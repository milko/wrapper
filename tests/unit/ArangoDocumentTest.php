<?php

/**
 * ArangoDocumentTest.php
 *
 * This file contains the unit tests of the {@link Milko\wrapper\Document} class using
 * ArangoDB collections.
 *
 *	@package	Test
 *
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		04/11/2016
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

use Milko\wrapper\Document;

/**
 * Arango Document unit tests
 *
 * We implement document testing.
 *
 *	@covers		Document
 *
 *	@package	Test
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		04/11/2016
 */
class ArangoDocumentTest extends PHPUnit_Framework_TestCase
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
	 * @var \Milko\wrapper\ArangoDB\Collection
	 */
	public $mCollection = NULL;




/*=======================================================================================
 *																						*
 *								PUBLIC TEST INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	testInstantiation																*
	 *==================================================================================*/

	/**
	 * Test __construct()
	 */
	public function testInstantiation()
	{
		//
		// Empty document.
		//
		$test = new Document( $this->mCollection );
		$this->assertEquals(
			[],
			$test->asArray(),
			'new Document( $this->mCollection ) ==> []'
		);
		$this->assertFalse(
			$test->isDirty(),
			'$test->isDirty() == FALSE'
		);
		$this->assertFalse(
			$test->isPersistent(),
			'$test->isPersistent() == FALSE'
		);

		//
		// Filled document.
		//
		$test = new Document( $this->mCollection, new ArrayObject( [ "uno" => 1 ] ) );
		$this->assertEquals(
			[ "uno" => 1 ],
			$test->asArray(),
			'new Document( $this->mCollection ) ==> [ "uno" => 1 ]'
		);
		$this->assertTrue(
			$test->isDirty(),
			'$test->isDirty() == TRUE'
		);
		$this->assertFalse(
			$test->isPersistent(),
			'$test->isPersistent() == FALSE'
		);

		//
		// Add a document.
		//
		$id = $this->mCollection->AddOne( [ "_key" => "pippo", "uno" => 1 ] );

		//
		// Retrieve the document.
		//
		$test = new Document( $this->mCollection, $id );
		$this->assertNotNull(
			$test,
			'$test !== NULL'
		);
		$this->assertSame(
			"pippo",
			$test[ \Milko\wrapper\ArangoDB\Collection::DocumentKey() ],
			'$test[ "pippo" ] == "pippo"'
		);
		$this->assertArrayHasKey(
			\Milko\wrapper\ArangoDB\Collection::DocumentKey(),
			$test->asArray(),
			'Has key'
		);
		$this->assertArrayHasKey(
			\Milko\wrapper\ArangoDB\Collection::DocumentRevision(),
			$test->asArray(),
			'Has revision'
		);
		$this->assertFalse(
			$test->isDirty(),
			'$test->isDirty() == FALSE'
		);
		$this->assertTrue(
			$test->isPersistent(),
			'$test->isPersistent() == TRUE'
		);

		//
		// Retrieve unexisting document.
		//
		$test = new Document( $this->mCollection, "UNKNOWN" );
		$this->assertEquals(
			[],
			$test->asArray(),
			'new Document( $this->mCollection ) ==> []'
		);
		$this->assertFalse(
			$test->isDirty(),
			'$test->isDirty() == FALSE'
		);
		$this->assertFalse(
			$test->isPersistent(),
			'$test->isPersistent() == FALSE'
		);

	} // testInstantiation.


	/*===================================================================================
	 *	testStatus      																*
	 *==================================================================================*/

	/**
	 * Test status()
	 *
	 * @covers       ClientServer::Connect()
	 * @covers       ClientServer::Disconnect()
	 * @covers       ClientServer::isConnected()
	 * @covers       ClientServer::createConnection()
	 * @covers       ClientServer::destructConnection()
	 */
	public function testStatus()
	{
		//
		// Empty document.
		//
		$test = new Document( $this->mCollection );
		$this->assertFalse(
			$test->isDirty(),
			'$test->isDirty() == FALSE'
		);

		//
		// Set property.
		//
		$test[ "A" ] = "B";
		$this->assertTrue(
			$test->isDirty(),
			'$test[ "A" ] = "B": $test->isDirty() == TRUE'
		);

		//
		// Add and get a document.
		//
		$id = $this->mCollection->AddOne( [ "_key" => "pappa", "uno" => 1 ] );
		$test = new Document( $this->mCollection, $id );
		$this->assertFalse(
			$test->IsDirty(),
			'$test->isDirty() == FALSE'
		);

		//
		// Reset property.
		//
		$test = new Document( $this->mCollection, $id );
		$test->offsetUnset( "uno" );
		$this->assertTrue(
			$test->isDirty(),
			'$test->unsetOffset( "A" ): $test->isDirty() == TRUE'
		);

	} // testStatus.



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
		$this->mCollection = $this->mDatabase->Client( "test_collection", [] );

	} // testConstructor.




} // class ArangoDocumentTest.


?>
