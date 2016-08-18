<?php

/**
 * ServerTest.php
 *
 * This file contains the unit tests of the {@link Milko\wrapper\Server} class.
 *
 *	@package	Test
 *
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/08/2016
 */

/**
 * Include local definitions.
 */
require_once(dirname( dirname(__DIR__) ) . "/includes.local.php");

/**
 * Include test class.
 */
require_once(dirname(__DIR__) . "/TestServerClass.php");

use Milko\wrapper\Server;

/**
 * Server unit tests
 *
 * We overload the parent class by implementing the abstract protected interface.
 *
 * We don't test the {@link Url} trait or inherited {@link Container} class here.
 *
 *	@covers		Milko\wrapper\Container
 *
 *	@package	Test
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		17/08/2016
 */
class ServerTest extends PHPUnit_Framework_TestCase
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
	 * @covers       Server::Connect()
	 * @covers       Server::Disconnect()
	 * @covers       Server::isConnected()
	 * @covers       Server::createConnection()
	 * @covers       Server::destructConnection()
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
		$this->assertEquals( "Server is connected", $this->mObject->Connection(), "Connection() == 'Server is connected'" );
		$this->assertEquals( "Server is connected", $result, "Connection() == 'Server is connected'" );

		$result = $this->mObject->Disconnect();
		$this->assertTrue( $result, "Disconnect() == TRUE" );
		$this->assertFalse( $this->mObject->isConnected(), "isConnected() == FALSE" );
		$this->assertNull( $this->mObject->Connection(), "Conection() === NULL" );

	} // testConnection.



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
		$this->mObject = new test_Server(
			'protocol://user:password@host:80/directory/file?key=val#frag'
		);

	} // testConstructor.




} // class ServerTest.


?>
