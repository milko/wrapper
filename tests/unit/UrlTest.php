<?php

/**
 * UrlTest.php
 *
 * This file contains the unit tests of the {@link Milko\wrapper\Url} class.
 *
 *	@package	Test
 *
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		16/08/2016
 */

/**
 * Include local definitions.
 */
require_once(dirname( dirname(__DIR__) ) . "/includes.local.php");

use Milko\wrapper\Url;

/**
 * Url unit tests
 *
 * We test the specific class functionality as the parent class was not modified
 * substantially.
 *
 *	@covers		Milko\wrapper\Container
 *
 *	@package	Test
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		12/08/2016
 */
class UrlTest extends PHPUnit_Framework_TestCase
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
 *								PUBLIC SETUP INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	provideConstructor																*
	 *==================================================================================*/

	/**
	 * Provide data to __construct() test.
	 *
	 * The data elements are:
	 *
	 * <ul>
	 * 	<li><tt>$1</tt>: Data parameter.
	 * 	<li><tt>$3</tt>: Expected object <tt>getArrayCopy()</tt>.
	 * </ul>
	 */
	public function provideConstructor()
	{
		//
		// Return test data.
		//
		return [
			[
				NULL,
				[]
			],
			[
				'prot://host',
				[
					Url::PROT => "prot",
					Url::HOST => "host"
				]
			],
			[
				'prot://host:8080',
				[
					Url::PROT => "prot",
					Url::HOST => "host",
					Url::PORT => 8080
				]
			],
			[
				'prot://user@host:8080',
				[
					Url::PROT => "prot",
					Url::USER => "user",
					Url::HOST => "host",
					Url::PORT => 8080
				]
			],
			[
				'prot://user:pass@host:8080',
				[
					Url::PROT => "prot",
					Url::USER => "user",
					Url::PASS => "pass",
					Url::HOST => "host",
					Url::PORT => 8080
				]
			],
			[
				'prot://user:pass@host:8080/dir',
				[
					Url::PROT => "prot",
					Url::USER => "user",
					Url::PASS => "pass",
					Url::HOST => "host",
					Url::PORT => 8080,
					Url::PATH => "/dir"
				]
			],
			[
				'prot://user:pass@host:8080/dir/file',
				[
					Url::PROT => "prot",
					Url::USER => "user",
					Url::PASS => "pass",
					Url::HOST => "host",
					Url::PORT => 8080,
					Url::PATH => "/dir/file"
				]
			],
			[
				'prot://user:pass@host:8080/dir/file?key=val',
				[
					Url::PROT => "prot",
					Url::USER => "user",
					Url::PASS => "pass",
					Url::HOST => "host",
					Url::PORT => 8080,
					Url::PATH => "/dir/file",
					Url::QUERY => [ "key" => "val" ]
				]
			],
			[
				'prot://user:pass@host:8080/dir/file?key=val&arg=val&uni',
				[
					Url::PROT => "prot",
					Url::USER => "user",
					Url::PASS => "pass",
					Url::HOST => "host",
					Url::PORT => 8080,
					Url::PATH => "/dir/file",
					Url::QUERY => [
						"key" => "val",
						"arg" => "val",
						"uni" => NULL
					]
				]
			],
			[
				'prot://user:pass@host:8080/dir/file?key=val&arg=val&uni#frag',
				[
					Url::PROT => "prot",
					Url::USER => "user",
					Url::PASS => "pass",
					Url::HOST => "host",
					Url::PORT => 8080,
					Url::PATH => "/dir/file",
					Url::QUERY => [
						"key" => "val",
						"arg" => "val",
						"uni" => NULL
					],
					Url::FRAG => "frag"
				]
			],
			[
				'prot://user:pass@host1:8080,host2:9090/dir/file?key=val&arg=val&uni#frag',
				[
					Url::PROT => "prot",
					Url::USER => "user",
					Url::PASS => "pass",
					Url::HOST => [ "host1", "host2" ],
					Url::PORT => [ 8080, 9090 ],
					Url::PATH => "/dir/file",
					Url::QUERY => [
						"key" => "val",
						"arg" => "val",
						"uni" => NULL
					],
					Url::FRAG => "frag"
				]
			],
			[
				'prot://user:pass@host1:8080,host2:8181,host3:8282/dir/file?key=val&arg=val&uni#frag',
				[
					Url::PROT => "prot",
					Url::USER => "user",
					Url::PASS => "pass",
					Url::HOST => [ "host1", "host2", "host3" ],
					Url::PORT => [ 8080, 8181, 8282 ],
					Url::PATH => "/dir/file",
					Url::QUERY => [
						"key" => "val",
						"arg" => "val",
						"uni" => NULL
					],
					Url::FRAG => "frag"
				]
			]
		];

	} // provideConstructor.


	/*===================================================================================
	 *	provideOffsetSet																*
	 *==================================================================================*/

	/**
	 * Provide data to offsetSet() test.
	 *
	 * The data elements are:
	 *
	 * <ul>
	 * 	<li><tt>$1</tt>: Offset.
	 * 	<li><tt>$2</tt>: Value.
	 * </ul>
	 */
	public function provideOffsetSet()
	{
		//
		// Return test data.
		//
		return [
			"offsetSet( Url::PORT, 'string' )" => [
				Url::PORT,
				"string"
			],
			"offsetSet( Url::PORT, [ 80, 90, 'string' ] )" => [
		Url::PORT,
		[ 80, 90, "string" ]
	]
		];

	} // provideOffsetSet.


	/*===================================================================================
	 *	provideOffsetUnset																*
	 *==================================================================================*/

	/**
	 * Provide data to offsetUnset() test.
	 *
	 * The data elements are:
	 *
	 * <ul>
	 * 	<li><tt>$1</tt>: Offset.
	 * </ul>
	 */
	public function provideOffsetUnset()
	{
		//
		// Return test data.
		//
		return [
			"offsetSet( Url::PROT, NULL )" => [
				Url::PROT
			],
			"offsetSet( Url::HOST, NULL )" => [
				Url::HOST
			]
		];

	} // provideOffsetUnset.



/*=======================================================================================
 *																						*
 *								PUBLIC TEST INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	testConstructor																	*
	 *==================================================================================*/

	/**
	 * Test __construct()
	 *
	 * @covers       Url::__construct()
	 * @dataProvider provideConstructor
	 *
	 * @param $parameter
	 * @param $expected
	 */
	public function testConstructor( $parameter, $expected )
	{
		//
		// Init local storage.
		//
		$message = "new Url( $parameter )";
		$offsets = [
			Url::FRAG => "Fragment",
			Url::HOST => "Host",
			Url::PATH => "Path",
			Url::PASS => "Password",
			Url::PORT => "Port",
			Url::PROT => "Protocol",
			Url::QUERY => "Query",
			Url::USER => "User"
		];

		//
		// Instantiate object.
		//
		$object = new Url( $parameter );

		//
		// Get copy.
		//
		$copy = $object->getArrayCopy();

		//
		// Check properties.
		//
		$this->assertEquals( $copy, $expected, $message );

		//
		// Check URL.
		//
		$this->assertEquals( (string)$object, $parameter, $message );
		$this->assertEquals( $object->URL(), $parameter, $message );

		//
		// Check method accessors.
		//
		foreach( $offsets as $offset => $method )
		{
			if( $object->offsetExists( $offset ) )
				$this->assertEquals( $object->$method(), $object[ $offset ], $message );
		}

		//
		// Re-create object from properties.
		//
		if( count( $copy ) )
		{
			//
			// Build object.
			//
			$object = new Url();
			foreach( $offsets as $offset => $method )
			{
				if( array_key_exists( $offset, $copy ) )
					$object->$method( $copy[ $offset ] );
			}

			//
			// Make tests.
			//
			$this->assertEquals( $copy, $expected, $message );
			$this->assertEquals( (string)$object, $parameter, $message );
			$this->assertEquals( $object->URL(), $parameter, $message );
		}

	} // testConstructor.


	/*===================================================================================
	 *	testConstructorExceptions														*
	 *==================================================================================*/

	/**
	 * Test __construct() exceptions.
	 *
	 * @covers       Url::__construct()
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructorExceptions()
	{
		//
		// Test instantiation.
		//
		$object = new Url( '' );

	} // testConstructorExceptions.


	/*===================================================================================
	 *	testOffsetSet																	*
	 *==================================================================================*/

	/**
	 * Test offsetSet().
	 *
	 * @covers       Url::offsetSet()
	 * @dataProvider provideOffsetSet
	 * @expectedException InvalidArgumentException
	 *
	 * @param $offset
	 * @param $value
	 */
	public function testOffsetSet( $offset, $value )
	{
		//
		// Try setting non numeric port.
		//
		$this->mObject->offsetSet( $offset, $value );

	} // testOffsetSet.


	/*===================================================================================
	 *	testOffsetUnset																	*
	 *==================================================================================*/

	/**
	 * Test offsetUnset().
	 *
	 * @covers       Url::offsetUnset()
	 * @dataProvider provideOffsetUnset
	 * @expectedException BadMethodCallException
	 *
	 * @param $offset
	 */
	public function testOffsetUnset( $offset )
	{
		//
		// Try setting non numeric port.
		//
		$this->mObject->offsetUnset( $offset );

	} // testOffsetUnset.



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
		$this->mObject = new Url(
			'protocol://user:password@host:80/directory/file?key=val#frag'
		);

	} // testConstructor.




} // class UrlTest.


?>
