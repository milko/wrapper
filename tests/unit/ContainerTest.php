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
			"new test_Container( NULL, FALSE );" => [
				NULL,
				FALSE,
				[]
			],
			"new test_Container( [], FALSE );" => [
				[],
				FALSE,
				[]
			],
			"new test_Container( new ArrayObject(), FALSE );" => [
				new ArrayObject(),
				FALSE,
				[]
			],
			"new test_Container( new test_Container(), FALSE );" => [
				new test_Container(),
				FALSE,
				[]
			],
			"new test_Container( [1, 2, 3], FALSE );" => [
				[1, 2, 3],
				FALSE,
				[1, 2, 3]
			],
			"new test_Container( new ArrayObject( [1, 2, 3] ), FALSE );" => [
				new ArrayObject( [1, 2, 3] ),
				FALSE,
				[1, 2, 3]
			],
			"new test_Container( new test_Container( [1, 2, 3] ), FALSE );" => [
				new test_Container( [1, 2, 3] ),
				FALSE,
				[1, 2, 3]
			],
			"new test_Container( new ArrayObject([ 'uno' => 1, 'due' => new Container([ 'tre' => 3 ]) ]), FALSE );" => [
				new ArrayObject([ 'uno' => 1, 'due' => new Container([ 'tre' => 3 ]) ]),
				FALSE,
				['uno' => 1, 'due' => new Container([ 'tre' => 3 ])]
			],
			//
			// Test with array flattening.
			//
			"new test_Container( NULL, TRUE );" => [
				NULL,
				TRUE,
				[]
			],
			"new test_Container( [], TRUE );" => [
				[],
				TRUE,
				[]
			],
			"new test_Container( new ArrayObject(), TRUE );" => [
				new ArrayObject(),
				TRUE,
				[]
			],
			"new test_Container( new test_Container(), TRUE );" => [
				new test_Container(),
				TRUE,
				[]
			],
			"new test_Container( [1, 2, 3], TRUE );" => [
				[1, 2, 3],
				TRUE,
				[1, 2, 3]
			],
			"new test_Container( new ArrayObject( [1, 2, 3], TRUE ) );" => [
				new ArrayObject( [1, 2, 3] ),
				TRUE,
				[1, 2, 3]
			],
			"new test_Container( new test_Container( [1, 2, 3], TRUE ) );" => [
				new test_Container( [1, 2, 3] ),
				TRUE,
				[1, 2, 3]
			],
			"new test_Container( new ArrayObject([ 'uno' => 1, 'due' => new test_Container([ 'tre' => 3 ]) ]), TRUE );" => [
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
							new Container([
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


	/*===================================================================================
	 *	provideIsArray																	*
	 *==================================================================================*/

	/**
	 * Provide test parameters to IsArray() test.
	 *
	 * The data elements are:
	 *
	 * <ul>
	 * 	<li><tt>$1</tt>: Method parameter.
	 * 	<li><tt>$2</tt>: Expected result.
	 * </ul>
	 */
	public function provideIsArray()
	{
		//
		// Return test data.
		//
		return [
			'IsArray( [] )' => [
				[],
				TRUE
			],
			'IsArray( new ArrayObject() )' => [
				new ArrayObject(),
				TRUE
			],
			'IsArray( new Container() )' => [
				new Container(),
				TRUE
			],
			'IsArray( [ 1, 2, 3 ] )' => [
				[ 1, 2, 3 ],
				TRUE
			],
			'IsArray( [ 0 => 1, 1 => 2, 2 => 3 ] )' => [
				[ 0 => 1, 1 => 2, 2 => 3 ],
				TRUE
			],
			'IsArray( new ArrayObject( [ 1, 2, 3 ] ) )' => [
				new ArrayObject( [ 1, 2, 3 ] ),
				TRUE
			],
			'IsArray( new Container( [ 1, 2, 3 ] ) )' => [
				new Container( [ 1, 2, 3 ] ),
				TRUE
			],
			'IsArray( new ArrayObject( [ 0 => 1, 1 => 2, 2 => 3 ] ) )' => [
				new ArrayObject( [ 0 => 1, 1 => 2, 2 => 3 ] ),
				TRUE
			],

			'IsArray( [ 1 => 1 ] )' => [
				[ 1 => 1 ],
				FALSE
			],
			'IsArray( [ 0 => 1, 2 => 2 ] )' => [
				[ 0 => 1, 2 => 2 ],
				FALSE
			],
			'IsArray( [ "one" => 1 ] )' => [
				[ "one" => 1 ],
				FALSE
			],
			'IsArray( new ArrayObject([ "one" => 1 ]) )' => [
				new ArrayObject([ "one" => 1 ]),
				FALSE
			],
			'IsArray( "string" )' => [
				"string",
				FALSE
			],
			'IsArray( 0 )' => [
				0,
				FALSE
			],
		];

	} // provideIsArray.



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
		// Make test.
		//
		$object = new test_Container( $theParameter1, $theParameter2 );
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
		// Instantiate object.
		//
		$test = new test_Container();

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
			new test_Container([ 1 ]),
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
			new test_Container([ 0 => 1, "uno" => 1 ]),
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
			new test_Container([ 0 => 1, "uno" => 1, 1 => "APPENDED" ]),
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
			new test_Container([ 0 => 1, "uno" => 1, 1 => "CHANGED" ]),
			$test,
			$message
		);

		$message = 'offsetSet( 1, NULL )';
		$test->offsetSet( 1, NULL );
		$this->assertNull(
			$test->offsetGet( 1 ),
			$message );
		$this->assertEquals(
			new test_Container([ 0 => 1, "uno" => 1 ]),
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
			new test_Container([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3 ]
				])
			]),
			$test,
			$message
		);

		$message = 'offsetSet( [ 2, "nested" ], new test_Container([ "object" => new ArrayObject([ "array" => [ "uno", "due" ] ]) ]) )';
		$test->offsetSet( [ 2, "nested" ], new test_Container([ "object" => new ArrayObject([ "array" => [ "uno", "due" ] ]) ]) );
		$this->assertEquals(
			new test_Container([
				"object" => new ArrayObject([
					"array" => [ "uno", "due" ]
				])
			]),
			$test->offsetGet( [ 2, "nested" ] ),
			$message );
		$this->assertEquals(
			new test_Container([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3 ],
					"nested" => new test_Container([
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
			new test_Container([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3 ],
					"nested" => new test_Container([
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
			new test_Container([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3, "number" => 32 ],
					"nested" => new test_Container([
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
			new test_Container([
				0 => 1,
				"uno" => 1,
				2 => new ArrayObject([
					"array" => [ 1, 2, 3, "number" => 32 ],
					"nested" => new test_Container([
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
			new test_Container([
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
			new test_Container([
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
			new test_Container([
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


	/*===================================================================================
	 *	testOffsetUnset																	*
	 *==================================================================================*/

	/**
	 * Test offsetUnset() method
	 *
	 * @covers       test_Container::offsetUnset()
	 */
	public function testOffsetUnset()
	{
		//
		// Make tests.
		//
		$message = 'offsetUnset( NULL )';
		$this->mObject->offsetUnset( NULL );
		$this->assertEquals(
			new test_Container([
				0 => "zero",
				"array" => [
					0 => 1,
					1 => 2,
					2 => 3
				],
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						0 => "one",
						1 => "two",
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								]),
								3 => new stdClass()
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				],
				1 => [ "name" => "smith" ]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( "UNKNOWN" )';
		$this->mObject->offsetUnset( "UNKNOWN" );
		$this->assertEquals(
			new test_Container([
				0 => "zero",
				"array" => [
					0 => 1,
					1 => 2,
					2 => 3
				],
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						0 => "one",
						1 => "two",
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								]),
								3 => new stdClass()
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				],
				1 => [ "name" => "smith" ]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( 9 )';
		$this->mObject->offsetUnset( 9 );
		$this->assertEquals(
			new test_Container([
				0 => "zero",
				"array" => [
					0 => 1,
					1 => 2,
					2 => 3
				],
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						0 => "one",
						1 => "two",
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								]),
								3 => new stdClass()
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				],
				1 => [ "name" => "smith" ]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( 0 )';
		$this->mObject->offsetUnset( 0 );
		$this->assertEquals(
			new test_Container([
				"array" => [
					0 => 1,
					1 => 2,
					2 => 3
				],
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						0 => "one",
						1 => "two",
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								]),
								3 => new stdClass()
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				],
				1 => [ "name" => "smith" ]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ 1, "name" ] )';
		$this->mObject->offsetUnset( [ 1, "name" ] );
		$this->assertEquals(
			new test_Container([
				"array" => [
					0 => 1,
					1 => 2,
					2 => 3
				],
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						0 => "one",
						1 => "two",
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								]),
								3 => new stdClass()
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "object", "array", 2, 0, 3 ] )';
		$this->mObject->offsetUnset( [ "object", "array", 2, 0, 3 ] );
		$this->assertEquals(
			new test_Container([
				"array" => [
					0 => 1,
					1 => 2,
					2 => 3
				],
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						0 => "one",
						1 => "two",
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								])
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "array", 0 ] )';
		$this->mObject->offsetUnset( [ "array", 0 ] );
		$this->assertEquals(
			new test_Container([
				"array" => [
					1 => 2,
					2 => 3
				],
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						0 => "one",
						1 => "two",
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								])
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "object", "array", 0 ] )';
		$this->mObject->offsetUnset( [ "object", "array", 0 ] );
		$this->assertEquals(
			new test_Container([
				"array" => [
					1 => 2,
					2 => 3
				],
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						1 => "two",
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								])
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "array" ] )';
		$this->mObject->offsetUnset( [ "array" ] );
		$this->assertEquals(
			new test_Container([
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						1 => "two",
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								])
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "object", "array", 1 ] )';
		$this->mObject->offsetUnset( [ "object", "array", 1 ] );
		$this->assertEquals(
			new test_Container([
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						2 => [
							0 => new Container([
								0 => "uno",
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								])
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "object", "array", 2, 0, 0 ] )';
		$this->mObject->offsetUnset( [ "object", "array", 2, 0, 0 ] );
		$this->assertEquals(
			new test_Container([
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						2 => [
							0 => new Container([
								1 => "due",
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								])
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "object", "array", 2, 0, 1 ] )';
		$this->mObject->offsetUnset( [ "object", "array", 2, 0, 1 ] );
		$this->assertEquals(
			new test_Container([
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
					"array" => [
						2 => [
							0 => new Container([
								2 => new ArrayObject([
									"nested" => [
										0 => new Container([
											"last" => "leaf"
										])
									]
								])
							])
						]
					]
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "object", "array", 2, 0, 2, "nested", 0, "last" ] )';
		$this->mObject->offsetUnset( [ "object", "array", 2, 0, 2, "nested", 0, "last" ] );
		$this->assertEquals(
			new test_Container([
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
				]),
				"nested" => [
					0 => new ArrayObject([
						0 => new Container([
							"leaf" => [
								"value"
							]
						])
					])
				]
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "nested", 0, 0, "leaf" ] )';
		$this->mObject->offsetUnset( [ "nested", 0, 0, "leaf" ] );
		$this->assertEquals(
			new test_Container([
				"object" => new ArrayObject([
					"string" => "a string",
					"number" => 25,
				])
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "object", "number" ] )';
		$this->mObject->offsetUnset( [ "object", "number" ] );
		$this->assertEquals(
			new test_Container([
				"object" => new ArrayObject([
					"string" => "a string"
				])
			]),
			$this->mObject,
			$message
		);

		$message = 'offsetUnset( [ "object", "string" ] )';
		$this->mObject->offsetUnset( [ "object", "string" ] );
		$this->assertEquals(
			new test_Container(),
			$this->mObject,
			$message
		);

	} // testOffsetUnset.


	/*===================================================================================
	 *	testGetIterator																	*
	 *==================================================================================*/

	/**
	 * Test getIterator() method
	 *
	 * @covers       test_Container::getIterator()
	 */
	public function testGetIterator()
	{
		//
		// Instantiate object.
		//
		$array = [ 1 => "one", 2 => "due", "tres" => 3 ];
		$test = new test_Container( $array );

		//
		// Test iterator.
		//
		$iterator = $test->getIterator();
		foreach( $iterator as $key => $value )
		{
			$message = "key [$key] exists";
			$this->assertTrue( array_key_exists( $key, $array ), $message );

			$message = "key [$key] matches value [$value]";
			$this->assertEquals( $key, array_search( $value, $array, TRUE ), $message );

			$message = "value is [$value]";
			$this->assertEquals( $value, $array[ $key ], $message );
		}

	} // testGetIterator.


	/*===================================================================================
	 *	testCount																		*
	 *==================================================================================*/

	/**
	 * Test count() method
	 *
	 * @covers       test_Container::count()
	 */
	public function testCount()
	{
		//
		// Start tests.
		//
		$test = new test_Container();
		$this->assertEquals( 0, $test->count(), "count test_Container()" );

		$test = new test_Container([ 1, 2, 3 ]);
		$this->assertEquals( 3, $test->count(), "count test_Container([ 1, 2, 3 ])" );

	} // testCount.


	/*===================================================================================
	 *	testArrayFunctions																*
	 *==================================================================================*/

	/**
	 * Test array function methods
	 *
	 * @covers       test_Container::getArrayCopy()
	 * @covers       test_Container::array_keys()
	 * @covers       test_Container::array_values()
	 * @covers       test_Container::asort()
	 * @covers       test_Container::ksort()
	 * @covers       test_Container::krsort()
	 * @covers       test_Container::natcasesort()
	 * @covers       test_Container::natsort()
	 * @covers       test_Container::arsort()
	 * @covers       test_Container::array_push()
	 * @covers       test_Container::array_pop()
	 * @covers       test_Container::array_unshift()
	 * @covers       test_Container::array_shift()
	 */
	public function testArrayFunctions()
	{
		//
		// Init local storage.
		//
		$test = new test_Container([ "uno" => 1, "due" => 2, 3 => "tre" ]);

		//
		// Start tests.
		//
		$message = 'getArrayCopy() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$result = $test->getArrayCopy();
		$this->assertEquals( [ "uno" => 1, "due" => 2, 3 => "tre" ], $result, $message );

		$message = 'array_keys() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$result = $test->array_keys();
		$this->assertEquals( [ "uno", "due", 3 ], $result, $message );

		$message = 'array_values() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$result = $test->array_values();
		$this->assertEquals( [ 1, 2, "tre" ], $result, $message );

		$message = 'asort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->asort();
		$this->assertEquals( [ 3, "uno", "due" ], $test->array_keys(), $message );

		$message = 'ksort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->ksort();
		$this->assertEquals( [ "due", "uno", 3 ], $test->array_keys(), $message );

		$message = 'krsort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->krsort();
		$this->assertEquals( [ 3, "uno", "due" ], $test->array_keys(), $message );

		$message = 'natcasesort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->natcasesort();
		$this->assertEquals( [ "uno", "due", 3 ], $test->array_keys(), $message );

		$message = 'natsort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->natsort();
		$this->assertEquals( [ "uno", "due", 3 ], $test->array_keys(), $message );

		$message = 'arsort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->arsort();
		$this->assertEquals( [ "due", "uno", 3 ], $test->array_keys(), $message );

		$message = 'array_push( "A", "B" )';
		$test->array_push( "A", "B" );
		$this->assertEquals( [ "due" => 2, "uno" => 1, 3 => "tre", 4 => [ "A", "B" ] ], $test->getArrayCopy(), $message );

		$message = 'array_pop()';
		$this->assertEquals( [ 0 => "A", 1 => "B" ], $test->array_pop(), $message );
		$this->assertEquals( [ "due" => 2, "uno" => 1, 3 => "tre" ], $test->getArrayCopy(), $message );

		$message = 'array_unshift( "A", "B" )';
		$test->array_unshift( "A", "B" );
		$this->assertEquals( [ 0 => [ "A", "B" ], "due" => 2, "uno" => 1, 1 => "tre" ], $test->getArrayCopy(), $message );

		$message = 'array_shift()';
		$result = $test->array_shift();
		$this->assertEquals( $result, [ 0 => "A", 1 => "B" ], $message );
		$this->assertEquals( [ "due" => 2, "uno" => 1, 0 => "tre" ], $test->getArrayCopy(), $message );

	} // testArrayFunctions.


	/*===================================================================================
	 *	testPropertyReference															*
	 *==================================================================================*/

	/**
	 * Test propertyReference() method
	 *
	 * @covers       test_Container::propertyReference()
	 */
	public function testPropertyReference()
	{
		//
		// Start tests.
		//
		$message = 'propertyReference()';
		$result = & $this->mObject->propertyReference();
		$this->assertTrue( $this->mObject->getArrayCopy() === $result, $message );

		$message = 'propertyReference( NULL )';
		$result = & $this->mObject->propertyReference( NULL );
		$this->assertTrue( $this->mObject->getArrayCopy() === $result, $message );

		$message = 'propertyReference( [] )';
		$result = & $this->mObject->propertyReference( [] );
		$this->assertTrue( $this->mObject->getArrayCopy() === $result, $message );

		$message = '$result = & propertyReference( 0 ); $result = 3;';
		$result = & $this->mObject->propertyReference( 0 );
		$this->assertEquals( "zero", $result, $message );
		$result = 3;
		$this->assertEquals( 3, $this->mObject[ 0 ], $message );

		$message = '$result = & propertyReference( [ "array", 1 ] ); $result = "due";';
		$result = & $this->mObject->propertyReference( [ "array", 1 ] );
		$this->assertEquals( 2, $result, $message );
		$result = "due";
		$this->assertEquals( "due", $this->mObject[ [ "array", 1 ] ], $message );

		$message = '$result = & propertyReference( [ "object", "array", 2, 0, 2, "nested", 0, "last" ] ); $result = "ultimo";';
		$result = & $this->mObject->propertyReference( [ "object", "array", 2, 0, 2, "nested", 0, "last" ] );
		$this->assertEquals( $result, "leaf", $message );
		$result = "ultimo";
		$this->assertEquals( "ultimo", $this->mObject[ [ "object", "array", 2, 0, 2, "nested", 0, "last" ] ], $message );

		unset( $result );

	} // testPropertyReference.


	/*===================================================================================
	 *	testPropertySchema																*
	 *==================================================================================*/

	/**
	 * Test propertySchema() method
	 *
	 * @covers       test_Container::propertySchema()
	 */
	public function testPropertySchema()
	{
		//
		// Start tests.
		//
		$message = 'propertySchema()';
		$result = $this->mObject->propertySchema();
		$this->assertEquals(
			[
				0 => [
					[ 0 ]
				],
				"array" => [
					[ "array" ],
					[ "object", "array" ]
				],
				"last" => [
					[ "object", "array", "nested", "last" ]
				],
				"leaf" => [
					[ "nested", "leaf" ]
				],
				"name" => [
					[ 1, "name" ]
				],
				"number" => [
					[ "object", "number" ]
				],
				"string" => [
					[ "object", "string" ]
				]
			],
			$result,
			$message
		);

		foreach( $result as $leaf => $branches )
		{
			foreach( $branches as $branch )
				$this->assertEquals( $leaf, $branch[ count( $branch ) - 1 ], "Checking leaf [$leaf]" );
		}

		$message = 'propertySchema( "." )';
		$result = $this->mObject->propertySchema( "." );
		$this->assertEquals(
			[
				0 => [ 0 ],
				"array" => [ "array", "object.array" ],
				"last" => [ "object.array.nested.last" ],
				"leaf" => [ "nested.leaf" ],
				"name" => [ "1.name" ],
				"number" => [ "object.number" ],
				"string" => [ "object.string" ]
			],
			$result,
			$message
		);

	} // testPropertySchema.


	/*===================================================================================
	 *	testAsArray																		*
	 *==================================================================================*/

	/**
	 * Test asArray() and toArray() methods
	 *
	 * @covers       test_Container::asArray()
	 * @covers       test_Container::toArray()
	 */
	public function testAsArray()
	{
		//
		// Instantiate expected.
		//
		$expected = [
			0 => "zero",
			"array" => [
				0 => 1,
				1 => 2,
				2 => 3
			],
			"object" => [
				"string" => "a string",
				"number" => 25,
				"array" => [
					0 => "one",
					1 => "two",
					2 => [
						0 => [
							0 => "uno",
							1 => "due",
							2 => [
								"nested" => [
									0 => [
										"last" => "leaf"
									]
								]
							],
							3 => new stdClass()
						]
					]
				]
			],
			"nested" => [
				0 => [
					0 => [
						"leaf" => [
							"value"
						]
					]
				]
			],
			1 => [ "name" => "smith" ]
		];

		//
		// Start tests.
		//
		$result = $this->mObject->asArray();
		$this->assertEquals( $expected, $this->mObject->asArray(), "asArray()" );
		$this->assertNotEquals( $expected, $this->mObject, "asArray()" );

		$this->mObject->toArray();
		$this->assertEquals( $expected, $this->mObject->getArrayCopy(), "toArray()" );

	} // testAsArray.


	/*===================================================================================
	 *	testConvertToArray																*
	 *==================================================================================*/

	/**
	 * Test ConvertToArray() method
	 *
	 * @covers       test_Container::ConvertToArray()
	 */
	public function testConvertToArray()
	{
		//
		// Instantiate expected.
		//
		$expected = [
			0 => "zero",
			"array" => [
				0 => 1,
				1 => 2,
				2 => 3
			],
			"object" => [
				"string" => "a string",
				"number" => 25,
				"array" => [
					0 => "one",
					1 => "two",
					2 => [
						0 => [
							0 => "uno",
							1 => "due",
							2 => [
								"nested" => [
									0 => [
										"last" => "leaf"
									]
								]
							],
							3 => new stdClass()
						]
					]
				]
			],
			"nested" => [
				0 => [
					0 => [
						"leaf" => [
							"value"
						]
					]
				]
			],
			1 => [ "name" => "smith" ]
		];

		//
		// Start tests.
		//
		Container::ConvertToArray( $this->mObject );
		$this->assertEquals( $expected, $this->mObject, "Container::ConvertToArray()" );

	} // testConvertToArray.


	/*===================================================================================
	 *	testIsArray																		*
	 *==================================================================================*/

	/**
	 * Test IsArray() method
	 *
	 * @covers       test_Container::IsArray()
	 * @dataProvider provideIsArray
	 *
	 * @param $theParameter
	 * @param $theExpected
	 */
	public function testIsArray( $theParameter, $theExpected )
	{
		//
		// Perform tests.
		//
		$result = test_Container::IsArray( $theParameter );
		if( $theExpected )
			$this->assertTrue( $result, $theExpected );
		else
			$this->assertFalse( $result, $theExpected );

	} // testIsArray.


	/*===================================================================================
	 *	testManageAttribute																*
	 *==================================================================================*/

	/**
	 * Test IsArray() method
	 *
	 * @covers       test_Container::manageAttribute()
	 */
	public function testManageAttribute()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container();

		//
		// Start tests.
		//
		$result = $test->Attribute( "NEW" );
		$message = '$result = Attribute( "NEW" )';
		$this->assertEquals( $result, "NEW", $message );
		$message = 'attribute == "NEW';
		$this->assertEquals( $test->attribute, "NEW", $message );

		$result = $test->Attribute( "OTHER", TRUE );
		$message = '$result = Attribute( "OTHER", TRUE )';
		$this->assertEquals( $result, "NEW", $message );
		$message = 'attribute == "OTHER';
		$this->assertEquals( $test->attribute, "OTHER", $message );

		$result = $test->Attribute();
		$message = '$result = Attribute()';
		$this->assertEquals( $result, "OTHER", $message );

		$result = $test->Attribute( FALSE, TRUE );
		$message = '$result = Attribute( FALSE, TRUE )';
		$this->assertEquals( $result, "OTHER", $message );
		$message = 'attribute === NULL';
		$this->assertNull( $test->attribute, $message );

	} // testManageAttribute.


	/*===================================================================================
	 *	testManageFlagAttribute															*
	 *==================================================================================*/

	/**
	 * Test manageFlagAttribute() method
	 *
	 * @covers       test_Container::manageFlagAttribute()
	 */
	public function testManageFlagAttribute()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container();

		$message = '$result = BitfieldAttribute()';
		$result = $test->BitfieldAttribute();
		$this->assertEquals( "00000000", bin2hex($result), $message );
		$this->assertEquals( $test->flag, $result, $message );

		$message = '$result = BitfieldAttribute( hex2bin("ff000000" ), TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE );
		$this->assertEquals( "ff000000", bin2hex($result), $message );
		$this->assertEquals( $test->flag, $result, $message );

		$message = '$result = BitfieldAttribute( hex2bin("ff0f" ), TRUE, TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("ff0f" ), TRUE, TRUE );
		$this->assertEquals( "ff000000", bin2hex($result), $message );
		$this->assertEquals( "ff0f0000", bin2hex($test->flag), $message );

		$message = '$result = BitfieldAttribute( hex2bin("f0000000" ) )';
		$result = $test->BitfieldAttribute( hex2bin("f0000000" ) );
		$this->assertTrue( $result, $message );

		$message = '$result = BitfieldAttribute( hex2bin("0f" ) )';
		$result = $test->BitfieldAttribute( hex2bin("0f" ) );
		$this->assertTrue( $result, $message );

		$message = '$result = BitfieldAttribute( hex2bin("000000ff" ) )';
		$result = $test->BitfieldAttribute( hex2bin("000000ff" ) );
		$this->assertFalse( $result, $message );

		$message = '$result = BitfieldAttribute( hex2bin("00f0" ) )';
		$result = $test->BitfieldAttribute( hex2bin("00f0" ) );
		$this->assertFalse( $result, $message );

		$message = '$result = BitfieldAttribute( hex2bin("f0f00000" ), FALSE, TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("f0f00000" ), FALSE, TRUE );
		$this->assertEquals( "ff0f0000", bin2hex($result), $message );
		$this->assertEquals( "0f0f0000", bin2hex($test->flag), $message );

		$message = '$result = BitfieldAttribute( hex2bin("0ff0" ), FALSE, TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("0ff0" ), FALSE, TRUE );
		$this->assertEquals( "0f0f0000", bin2hex($result), $message );
		$this->assertEquals( "000f", bin2hex($test->flag), $message );

		$message = '$result = BitfieldAttribute( hex2bin("ff000000" ), TRUE, TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE, TRUE );
		$this->assertEquals( "000f", bin2hex($result), $message );
		$this->assertEquals( "ff0f0000", bin2hex($test->flag), $message );

	} // testManageFlagAttribute.


	/*===================================================================================
	 *	testManageProperty																*
	 *==================================================================================*/

	/**
	 * Test manageProperty() method
	 *
	 * @covers       test_Container::manageProperty()
	 */
	public function testManageProperty()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container([
			"nested" => new ArrayObject([
				"container" => new Container([
					"array" => [
						"string" => "a string"
					]
				])
			]),
			"one" => new test_Container([
				"two" => 3
			])
		]);

		$message = '$result = $test->Property( "prop", "NEW" )';
		$result = $test->Property( "prop", "NEW" );
		$this->assertEquals( "NEW", $result, $message );
		$this->assertEquals( "NEW", $test[ "prop" ], $message );

		$message = '$result = $test->Property( "prop", "OTHER", TRUE )';
		$result = $test->Property( "prop", "OTHER", TRUE );
		$this->assertEquals( "NEW", $result, $message );
		$this->assertEquals( "OTHER", $test[ "prop" ], $message );

		$message = '$result = $test->Property( "prop" )';
		$result = $test->Property( "prop" );
		$this->assertEquals( "OTHER", $result, $message );
		$this->assertEquals( "OTHER", $test[ "prop" ], $message );

		$message = '$result = $test->Property( "prop", FALSE, TRUE )';
		$result = $test->Property( "prop", FALSE, TRUE );
		$this->assertEquals( "OTHER", $result, $message );
		$this->assertNull( $test[ "prop" ], $message );

		$result = $test->Property( [ "nested", "container", "array", "string" ] );
		$message = '$result = $test->Property( [ "nested", "container", "array", "string" ] )';
		$this->assertEquals( "a string", $result, $message );

		$message = '$result = $test->Property( [ "nested", "container", "UNKNOWN", "string" ] )';
		$result = $test->Property( [ "nested", "container", "UNKNOWN", "string" ] );
		$this->assertNull( $result, $message );

		$message = '$result = $test->Property( [ "nested", "container", "array", "string" ], "NEW STRING" )';
		$result = $test->Property( [ "nested", "container", "array", "string" ], "NEW STRING" );
		$this->assertEquals( "NEW STRING", $result, $message );
		$this->assertEquals( "NEW STRING", $test[ "nested" ][ "container" ][ "array" ][ "string" ], $message );

		$message = '$result = $test->Property( [ "nested", "container", "array", "string" ], "OTHER STRING", TRUE )';
		$result = $test->Property( [ "nested", "container", "array", "string" ], "OTHER STRING", TRUE );
		$this->assertEquals( "NEW STRING", $result, $message );
		$this->assertEquals( "OTHER STRING", $test[ "nested" ][ "container" ][ "array" ][ "string" ], $message );

		$message = '$result = $test->Property( [ "branch", "array", "array", "string" ], "INSERTED STRING", TRUE )';
		$result = $test->Property( [ "branch", "array", "array", "string" ], "INSERTED STRING", TRUE );
		$this->assertNull( $result, $message );
		$this->assertEquals( "INSERTED STRING", $test[ "branch" ][ "array" ][ "array" ][ "string" ], $message );

		$message = '$result = $test->Property( [ "nested", "container", "array", "string" ], FALSE, TRUE )';
		$result = $test->Property( [ "nested", "container", "array", "string" ], FALSE, TRUE );
		$this->assertEquals( "OTHER STRING", $result, $message );
		$this->assertFalse( $test->offsetExists( "nested" ), $message );

	} // testManageProperty.


	/*===================================================================================
	 *	testManageFlagProperty															*
	 *==================================================================================*/

	/**
	 * Test manageFlagProperty() method
	 *
	 * @covers       test_Container::manageFlagProperty()
	 */
	public function testManageFlagProperty()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container();
		$test[ "flag" ] = hex2bin( "00000000" );

		$message = '$result = $test->BitfieldProperty( "flag" )';
		$result = $test->BitfieldProperty( "flag" );
		$this->assertEquals( "00000000", bin2hex($result), $message );
		$this->assertEquals( $test[ "flag" ], $result, $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE )';
		$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE );
		$this->assertEquals( "ff000000", bin2hex($result), $message );
		$this->assertEquals( $test[ "flag" ], $result, $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("ff0f" ), TRUE, TRUE )';
		$result = $test->BitfieldProperty( "flag", hex2bin("ff0f" ), TRUE, TRUE );
		$this->assertEquals( "ff000000", bin2hex($result), $message );
		$this->assertEquals( "ff0f0000", bin2hex($test[ "flag" ]), $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("f0000000" ) )';
		$result = $test->BitfieldProperty( "flag", hex2bin("f0000000" ) );
		$this->assertTrue( $result, $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("0f" ) )';
		$result = $test->BitfieldProperty( "flag", hex2bin("0f" ) );
		$this->assertTrue( $result, $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("000000ff" ) )';
		$result = $test->BitfieldProperty( "flag", hex2bin("000000ff" ) );
		$this->assertFalse( $result, $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("00f0" ) )';
		$result = $test->BitfieldProperty( "flag", hex2bin("00f0" ) );
		$this->assertFalse( $result, $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("f0f00000" ), FALSE, TRUE )';
		$result = $test->BitfieldProperty( "flag", hex2bin("f0f00000" ), FALSE, TRUE );
		$this->assertEquals( "ff0f0000", bin2hex($result), $message );
		$this->assertEquals( "0f0f0000", bin2hex($test[ "flag" ]), $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("0ff0" ), FALSE, TRUE )';
		$result = $test->BitfieldProperty( "flag", hex2bin("0ff0" ), FALSE, TRUE );
		$this->assertEquals( "0f0f0000", bin2hex($result), $message );
		$this->assertEquals( "000f", bin2hex($test[ "flag" ]), $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE, TRUE )';
		$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE, TRUE );
		$this->assertEquals( "000f", bin2hex($result), $message );
		$this->assertEquals( "ff0f0000", bin2hex($test[ "flag" ]), $message );

	} // testManageFlagProperty.


	/*===================================================================================
	 *	testNestedPropertyReference														*
	 *==================================================================================*/

	/**
	 * Test nestedPropertyReference() method
	 *
	 * @covers       test_Container::nestedPropertyReference()
	 */
	public function testNestedPropertyReference()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container( [
			"nested" => new ArrayObject( [
				"container" => new test_Container( [
					"array" => [
						"string" => "a string"
					]
				] )
			] ),
			"one" => new test_Container( [
				"two" => 3
			] ),
			1 => "uno"
		] );

		$message = '$result = & $test->NestedProperty( [ 1 ] );';
		$offsets = [ 1 ];
		$result = &$test->NestedProperty( $offsets );
		$this->assertEquals( "uno", $result, $message );
		$this->assertEquals( $test[ 1 ], $result, $message );
		$this->assertCount( 0, $offsets, $message );

		$message = '$result = $test->NestedProperty( [ "nested", "container", "array", "string" ] );';
		$offsets = [ "nested", "container", "array", "string" ];
		$result = $test->NestedProperty( $offsets );
		$this->assertEquals( "a string", $result, $message );
		$this->assertEquals( $test[ "nested" ][ "container" ][ "array" ][ "string" ], $result, $message );
		$this->assertCount( 0, $offsets, $message );

		$message = '$result = $test->NestedProperty( [ "nested", "container", "array", "UNKNOWN" ] );';
		$offsets = [ "nested", "container", "array", "UNKNOWN" ];
		$result = $test->NestedProperty( $offsets );
		$this->assertEquals( [ "string" => "a string" ], $result, $message );
		$this->assertEquals( $test[ "nested" ][ "container" ][ "array" ], $result, $message );
		$this->assertEquals( [ "UNKNOWN" ], $offsets, $message );

		$message = '$result = $test->NestedProperty( [ "UNKNOWN" ] );';
		$offsets = [ "UNKNOWN" ];
		$result = $test->NestedProperty( $offsets );
		$this->assertEquals( $test->propertyReference(), $result, $message );
		$this->assertEquals( [ "UNKNOWN" ], $offsets, $message );

	} // testNestedPropertyReference.



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
		$this->mObject = new test_Container([
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
									new Container([
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




} // class ContainerTest.


?>
