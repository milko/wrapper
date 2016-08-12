<?php

/**
 * ContainerTest.php
 *
 * This file contains the unit tests of the {@link Milko\wrapper\Container} class.
 *
 *	@package	Test
 *
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		12/08/2016
 */

/**
 * Include local definitions.
 */
require_once(dirname( dirname(__DIR__) ) . "/includes.local.php");

/**
 * Include test class.
 */
require_once(dirname(__DIR__) . "/TestContainerClass.php");

use Milko\wrapper\Container;

/**
 * Container unit tests
 *
 * We overload the parent class by implementing unit tests and adding two data members:
 *
 * <ul>
 *  <li><b>{@link $mClass}</b>: This data member holds the current test class name.
 *  <li><b>{@link $mObject}</b>: This data member holds the test object.
 * </ul>
 *
 *	@covers		Milko\wrapper\Container
 *
 *	@package	Test
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		12/08/2016
 */
class ContainerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Class.
	 *
	 * This attribute stores the test object class name.
	 *
	 * @var string
	 */
	public static $mClass = NULL;

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
 *								STATIC SETUP INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	setUpBeforeClass																*
	 *==================================================================================*/

	/**
	 * Set the class name
	 *
	 * This method is called before the first test is run, it will set the test class name.
	 */
	public static function setUpBeforeClass()
	{
		static::$mClass = "test_Container";

	} // setUpBeforeClass.



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
	 * 	<li><tt>$2</tt>: Array flatten switch parameter.
	 * 	<li><tt>$3</tt>: Expected object <tt>getArrayCopy()</tt>.
	 * </ul>
	 */
	public function provideConstructor()
	{
		//
		// Return test data.
		//
		return [
			//
			// Test without array flattening.
			//
			'new test_Container( NULL, FALSE );' => [
				NULL,
				FALSE,
				[]
			],
			'new test_Container( [], FALSE );' => [
				[],
				FALSE,
				[]
			],
			'new test_Container( new ArrayObject(), FALSE );' => [
				new ArrayObject(),
				FALSE,
				[]
			],
			'new test_Container( new test_Container(), FALSE );' => [
				new test_Container(),
				FALSE,
				[]
			],
			'new test_Container( [1, 2, 3], FALSE );' => [
				[1, 2, 3],
				FALSE,
				[1, 2, 3]
			],
			'new test_Container( new ArrayObject( [1, 2, 3] ), FALSE );' => [
				new ArrayObject( [1, 2, 3] ),
				FALSE,
				[1, 2, 3]
			],
			'new test_Container( new test_Container( [1, 2, 3] ), FALSE );' => [
				new test_Container( [1, 2, 3] ),
				FALSE,
				[1, 2, 3]
			],
			'new test_Container( new ArrayObject([ "uno" => 1, "due" => new test_Container([ "tre" => 3 ]) ]), FALSE );' => [
				new ArrayObject([ 'uno' => 1, 'due' => new test_Container([ 'tre' => 3 ]) ]),
				FALSE,
				['uno' => 1, 'due' => new test_Container([ 'tre' => 3 ])]
			],
			//
			// Test with array flattening.
			//
			'new test_Container( NULL, TRUE );' => [
				NULL,
				TRUE,
				[]
			],
			'new test_Container( [], TRUE );' => [
				[],
				TRUE,
				[]
			],
			'new test_Container( new ArrayObject(), TRUE );' => [
				new ArrayObject(),
				TRUE,
				[]
			],
			'new test_Container( new test_Container(), TRUE );' => [
				new test_Container(),
				TRUE,
				[]
			],
			'new test_Container( [1, 2, 3], TRUE );' => [
				[1, 2, 3],
				TRUE,
				[1, 2, 3]
			],
			'new test_Container( new ArrayObject( [1, 2, 3], TRUE ) );' => [
				new ArrayObject( [1, 2, 3] ),
				TRUE,
				[1, 2, 3]
			],
			'new test_Container( new test_Container( [1, 2, 3], TRUE ) );' => [
				new test_Container( [1, 2, 3] ),
				TRUE,
				[1, 2, 3]
			],
			'new test_Container( new ArrayObject([ "uno" => 1, "due" => new test_Container([ "tre" => 3 ]) ]), TRUE );' => [
				new ArrayObject([ 'uno' => 1, 'due' => new test_Container([ 'tre' => 3 ]) ]),
				TRUE,
				[ 'uno' => 1, 'due' =>[ 'tre' => 3 ] ]
			]
		];

	} // provideConstructor.


	/*===================================================================================
	 *	provideConstructorErrors														*
	 *==================================================================================*/

	/**
	 * Provide invalid parameters to __construct() test.
	 *
	 * The data elements are:
	 *
	 * <ul>
	 * 	<li><tt>$1</tt>: Data parameter.
	 * 	<li><tt>$2</tt>: Array flatten switch parameter.
	 * </ul>
	 */
	public function provideConstructorErrors()
	{
		//
		// Return test data.
		//
		return [
			'1' => [
				1,
				FALSE
			],
			'"string"' => [
				"string",
				FALSE
			],
			'new stdClass()' => [
				new stdClass(),
				FALSE
			]
		];

	} // provideConstructorErrors.


	/*===================================================================================
	 *	provideOffsetExists																*
	 *==================================================================================*/

	/**
	 * Provide test parameters to offsetExists() test.
	 *
	 * The data elements are:
	 *
	 * <ul>
	 * 	<li><tt>$1</tt>: Method parameter.
	 * 	<li><tt>$2</tt>: Expected result.
	 * </ul>
	 */
	public function provideOffsetExists()
	{
		//
		// Return test data.
		//
		return [

			// Top level matches.
			'offsetExists( 0 )' => [
				0,
				TRUE
			],
			'offsetExists( "array" )' => [
				"array",
				TRUE
			],
			'offsetExists( "object" )' => [
				"object",
				TRUE
			],
			'offsetExists( "nested" )' => [
				"nested",
				TRUE
			],

			// Top level misses.
			'offsetExists( 9 )' => [
				9,
				FALSE
			],
			'offsetExists( NULL )' => [
				NULL,
				FALSE
			],
			'offsetExists( "UNKNOWN" )' => [
				"UNKNOWN",
				FALSE
			],

			// Nested level matches.
			'offsetExists( [ "array", 0 ] )' => [
				[ "array", 0 ],
				TRUE
			],
			'offsetExists( [ 1, "name" ] )' => [
				[ 1, "name" ],
				TRUE
			],
			'offsetExists( [ "object", "string" ] )' => [
				[ "object", "string" ],
				TRUE
			],
			'offsetExists( [ "object", "array", 2, 0 ] )' => [
				[ "object", "array", 2, 0 ],
				TRUE
			],
			'offsetExists( [ "object", "array", 2, 0, 2, "nested", 0, "last" ] )' => [
				[ "object", "array", 2, 0, 2, "nested", 0, "last" ],
				TRUE
			],
			'offsetExists( [ "nested", 0, 0, "leaf", 0 ] )' => [
				[ "nested", 0, 0, "leaf", 0 ],
				TRUE
			],

			// Nested level misses.
			'offsetExists( [ "array", 9 ] )' => [
				[ "array", 9 ],
				FALSE
			],
			'offsetExists( [ 9, 0 ] )' => [
				[ 9, 0 ],
				FALSE
			],
			'offsetExists( [ "object", "UNKNOWN" ] )' => [
				[ "object", "UNKNOWN" ],
				FALSE
			],
			'offsetExists( [ "UNKNOWN", "string" ] )' => [
				[ "UNKNOWN", "string" ],
				FALSE
			],
			'offsetExists( [ "object", "array", 2, 0, "nested", 0, "UNKNOWN" ] )' => 	[
				[ "object", "array", 2, 0, "nested", 0, "UNKNOWN" ],
				FALSE
			],
			'offsetExists( [ "object", "array", 2, 0, "nested", 9, "last" ] )' => [
				[ "object", "array", 2, 0, "nested", 9, "last" ],
				FALSE
			],
			'offsetExists( [ "object", "array", 2, 0, "UNKNOWN", 0, "last" ] )' => [
				[ "object", "array", 2, 0, "UNKNOWN", 0, "last" ],
				FALSE
			],
			'offsetExists( [ "object", "array", 2, 9, "nested", 0, "last" ] )' => [
				[ "object", "array", 2, 9, "nested", 0, "last" ],
				FALSE
			],
			'offsetExists( [ "object", "array", 9, 0, "nested", 0, "last" ] )' => [
				[ "object", "array", 9, 0, "nested", 0, "last" ],
				FALSE
			],
			'offsetExists( [ "object", "UNKNOWN", 2, 0, "nested", 0, "last" ] )' => [
				[ "object", "UNKNOWN", 2, 0, "nested", 0, "last" ],
				FALSE
			],
			'offsetExists( [ "UNKNOWN", "array", 2, 0, "nested", 0, "last" ] )' => [
				[ "UNKNOWN", "array", 2, 0, "nested", 0, "last" ],
				FALSE
			],
			'offsetExists( [ "nested", 9, 0, NULL, 9 ] )' => 	[
				[ "nested", 9, 0, NULL, 9 ],
				FALSE
			],
			'offsetExists( [ NULL, 9, 9, NULL, 9 ] )' => [
				[ NULL, 9, 9, NULL, 9 ],
				FALSE
			]
		];

	} // provideOffsetExists.


	/*===================================================================================
	 *	provideOffsetExistsErrors														*
	 *==================================================================================*/

	/**
	 * Test offsetExists() exceptions.
	 *
	 * The data parameter is the parameter to the method.
	 */
	public function provideOffsetExistsErrors()
	{
		//
		// Return test data.
		//
		return [
			[ new DateTime() ],
			[ [ 1, [ 2, 3 ], 4 ] ]
		];

	} // provideOffsetExistsErrors.


	/*===================================================================================
	 *	provideOffsetGet																*
	 *==================================================================================*/

	/**
	 * Provide test parameters to offsetGet() test.
	 *
	 * The data elements are:
	 *
	 * <ul>
	 * 	<li><tt>$1</tt>: Method parameter.
	 * 	<li><tt>$2</tt>: Expected result.
	 * </ul>
	 */
	public function provideOffsetGet()
	{
		//
		// Return test data.
		//
		return [

			// Top level matches.
			'offsetGet( 0 )' => [
				0,
				"zero"
			],
			'offsetGet( "array" )' => [
				"array",
				[ 1, 2, 3 ]
			],
			'offsetGet( "nested" )' => [
				"nested",
				[
					new ArrayObject([
						new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			],

			// Top level misses.
			'offsetGet( 9 )' => [
				9,
				NULL
			],
			'offsetGet( NULL )' => [
				NULL,
				NULL
			],
			'offsetGet( "UNKNOWN" )' => [
				"UNKNOWN",
				NULL
			],

			// Nested level matches.
			'offsetGet( [ "array", 0 ] )' => [
				[ "array", 0 ],
				1
			],
			'offsetGet( [ 1, "name" ] )' => [
				[ 1, "name" ],
				"smith"
			],
			'offsetGet( [ "object", "string" ] )' => [
				[ "object", "string" ],
				"a string"
			],
			'offsetGet( [ "object", "array", 2, 0 ] )' => [
				[ "object", "array", 2, 0 ],
				new Container([
					"uno",
					"due",
					new ArrayObject([
						"nested" => [
							new test_Container([
								"last" => "leaf"
							])
						]
					]),
					new stdClass()
				])
			],
			'offsetGet( [ "object", "array", 2, 0, 2, "nested", 0, "last" ] )' => [
				[ "object", "array", 2, 0, 2, "nested", 0, "last" ],
				"leaf"
			],
			'offsetGet( [ "nested", 0, 0, "leaf", 0 ] )' => [
				[ "nested", 0, 0, "leaf", 0 ],
				"value"
			],

			// Nested level misses.
			'offsetGet( [ "array", 9 ] )' => [
				[ "array", 9 ],
				NULL
			],
			'offsetGet( [ 9, 0 ] )' => [
				[ 9, 0 ],
				NULL
			],
			'offsetGet( [ "object", "UNKNOWN" ] )' => [
				[ "object", "UNKNOWN" ],
				NULL
			],
			'offsetGet( [ "UNKNOWN", "string" ] )' => [
				[ "UNKNOWN", "string" ],
				NULL
			],
			'offsetGet( [ "object", "array", 2, 0, "nested", 0, "UNKNOWN" ] )' => 	[
				[ "object", "array", 2, 0, "nested", 0, "UNKNOWN" ],
				NULL
			],
			'offsetGet( [ "object", "array", 2, 0, "nested", 9, "last" ] )' => [
				[ "object", "array", 2, 0, "nested", 9, "last" ],
				NULL
			],
			'offsetGet( [ "object", "array", 2, 0, "UNKNOWN", 0, "last" ] )' => [
				[ "object", "array", 2, 0, "UNKNOWN", 0, "last" ],
				NULL
			],
			'offsetGet( [ "object", "array", 2, 9, "nested", 0, "last" ] )' => [
				[ "object", "array", 2, 9, "nested", 0, "last" ],
				NULL
			],
			'offsetGet( [ "object", "array", 9, 0, "nested", 0, "last" ] )' => [
				[ "object", "array", 9, 0, "nested", 0, "last" ],
				NULL
			],
			'offsetGet( [ "object", "UNKNOWN", 2, 0, "nested", 0, "last" ] )' => [
				[ "object", "UNKNOWN", 2, 0, "nested", 0, "last" ],
				NULL
			],
			'offsetGet( [ "UNKNOWN", "array", 2, 0, "nested", 0, "last" ] )' => [
				[ "UNKNOWN", "array", 2, 0, "nested", 0, "last" ],
				NULL
			],
			'offsetGet( [ "nested", 9, 0, NULL, 9 ] )' => 	[
				[ "nested", 9, 0, NULL, 9 ],
				NULL
			],
			'offsetGet( [ NULL, 9, 9, NULL, 9 ] )' => [
				[ NULL, 9, 9, NULL, 9 ],
				NULL
			]
		];

	} // provideOffsetGet.



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
	 * @covers       test_Container::__construct()
	 * @dataProvider provideConstructor
	 *
	 * @param $theParameter1
	 * @param $theParameter2
	 * @param $theExpected
	 */
	public function testConstructor( $theParameter1, $theParameter2, $theExpected )
	{
		//
		// Create object.
		//
		$object = new test_Container( $theParameter1, $theParameter2 );

		//
		// Check object contents.
		//
		$this->assertEquals( $object->getArrayCopy(), $theExpected );

	} // testConstructor.


	/*===================================================================================
	 *	testConstructorErrors															*
	 *==================================================================================*/

	/**
	 * Test __construct() exceptions
	 *
	 * @covers       test_Container::__construct()
	 * @dataProvider provideConstructorErrors
	 * @expectedException InvalidArgumentException
	 *
	 * @param $theParameter1
	 * @param $theParameter2
	 */
	public function testConstructorErrors( $theParameter1, $theParameter2 )
	{
		//
		// Should raise exception.
		//
		$object = new Container( $theParameter1, $theParameter2 );

	} // testConstructorErrors.


	/*===================================================================================
	 *	testOffsetExists																*
	 *==================================================================================*/

	/**
	 * Test offsetExists() method
	 *
	 * @covers       test_Container::offsetExists()
	 * @dataProvider provideOffsetExists
	 *
	 * @param $theParameter
	 * @param $theExpected
	 */
	public function testOffsetExists( $theParameter, $theExpected )
	{
		//
		// Show test data.
		//
//		echo( "\n" );
//		print_r( $this->mObject );
//		print_r( $this->mObject->asArray() );
//		exit;

		//
		// Test according to expected boolean.
		//
		$result = $this->mObject->offsetExists( $theParameter );
		if( $theExpected )
			$this->assertTrue( $result, $theExpected );
		else
			$this->assertFalse( $result, $theExpected );

	} // testOffsetExists.


	/*===================================================================================
	 *	testOffsetExistsErrors															*
	 *==================================================================================*/

	/**
	 * Test offsetExists() exceptions
	 *
	 * @covers       test_Container::offsetExists()
	 * @dataProvider provideOffsetExistsErrors
	 * @expectedException InvalidArgumentException
	 *
	 * @param $theParameter
	 */
	public function testOffsetExistsErrors( $theParameter )
	{
		//
		// Should raise exception.
		//
		$this->mObject->offsetExists( $theParameter );

	} // testOffsetExistsErrors.


	/*===================================================================================
	 *	testOffsetGet																	*
	 *==================================================================================*/

	/**
	 * Test offsetGet() method
	 *
	 * @covers       test_Container::offsetGet()
	 * @dataProvider provideOffsetGet
	 *
	 * @param $theParameter
	 * @param $theExpected
	 */
	public function testOffsetGet( $theParameter, $theExpected )
	{
		//
		// Show test data.
		//
//		echo( "\n" );
//		print_r( $this->mObject );
//		print_r( $this->mObject->asArray() );
//		exit;

		//
		// Make test.
		//
		$this->assertEquals( $theExpected, $this->mObject->offsetGet( $theParameter ) );

	} // testOffsetGet.


	/*===================================================================================
	 *	testOffsetSet																	*
	 *==================================================================================*/

	/**
	 * Test offsetSet() method
	 *
	 * @covers       test_Container::offsetSet()
	 */
	public function testOffsetSet()
	{
		//
		// Init local storage.
		//
		$class = static::$mClass;
		$test = new $class();

		//
		// Make tests.
		//
		$message = 'offsetSet( 0, 1 )';
		$test->offsetSet( 0, 1 );
		$this->assertEquals(
			1,
			$test->offsetGet( 0 ),
			$message
		);
		$this->assertEquals(
			new $class([ 1 ]),
			$test,
			$message
		);

		$message = 'offsetSet( "uno", 1 )';
		$test->offsetSet( "uno", 1 );
		$this->assertEquals(
			1,
			$test->offsetGet( "uno" ),
			$message
		);
		$this->assertEquals(
			new $class([ 0 => 1, "uno" => 1 ]),
			$test,
			$message
		);

		$message = 'offsetSet( NULL, "APPENDED" )';
		$test->offsetSet( NULL, "APPENDED" );
		$this->assertEquals(
			"APPENDED",
			$test->offsetGet( 1 ),
			$message );
		$this->assertEquals(
			new $class([ 0 => 1, "uno" => 1, 1 => "APPENDED" ]),
			$test,
			$message
		);

		$message = 'offsetSet( 1, "CHANGED" )';
		$test->offsetSet( 1, "CHANGED" );
		$this->assertEquals(
			"CHANGED",
			$test->offsetGet( 1 ),
			$message );
		$this->assertEquals(
			new $class([ 0 => 1, "uno" => 1, 1 => "CHANGED" ]),
			$test,
			$message
		);

		$message = 'offsetSet( 1, NULL )';
		$test->offsetSet( 1, NULL );
		$this->assertNull(
			$test->offsetGet( 1 ),
			$message );
		$this->assertEquals(
			new $class([ 0 => 1, "uno" => 1 ]),
			$test,
			$message
		);

		$message = 'offsetSet( NULL, new ArrayObject([ "array" => [ 1, 2, 3 ] ]) )';
		$test->offsetSet( NULL, new ArrayObject([ "array" => [ 1, 2, 3 ] ]) );
		$this->assertEquals(
			new ArrayObject([
				"array" => [ 1, 2, 3 ]
			]),
			$test->offsetGet( 2 ),
			$message );
		$this->assertEquals(
			new $class([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3 ]
				])
			]),
			$test,
			$message
		);

		$message = 'offsetSet( [ 2, "nested" ], new $class([ "object" => new ArrayObject([ "array" => [ "uno", "due" ] ]) ]) )';
		$test->offsetSet( [ 2, "nested" ], new $class([ "object" => new ArrayObject([ "array" => [ "uno", "due" ] ]) ]) );
		$this->assertEquals(
			new $class([
				"object" => new ArrayObject([
					"array" => [ "uno", "due" ]
				])
			]),
			$test->offsetGet( [ 2, "nested" ] ),
			$message );
		$this->assertEquals(
			new $class([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3 ],
					"nested" => new $class([
						"object" => new ArrayObject([
							"array" => [ "uno", "due" ]
						])
					])
				])
			]),
			$test,
			$message
		);

		$message = 'offsetSet( [ 2, "inserted", "array", "string" ], "a string" )';
		$test->offsetSet( [ 2, "inserted", "array", "string" ], "a string" );
		$this->assertEquals(
			"a string",
			$test->offsetGet( [ 2, "inserted", "array", "string" ] ),
			$message );
		$this->assertEquals(
			new $class([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3 ],
					"nested" => new $class([
						"object" => new ArrayObject([
							"array" => [ "uno", "due" ]
						])
					]),
					"inserted" => [
						"array" => [
							"string" => "a string"
						]
					]
				])
			]),
			$test,
			$message
		);

		$message = 'offsetSet( [ 2, "array", "number" ], 32 )';
		$test->offsetSet( [ 2, "array", "number" ], 32 );
		$this->assertEquals(
			32,
			$test->offsetGet( [ 2, "array", "number" ] ),
			$message );
		$this->assertEquals(
			new $class([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3, "number" => 32 ],
					"nested" => new $class([
						"object" => new ArrayObject([
							"array" => [ "uno", "due" ]
						])
					]),
					"inserted" => [
						"array" => [
							"string" => "a string"
						]
					]
				])
			]),
			$test,
			$message
		);

		$message = 'offsetSet( [ 2, "nested", "object", "array", NULL ], "tre" )';
		$test->offsetSet( [ 2, "nested", "object", "array", NULL ], "tre" );
		$this->assertEquals(
			"tre",
			$test->offsetGet( [ 2, "nested", "object", "array", 2 ] ),
			$message );
		$this->assertEquals(
			new $class([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3, "number" => 32 ],
					"nested" => new $class([
						"object" => new ArrayObject([
							"array" => [ "uno", "due", "tre" ]
						])
					]),
					"inserted" => [
						"array" => [
							"string" => "a string"
						]
					]
				])
			]),
			$test,
			$message
		);

		$message = 'offsetSet( [ 2, "nested", NULL, "object", NULL ], "Appended?" )';
		$test->offsetSet( [ 2, "nested", NULL, "object", NULL ], "Appended?" );
		$this->assertEquals(
			"Appended?",
			$test->offsetGet( [ 2, "nested", 0, "object", 0 ] ),
			$message );
		$this->assertEquals(
			new $class([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3, "number" => 32 ],
					"nested" => new test_Container([
						"object" => new ArrayObject([
							"array" => [ "uno", "due", "tre" ]
						]),
						0 => [
							"object" => [ "Appended?" ]
						]
					]),
					"inserted" => [
						"array" => [
							"string" => "a string"
						]
					]
				])
			]),
			$test,
			$message
		);

		$message = 'offsetSet( [ 2, "inserted", "array", "string" ], NULL )';
		$test->offsetSet( [ 2, "inserted", "array", "string" ], NULL );
		$this->assertEquals(
			NULL,
			$test->offsetGet( [ 2, "inserted", "array", "string" ] ),
			$message );
		$this->assertEquals(
			new $class([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3, "number" => 32 ],
					"nested" => new test_Container([
						"object" => new ArrayObject([
							"array" => [ "uno", "due", "tre" ]
						]),
						0 => [
							"object" => [ "Appended?" ]
						]
					])
				])
			]),
			$test,
			$message
		);

		$message = 'offsetSet( [ 2, "nested", 0, "object", 0 ], NULL )';
		$test->offsetSet( [ 2, "nested", 0, "object", 0 ], NULL );
		$this->assertEquals(
			NULL,
			$test->offsetGet( [ 2, "nested", 0, "object", 0 ] ),
			$message );
		$this->assertEquals(
			new $class([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3, "number" => 32 ],
					"nested" => new test_Container([
						"object" => new ArrayObject([
							"array" => [ "uno", "due", "tre" ]
						])
					])
				])
			]),
			$test,
			$message
		);

	} // testOffsetSet.



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
		// Get class.
		//
		$class = static::$mClass;

		//
		// Instantiate object.
		//
		$this->mObject = new $class([
			"zero",
			"array" => [ 1, 2, 3 ],
			"object" => new ArrayObject([
				"string" => "a string",
				"number" => 25,
				"array" => [
					"one",
					"two",
					[
						new Container([
							"uno",
							"due",
							new ArrayObject([
								"nested" => [
									new test_Container([
										"last" => "leaf"
									])
								]
							]),
							new stdClass()
						])
					]
				]
			]),
			"nested" => [
				new ArrayObject([
					new Container([
						"leaf" => [
							"value"
						]
					])
				])
			],
			[ "name" => "smith" ]
		]);

	} // testConstructor.




} // class Container.


?>
