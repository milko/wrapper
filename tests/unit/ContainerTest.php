<?php

/*
 * This file tests the Container class.
 *
 * (c) Milko Škofič <skofic@gmail.com>
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
 * @covers Milko\wrapper\Container
 */
class ContainerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Provide constructor test parameters.
	 *
	 * $1: parameter 1 for construct.
	 * $2: parameter 2 for construct.
	 * $3: expected value.
	 */
	public function provideConstructorParameters()
	{
		return [
			//
			// Test without array flattening.
			//
			"new Container( NULL, FALSE );" => [
				NULL,
				FALSE,
				[]
			],
			"new Container( [], FALSE );" => [
				[],
				FALSE,
				[]
			],
			"new Container( new ArrayObject(), FALSE );" => [
				new ArrayObject(),
				FALSE,
				[]
			],
			"new Container( new Container(), FALSE );" => [
				new Container(),
				FALSE,
				[]
			],
			"new Container( [1, 2, 3], FALSE );" => [
				[1, 2, 3],
				FALSE,
				[1, 2, 3]
			],
			"new Container( new ArrayObject( [1, 2, 3] ), FALSE );" => [
				new ArrayObject( [1, 2, 3] ),
				FALSE,
				[1, 2, 3]
			],
			"new Container( new Container( [1, 2, 3] ), FALSE );" => [
				new Container( [1, 2, 3] ),
				FALSE,
				[1, 2, 3]
			],
			"new Container( new ArrayObject([ \"uno\" => 1, \"due\" => new Container([ \"tre\" => 3 ]) ]), FALSE );" => [
				new ArrayObject([ "uno" => 1, "due" => new Container([ "tre" => 3 ]) ]),
				FALSE,
				["uno" => 1, "due" => new Container([ "tre" => 3 ])]
			],
			//
			// Test with array flattening.
			//
			"new Container( NULL, TRUE );" => [
				NULL,
				TRUE,
				[]
			],
			"new Container( [], TRUE );" => [
				[],
				TRUE,
				[]
			],
			"new Container( new ArrayObject(), TRUE );" => [
				new ArrayObject(),
				TRUE,
				[]
			],
			"new Container( new Container(), TRUE );" => [
				new Container(),
				TRUE,
				[]
			],
			"new Container( [1, 2, 3], TRUE );" => [
				[1, 2, 3],
				TRUE,
				[1, 2, 3]
			],
			"new Container( new ArrayObject( [1, 2, 3], TRUE ) );" => [
				new ArrayObject( [1, 2, 3] ),
				TRUE,
				[1, 2, 3]
			],
			"new Container( new Container( [1, 2, 3], TRUE ) );" => [
				new Container( [1, 2, 3] ),
				TRUE,
				[1, 2, 3]
			],
			"new Container( new ArrayObject([ \"uno\" => 1, \"due\" => new Container([ \"tre\" => 3 ]) ]), TRUE );" => [
				new ArrayObject([ "uno" => 1, "due" => new Container([ "tre" => 3 ]) ]),
				TRUE,
				[ "uno" => 1, "due" =>[ "tre" => 3 ] ]
			]
		];

	} // provideConstructorParameters.

	/**
	 * Provide constructor test invalid parameters.
	 *
	 * $1: parameter 1 for construct.
	 * $2: parameter 2 for construct.
	 */
	public function provideInvalidConstructorParameters()
	{
		return [
			[
				1,
				FALSE
			],
			[
				"string",
				FALSE
			],
			[
				new stdClass(),
				FALSE
			]
		];

	} // provideInvalidConstructorParameters.

	/**
	 * Provide offsetExists() test parameters.
	 *
	 * $1: Object.
	 * $2: Parameter.
	 * $3: Expected.
	 */
	public function provideOffsetExistsParameters()
	{
		//
		// Init test object.
		//
		$test = new test_Container([
			"uno",
			"due",
			"tre",
			"array" => [
				1,
				2,
				3,
				"obj" => new ArrayObject([
					3,
					4,
					5,
					"obj" => new test_Container([
						9,
						8,
						7
					])
				])
			],
			"nested" => [
				"one" => new ArrayObject([
					"two" => [
						"three" => 3
					]
				])
			]
		]);

//		print_r( $test );
//		exit;

		return [
			//
			// Test matches.
			//
			'offsetExists( 1 )' => [
				$test,
				1,
				TRUE
			],
			'offsetExists( "array" )' => [
				$test,
				"array",
				TRUE
			],
			'offsetExists( [ "array", 0 ] )' => [
				$test,
				[ "array", 0 ],
				TRUE
			],
			'offsetExists( [ "array", "obj", 0 ] )' => [
				$test,
				[ "array", "obj", 0 ],
				TRUE
			],
			'offsetExists( [ "nested", "one", "two", "three" ] )' => [
				$test,
				[ "nested", "one", "two", "three" ],
				TRUE
			],

			//
			// Test non matches.
			//
			'offsetExists( NULL )' => [
				$test,
				NULL,
				FALSE
			],
			'offsetExists( "UNKNOWN" )' => [
				$test,
				"UNKNOWN",
				FALSE
			],
			'offsetExists( [ "array", 9 ] )' => [
				$test,
				[ "array", 9 ],
				FALSE
			],
			'offsetExists( [ "UNKNOWN", 0 ] )' => [
				$test,
				[ "UNKNOWN", 0 ],
				FALSE
			],
			'offsetExists( [ "array", "obj", "UNKNOWN" ] )' => [
				$test,
				[ "array", "obj", "UNKNOWN" ],
				FALSE
			],
			'offsetExists( [ "array", "UNKNOWN", 0 ] )' => [
				$test,
				[ "array", "UNKNOWN", 0 ],
				FALSE
			],
			'offsetExists( [ "UNKNOWN", "obj", 0 ] )' => [
				$test,
				[ "UNKNOWN", "obj", 0 ],
				FALSE
			],
			'offsetExists( [ NULL, "one", "two", "three" ] )' => [
				$test,
				[ NULL, "one", "two", "three" ],
				FALSE
			],
			'offsetExists( [ "nested", "one", NULL, "three" ] )' => [
				$test,
				[ "nested", "one", NULL, "three" ],
				FALSE
			],
			'offsetExists( [ "nested", "one", "two", NULL ] )' => [
				$test,
				[ "nested", "one", "two", NULL ],
				FALSE
			],
			'offsetExists( [ "UNKNOWN", "one", "two", "three" ] )' => [
				$test,
				[ "UNKNOWN", "one", "two", "three" ],
				FALSE
			],
			'offsetExists( [ "nested", "one", "UNKNOWN", "three" ] )' => [
				$test,
				[ "nested", "one", "UNKNOWN", "three" ],
				FALSE
			],
			'offsetExists( [ "nested", "one", "two", "UNKNOWN" ] )' => [
				$test,
				[ "nested", "one", "two", "UNKNOWN" ],
				FALSE
			]
		];

	} // provideOffsetExistsParameters.

	/**
	 * Provide offsetGet() test parameters.
	 *
	 * $1: Object.
	 * $2: Parameter.
	 * $3: Expected.
	 */
	public function provideOffsetGetParameters()
	{
		//
		// Init test object.
		//
		$test = new test_Container([
			"uno",
			"due",
			"tre",
			"array" => [
				1,
				2,
				3,
				"obj" => new ArrayObject([
					3,
					4,
					5,
					"obj" => new test_Container([
						9,
						8,
						7
					])
				])
			],
			"nested" => [
				"one" => new ArrayObject([
					"two" => [
						"three" => 3
					]
				])
			]
		]);

//		print_r( $test );
//		exit;

		return [
			//
			// Test matches.
			//
			'offsetGet( 1 )' => [
				$test,
				1,
				"due"
			],
			'offsetGet( "nested" )' => [
				$test,
				"nested",
				[ "one" => new ArrayObject( [ "two" => [ "three" => 3 ] ] ) ]
			],
			'offsetGet( [ "array", 0 ] )' => [
				$test,
				[ "array", 0 ],
				1
			],
			'offsetGet( [ "array", "obj", 0 ] )' => [
				$test,
				[ "array", "obj", 0 ],
				3
			],
			'offsetGet( [ "nested", "one", "two", "three" ] )' => [
				$test,
				[ "nested", "one", "two", "three" ],
				3
			],

			//
			// Test non matches.
			//
			'offsetGet( NULL )' => [
				$test,
				NULL,
				NULL
			],
			'offsetGet( "UNKNOWN" )' => [
				$test,
				"UNKNOWN",
				NULL
			],
			'offsetGet( [ "array", 9 ] )' => [
				$test,
				[ "array", 9 ],
				NULL
			],
			'offsetGet( [ "UNKNOWN", 0 ] )' => [
				$test,
				[ "UNKNOWN", 0 ],
				NULL
			],
			'offsetGet( [ "array", "obj", "UNKNOWN" ] )' => [
				$test,
				[ "array", "obj", "UNKNOWN" ],
				NULL
			],
			'offsetGet( [ "array", "UNKNOWN", 0 ] )' => [
				$test,
				[ "array", "UNKNOWN", 0 ],
				NULL
			],
			'offsetGet( [ "UNKNOWN", "obj", 0 ] )' => [
				$test,
				[ "UNKNOWN", "obj", 0 ],
				NULL
			],
			'offsetGet( [ NULL, "one", "two", "three" ] )' => [
				$test,
				[ NULL, "one", "two", "three" ],
				NULL
			],
			'offsetGet( [ "nested", "one", NULL, "three" ] )' => [
				$test,
				[ "nested", "one", NULL, "three" ],
				NULL
			],
			'offsetGet( [ "nested", "one", "two", NULL ] )' => [
				$test,
				[ "nested", "one", "two", NULL ],
				NULL
			],
			'offsetGet( [ "UNKNOWN", "one", "two", "three" ] )' => [
				$test,
				[ "UNKNOWN", "one", "two", "three" ],
				NULL
			],
			'offsetGet( [ "nested", "one", "UNKNOWN", "three" ] )' => [
				$test,
				[ "nested", "one", "UNKNOWN", "three" ],
				NULL
			],
			'offsetGet( [ "nested", "one", "two", "UNKNOWN" ] )' => [
				$test,
				[ "nested", "one", "two", "UNKNOWN" ],
				NULL
			]
		];

	} // provideOffsetGetParameters.

	/**
	 * Provide offsetSet() test parameters.
	 *
	 * $1: Object.
	 * $2: Key.
	 * $3: Value.
	 * $4: Expected object contents.
	 */
	public function provideOffsetSetParameters()
	{
		//
		// Init test object.
		//
		$test = new test_Container();

		return [
			'offsetSet( 0, 1 )' => [
				$test,
				0,
				1,
				new test_Container([ 1 ])
			],
			'offsetSet( "uno", 1 )' => [
				$test,
				"uno",
				1,
				new test_Container([ 0 => 1, "uno" => 1 ])
			],
			'offsetSet( NULL, "APPENDED" )' => [
				$test,
				NULL,
				"APPENDED",
				new test_Container([ 0 => 1, "uno" => 1, 1 => "APPENDED" ])
			],
			'offsetSet( 1, "CHANGED" )' => [
				$test,
				1,
				"CHANGED",
				new test_Container([ 0 => 1, "uno" => 1, 1 => "CHANGED" ])
			],
			'offsetSet( 1, NULL )' => [
				$test,
				1,
				NULL,
				new test_Container([ 0 => 1, "uno" => 1 ])
			],
			'offsetSet( NULL, new ArrayObject([ "array" => [ 1, 2, 3 ] ]) )' => [
				$test,
				NULL,
				new ArrayObject([ "array" => [ 1, 2, 3 ] ]),
				new test_Container([
					0 => 1,
					"uno" => 1,
					2 => new ArrayObject([
						"array" => [ 1, 2, 3 ]
					])
				])
			],
			'offsetSet( [ 2, "nested" ], new test_Container([ "object" => new ArrayObject([ "array" => [ "uno", "due" ] ]) )' => [
				$test,
				[ 2, "nested" ],
				new test_Container([ "object" => new ArrayObject([ "array" => [ "uno", "due" ] ]) ]),
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
				])
			],
			'offsetSet( [ 2, "inserted", "array", "string" ], "a string" )' => [
				$test,
				[ 2, "inserted", "array", "string" ],
				"a string",
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
				])
			],
			'offsetSet( [ 2, "array", "number" ], 32 )' => [
				$test,
				[ 2, "array", "number" ],
				32,
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
				])
			],
			'offsetSet( [ 2, "nested", "object", "array", NULL ], "tre" )' => [
				$test,
				[ 2, "nested", "object", "array", NULL ],
				"tre",
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
				])
			],
			'offsetSet( [ 2, "nested", NULL, "array", NULL ], "Appended?" )' => [
				$test,
				[ 2, "nested", NULL, "object", NULL ],
				"Appended?",
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
				])
			],
			'offsetSet( [ 2, "inserted", "array", "string" ], NULL )' => [
				$test,
				[ 2, "inserted", "array", "string" ],
				NULL,
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
				])
			],
			'offsetSet( [ 2, "nested", 0, "object", 0 ], NULL )' => [
				$test,
				[ 2, "nested", 0, "object", 0 ],
				NULL,
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
				])
			],
		];

	} // provideOffsetSetParameters.

	/**
	 * Provide offsetUnset() test parameters.
	 *
	 * $1: Object.
	 * $2: Parameter.
	 * $3: Expected object.
	 */
	public function provideOffsetUnsetParameters()
	{
		//
		// Init test object.
		//
		$test = new test_Container([
			"uno",
			"due",
			"tre",
			"array" => [
				1,
				2,
				3,
				"obj" => new ArrayObject([
					3,
					4,
					5,
					"obj" => new test_Container([
						9,
						8,
						7
					])
				])
			],
			"nested" => [
				"one" => new ArrayObject([
					"two" => [
						"three" => 3
					]
				])
			]
		]);

//		print_r( $test );
//		exit;

		return [
			'offsetUnset( NULL )' => [
				$test,
				NULL,
				new test_Container([
					"uno",
					"due",
					"tre",
					"array" => [
						1,
						2,
						3,
						"obj" => new ArrayObject([
							3,
							4,
							5,
							"obj" => new test_Container([
								9,
								8,
								7
							])
						])
					],
					"nested" => [
						"one" => new ArrayObject([
							"two" => [
								"three" => 3
							]
						])
					]
				])
			],
			'offsetUnset( "UNKNOWN" )' => [
				$test,
				"UNKNOWN",
				new test_Container([
					"uno",
					"due",
					"tre",
					"array" => [
						1,
						2,
						3,
						"obj" => new ArrayObject([
							3,
							4,
							5,
							"obj" => new test_Container([
								9,
								8,
								7
							])
						])
					],
					"nested" => [
						"one" => new ArrayObject([
							"two" => [
								"three" => 3
							]
						])
					]
				])
			],
			'offsetUnset( 0 )' => [
				$test,
				0,
				new test_Container([
					1 => "due",
					2 => "tre",
					"array" => [
						1,
						2,
						3,
						"obj" => new ArrayObject([
							3,
							4,
							5,
							"obj" => new test_Container([
								9,
								8,
								7
							])
						])
					],
					"nested" => [
						"one" => new ArrayObject([
							"two" => [
								"three" => 3
							]
						])
					]
				])
			],
			'offsetUnset( [ "array", "obj", "obj" ] )' => [
				$test,
				[ "array", "obj", "obj" ],
				$temp = new test_Container([
					1 => "due",
					2 => "tre",
					"array" => [
						1,
						2,
						3,
						"obj" => new ArrayObject([
							3,
							4,
							5
						])
					],
					"nested" => [
						"one" => new ArrayObject([
							"two" => [
								"three" => 3
							]
						])
					]
				])
			],
			'offsetUnset( [ "array", "obj", "obj" ] )' => [
				$test,
				[ "array", "obj", "obj" ],
				$temp
			],
			'offsetUnset( [ "UNKNOWN", "obj", "obj" ] )' => [
				$test,
				[ "UNKNOWN", "obj", "obj" ],
				$temp
			],
			'offsetUnset( [ "array", "UNKNOWN" ] )' => [
				$test,
				[ "array", "UNKNOWN" ],
				$temp
			],
			'offsetUnset( [ "nested", "one", "two", "three" ] )' => [
				$test,
				[ "nested", "one", "two", "three" ],
				$temp = new test_Container([
					1 => "due",
					2 => "tre",
					"array" => [
						1,
						2,
						3,
						"obj" => new ArrayObject([
							3,
							4,
							5
						])
					]
				])
			],
		];

	} // provideOffsetUnsetParameters.

	/**
	 * Provide IsArray() test parameters.
	 *
	 * $1: Parameter.
	 * $2: Expected.
	 */
	public function provideIsArrayParameters()
	{
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

	} // provideIsArrayParameters.

	/**
	 * @covers       Container::__construct()
	 * @uses		 Container
	 * @dataProvider provideConstructorParameters
	 */
	public function testConstructor( $param1, $param2, $expected )
	{
		//
		// Tests.
		//
		$object = new Container( $param1, $param2 );
		$this->assertEquals( $object->getArrayCopy(), $expected );

	} // testConstructor.

	/**
	 * @covers       Container::__construct() exceptions
	 * @uses		 Container
	 * @dataProvider provideInvalidConstructorParameters
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructorExceptions( $param1, $param2 )
	{
		//
		// Tests.
		//
		$object = new Container( $param1, $param2 );

	} // testConstructorExceptions.

	/**
	 * @covers       Container::offsetExists()
	 * @dataProvider provideOffsetExistsParameters
	 * @uses		 Container
	 */
	public function testOffsetExists( test_Container $test, $param, $expected )
	{
		$result = $test->offsetExists( $param );
		if( $expected )
			$this->assertTrue( $result, $expected );
		else
			$this->assertFalse( $result, $expected );

	} // testOffsetExists.

	/**
	 * @covers       Container::offsetGet()
	 * @dataProvider provideOffsetGetParameters
	 * @uses		 Container
	 */
	public function testOffsetGet( test_Container $test, $param, $expected )
	{
		$this->assertEquals( $expected, $test->offsetGet( $param ) );

	} // testOffsetGet.

	/**
	 * @covers       Container::offsetSet()
	 * @dataProvider provideOffsetSetParameters
	 * @uses		 Container
	 */
	public function testOffsetSet( test_Container $test, $key, $value, $source )
	{
		$test->offsetSet( $key, $value );

//		echo( "\n" );
//		print_r( $test );

		$this->assertEquals( $source, $test );

	} // testOffsetGet.

	/**
	 * @covers       Container::offsetUnset()
	 * @dataProvider provideOffsetUnsetParameters
	 * @uses		 Container
	 */
	public function testOffsetUnset( test_Container $test, $param, $source )
	{
		$test->offsetUnset( $param );

//		echo( "\n" );
//		print_r( $test );

		$this->assertEquals( $source, $test );

	} // testOffsetUnset.

	/**
	 * @covers       Container::getIterator()
	 * @uses		 Container
	 */
	public function testGetIterator()
	{
		//
		// Instantiate object.
		//
		$array = [ 1 => "one", 2 => "due", "tres" => 3 ];
		$test = new test_Container( $array );

//		echo( "\n" );
//		print_r( $test );

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

	/**
	 * @covers       Container::count()
	 * @uses		 Container
	 */
	public function testCount()
	{
		$test = new test_Container();
		$this->assertEquals( $test->count(), 0, "count test_Container()" );

		$test = new test_Container([ 1, 2, 3 ]);
		$this->assertEquals( $test->count(), 3, "count test_Container([ 1, 2, 3 ])" );

	} // testCount.

	/**
	 * @covers       Array functions
	 * @uses		 Container
	 */
	public function testArrayFunctions()
	{
		$message = 'getArrayCopy() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test = new test_Container([ "uno" => 1, "due" => 2, 3 => "tre" ]);
		$result = $test->getArrayCopy();
		$this->assertEquals( $result, [ "uno" => 1, "due" => 2, 3 => "tre" ], $message );

		$message = 'array_keys() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$result = $test->array_keys();
		$this->assertEquals( $result, [ "uno", "due", 3 ], $message );

		$message = 'array_values() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$result = $test->array_values();
		$this->assertEquals( $result, [ 1, 2, "tre" ], $message );

		$message = 'asort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->asort();
		$this->assertEquals( $test->array_keys(), [ 3, "uno", "due" ], $message );

		$message = 'ksort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->ksort();
		$this->assertEquals( $test->array_keys(), [ "due", "uno", 3 ], $message );

		$message = 'krsort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->krsort();
		$this->assertEquals( $test->array_keys(), [ 3, "uno", "due" ], $message );

		$message = 'natcasesort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->natcasesort();
		$this->assertEquals( $test->array_keys(), [ "uno", "due", 3 ], $message );

		$message = 'natsort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->natsort();
		$this->assertEquals( $test->array_keys(), [ "uno", "due", 3 ], $message );

		$message = 'arsort() [ "uno" => 1, "due" => 2, 3 => "tre" ]';
		$test->arsort();
		$this->assertEquals( $test->array_keys(), [ "due", "uno", 3 ], $message );

		$message = 'array_push( "A", "B" )';
		$test->array_push( "A", "B" );
		$this->assertEquals( $test->getArrayCopy(), [ "due" => 2, "uno" => 1, 3 => "tre", 4 => [ "A", "B" ] ], $message );

		$message = 'array_pop()';
		$result = $test->array_pop();
		$this->assertEquals( $result, [ 0 => "A", 1 => "B" ], $message );
		$this->assertEquals( $test->getArrayCopy(), [ "due" => 2, "uno" => 1, 3 => "tre" ], $message );

		$message = 'array_unshift( "A", "B" )';
		$test->array_unshift( "A", "B" );
		$this->assertEquals( $test->getArrayCopy(), [ 0 => [ "A", "B" ], "due" => 2, "uno" => 1, 1 => "tre" ], $message );

		$message = 'array_shift()';
		$result = $test->array_shift();
		$this->assertEquals( $result, [ 0 => "A", 1 => "B" ], $message );
		$this->assertEquals( $test->getArrayCopy(), [ "due" => 2, "uno" => 1, 0 => "tre" ], $message );

	} // testArrayFunctions.

	/**
	 * @covers       Container::propertyReference()
	 * @uses		 Container
	 */
	public function testPropertyReference()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container([
			0 => "uno",
			2 => "tre",
			"array" => [
				0 => 1,
				1 => 2,
				2 => 3,
				"obj" => new ArrayObject([
					0 => 3,
					1 => 4,
					2 => 5,
					"obj" => new test_Container([
						0 => 9,
						2 => 7
					]),
					3 => [ "ADD" => "ADDED NESTED" ]
				])
			],
			"nested" => [
				"one" => new ArrayObject([
					"two" => [ "three" => 3]
				])
			],
			3 => "ADDED"
		]);

//		print_r( $test );
//		exit;

		$message = '$test->propertyReference()';
		$result = & $test->propertyReference();
		$this->assertTrue( $test->getArrayCopy() === $result, $message );

		$message = '$test->propertyReference( NULL )';
		$result = & $test->propertyReference( NULL );
		$this->assertTrue( $test->getArrayCopy() === $result, $message );

		$message = '$test->propertyReference( [] )';
		$result = & $test->propertyReference( [] );
		$this->assertTrue( $test->getArrayCopy() === $result, $message );

		$message = '$result = & $test->propertyReference( 0 ); $result = 3;';
		$result = & $test->propertyReference( 0 );
		$this->assertEquals( $result, "uno", $message );
		$result = 3;
		$this->assertEquals( $result, 3, $message );
		$this->assertEquals( $test[ 0 ], 3, $message );

		$message = '$result = & $test->propertyReference( [ "array", "obj", "obj", 0 ] ); $result = "X";';
		$result = & $test->propertyReference( [ "array", "obj", "obj", 0 ] );
		$this->assertEquals( $result, 9, $message );
		$result = "X";
		$this->assertEquals( $result, "X", $message );
		$this->assertEquals( $test[ [ "array", "obj", "obj", 0 ] ], "X", $message );

		unset( $result );

	} // testPropertyReference.

	/**
	 * @covers       Container::propertySchema()
	 * @uses		 Container
	 */
	public function testPropertySchema()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container([
			0 => "uno",
			2 => "tre",
			"array" => [
				0 => 1,
				1 => 2,
				2 => 3,
				"obj" => new ArrayObject([
					0 => 3,
					1 => 4,
					2 => 5,
					"obj" => new test_Container([
						0 => 9,
						2 => 7
					]),
					3 => [ "ADD" => "ADDED NESTED" ]
				])
			],
			"nested" => [
				"one" => new ArrayObject([
					"two" => [ "three" => 3]
				])
			],
			3 => "ADDED"
		]);

//		print_r( $test );
//		exit;

		$message = '$test->propertySchema()';
		$result = $test->propertySchema();
//		print_r( $result );
		$this->assertEquals(
			$result,
			[
				0 => [
					[ 0 ],
					[ "array", 0 ],
					[ "array", "obj", 0 ],
					[ "array", "obj", "obj", 0 ]
				],
				"ADD" => [
					[ "array", "obj", 3, "ADD" ]
				],
				"three" => [
					[ "nested", "one", "two", "three" ]
				],
				1 => [
					[ "array", 1 ],
					[ "array", "obj", 1 ]
				],
				2 => [
					[ 2 ],
					[ "array", 2 ],
					[ "array", "obj", 2 ],
					[ "array", "obj", "obj", 2 ]
				],
				3 => [
					[ 3 ]
				]
			],
			$message
		);

		$message = '$test->propertySchema( '.' )';
		$result = $test->propertySchema( '.' );
//		print_r( $result );
		$this->assertEquals(
			$result,
			[
				0 => [
					"0",
					"array.0",
					"array.obj.0",
					"array.obj.obj.0"
				],
				"ADD" => [
					"array.obj.3.ADD"
				],
				"three" => [
					"nested.one.two.three"
				],
				1 => [
					"array.1",
					"array.obj.1"
				],
				2 => [
					"2",
					"array.2",
					"array.obj.2",
					"array.obj.obj.2"
				],
				3 => [
					"3"
				]
			],
			$message
		);

	} // testPropertySchema.

	/**
	 * @covers       Container::asArray()
	 * @uses		 Container
	 */
	public function testAsArray()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container([
			0 => "uno",
			2 => "tre",
			"array" => [
				0 => 1,
				1 => 2,
				2 => 3,
				"obj" => new ArrayObject([
					0 => 3,
					1 => 4,
					2 => 5,
					"obj" => new test_Container([
						0 => 9,
						2 => 7
					]),
					3 => [ "ADD" => "ADDED NESTED" ]
				])
			],
			"nested" => [
				"one" => new ArrayObject([
					"two" => [ "three" => 3]
				])
			],
			3 => "ADDED"
		]);

//		print_r( $test );
//		exit;

		$message = '$result = $test->asArray()';
		$result = $test->asArray();
//		print_r( $result );
		$this->assertEquals(
			$result,
			[
				0 => "uno",
				2 => "tre",
				"array" => [
					0 => 1,
					1 => 2,
					2 => 3,
					"obj" => [
						0 => 3,
						1 => 4,
						2 => 5,
						"obj" => [
							0 => 9,
							2 => 7
						],
						3 => [ "ADD" => "ADDED NESTED" ]
					]
				],
				"nested" => [
					"one" => [
						"two" => [ "three" => 3]
					]
				],
				3 => "ADDED"
			],
			$message
		);
		$this->assertEquals(
			$test,
			new test_Container([
				0 => "uno",
				2 => "tre",
				"array" => [
					0 => 1,
					1 => 2,
					2 => 3,
					"obj" => new ArrayObject([
						0 => 3,
						1 => 4,
						2 => 5,
						"obj" => new test_Container([
							0 => 9,
							2 => 7
						]),
						3 => [ "ADD" => "ADDED NESTED" ]
					])
				],
				"nested" => [
					"one" => new ArrayObject([
						"two" => [ "three" => 3]
					])
				],
				3 => "ADDED"
			]),
			$message
		);

	} // testAsArray.

	/**
	 * @covers       Container::toArray()
	 * @uses		 Container
	 */
	public function testToArray()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container([
			0 => "uno",
			2 => "tre",
			"array" => [
				0 => 1,
				1 => 2,
				2 => 3,
				"obj" => new ArrayObject([
					0 => 3,
					1 => 4,
					2 => 5,
					"obj" => new test_Container([
						0 => 9,
						2 => 7
					]),
					3 => [ "ADD" => "ADDED NESTED" ]
				])
			],
			"nested" => [
				"one" => new ArrayObject([
					"two" => [ "three" => 3]
				])
			],
			3 => "ADDED"
		]);

//		print_r( $test );
//		exit;

		$message = '$test->toArray()';
		$test->toArray();
//		print_r( $result );
		$this->assertEquals(
			$test->getArrayCopy(),
			[
				0 => "uno",
				2 => "tre",
				"array" => [
					0 => 1,
					1 => 2,
					2 => 3,
					"obj" => [
						0 => 3,
						1 => 4,
						2 => 5,
						"obj" => [
							0 => 9,
							2 => 7
						],
						3 => [ "ADD" => "ADDED NESTED" ]
					]
				],
				"nested" => [
					"one" => [
						"two" => [ "three" => 3]
					]
				],
				3 => "ADDED"
			],
			$message
		);

	} // testToArray.

	/**
	 * @covers       Container::ConvertToArray()
	 * @uses		 Container
	 */
	public function testConvertToArray()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container([
			"ArrayObject" => new ArrayObject([
				"array" => [
					"container" => new Container([
						"string" => "a string"
					])
				]
			])
		]);

//		print_r( $test );
//		exit;

		$message = 'Container::ConvertToArray( $test )';
		test_Container::ConvertToArray( $test );
//		print_r( $test );
		$this->assertEquals(
			$test,
			[
				"ArrayObject" => [
					"array" => [
						"container" => [
							"string" => "a string"
						]
					]
				]
			],
			$message
		);

	} // testConvertToArray.

	/**
	 * @covers       Container::IsArray()
	 * @dataProvider provideIsArrayParameters
	 * @uses		 Container
	 */
	public function testIsArray( $param, $expected )
	{
		$result = Container::IsArray( $param );
		if( $expected )
			$this->assertTrue( $result, $expected );
		else
			$this->assertFalse( $result, $expected );

	} // testIsArray.

	/**
	 * @covers       Container::manageAttribute()
	 * @uses		 Container
	 */
	public function testManageAttribute()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container();

		$result = $test->Attribute( "NEW" );
		$message = '$result = $test->Attribute( "NEW" )';
		$this->assertEquals( $result, "NEW", $message );
		$message = '$test->attribute == "NEW';
		$this->assertEquals( $test->attribute, "NEW", $message );

		$result = $test->Attribute( "OTHER", TRUE );
		$message = '$result = $test->Attribute( "OTHER", TRUE )';
		$this->assertEquals( $result, "NEW", $message );
		$message = '$test->attribute == "OTHER';
		$this->assertEquals( $test->attribute, "OTHER", $message );

		$result = $test->Attribute();
		$message = '$result = $test->Attribute()';
		$this->assertEquals( $result, "OTHER", $message );

		$result = $test->Attribute( FALSE, TRUE );
		$message = '$result = $test->Attribute( FALSE, TRUE )';
		$this->assertEquals( $result, "OTHER", $message );
		$message = '$test->attribute === NULL';
		$this->assertNull( $test->attribute, $message );

	} // testManageAttribute.

	/**
	 * @covers       Container::manageFlagAttribute()
	 * @uses		 Container
	 */
	public function testManageFlagAttribute()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container();

		$message = '$result = $test->BitfieldAttribute()';
		$result = $test->BitfieldAttribute();
		$this->assertEquals( bin2hex($result), "00000000", $message );
		$this->assertEquals( $result, $test->flag, $message );

		$message = '$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE );
		$this->assertEquals( bin2hex($result), "ff000000", $message );
		$this->assertEquals( $result, $test->flag, $message );

		$message = '$result = $test->BitfieldAttribute( hex2bin("ff0f" ), TRUE, TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("ff0f" ), TRUE, TRUE );
		$this->assertEquals( bin2hex($result), "ff000000", $message );
		$this->assertEquals( bin2hex($test->flag), "ff0f0000", $message );

		$message = '$result = $test->BitfieldAttribute( hex2bin("f0000000" ) )';
		$result = $test->BitfieldAttribute( hex2bin("f0000000" ) );
		$this->assertTrue( $result, $message );

		$message = '$result = $test->BitfieldAttribute( hex2bin("0f" ) )';
		$result = $test->BitfieldAttribute( hex2bin("0f" ) );
		$this->assertTrue( $result, $message );

		$message = '$result = $test->BitfieldAttribute( hex2bin("000000ff" ) )';
		$result = $test->BitfieldAttribute( hex2bin("000000ff" ) );
		$this->assertFalse( $result, $message );

		$message = '$result = $test->BitfieldAttribute( hex2bin("00f0" ) )';
		$result = $test->BitfieldAttribute( hex2bin("00f0" ) );
		$this->assertFalse( $result, $message );

		$message = '$result = $test->BitfieldAttribute( hex2bin("f0f00000" ), FALSE, TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("f0f00000" ), FALSE, TRUE );
		$this->assertEquals( bin2hex($result), "ff0f0000", $message );
		$this->assertEquals( bin2hex($test->flag), "0f0f0000", $message );

		$message = '$result = $test->BitfieldAttribute( hex2bin("0ff0" ), FALSE, TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("0ff0" ), FALSE, TRUE );
		$this->assertEquals( bin2hex($result), "0f0f0000", $message );
		$this->assertEquals( bin2hex($test->flag), "000f", $message );

		$message = '$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE, TRUE )';
		$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE, TRUE );
		$this->assertEquals( bin2hex($result), "000f", $message );
		$this->assertEquals( bin2hex($test->flag), "ff0f0000", $message );

	} // testManageFlagAttribute.

	/**
	 * @covers       Container::manageProperty()
	 * @uses		 Container
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

//		print_r( $test );
//		exit;

		$message = '$result = $test->Property( "prop", "NEW" )';
		$result = $test->Property( "prop", "NEW" );
		$this->assertEquals( $result, "NEW", $message );
		$this->assertEquals( $test[ "prop" ], "NEW", $message );

		$message = '$result = $test->Property( "prop", "OTHER", TRUE )';
		$result = $test->Property( "prop", "OTHER", TRUE );
		$this->assertEquals( $result, "NEW", $message );
		$this->assertEquals( $test[ "prop" ], "OTHER", $message );

		$message = '$result = $test->Property( "prop" )';
		$result = $test->Property( "prop" );
		$this->assertEquals( $result, "OTHER", $message );
		$this->assertEquals( $test[ "prop" ], "OTHER", $message );

		$message = '$result = $test->Property( "prop", FALSE, TRUE )';
		$result = $test->Property( "prop", FALSE, TRUE );
		$this->assertEquals( $result, "OTHER", $message );
		$this->assertNull( $test[ "prop" ], $message );

		$result = $test->Property( [ "nested", "container", "array", "string" ] );
		$message = '$result = $test->Property( [ "nested", "container", "array", "string" ] )';
		$this->assertEquals( $result, "a string", $message );

		$message = '$result = $test->Property( [ "nested", "container", "UNKNOWN", "string" ] )';
		$result = $test->Property( [ "nested", "container", "UNKNOWN", "string" ] );
		$this->assertNull( $result, $message );

		$message = '$result = $test->Property( [ "nested", "container", "array", "string" ], "NEW STRING" )';
		$result = $test->Property( [ "nested", "container", "array", "string" ], "NEW STRING" );
		$this->assertEquals( $result, "NEW STRING", $message );
		$this->assertEquals( $test[ "nested" ][ "container" ][ "array" ][ "string" ], "NEW STRING", $message );

		$message = '$result = $test->Property( [ "nested", "container", "array", "string" ], "OTHER STRING", TRUE )';
		$result = $test->Property( [ "nested", "container", "array", "string" ], "OTHER STRING", TRUE );
		$this->assertEquals( $result, "NEW STRING", $message );
		$this->assertEquals( $test[ "nested" ][ "container" ][ "array" ][ "string" ], "OTHER STRING", $message );

		$message = '$result = $test->Property( [ "branch", "array", "array", "string" ], "INSERTED STRING", TRUE )';
		$result = $test->Property( [ "branch", "array", "array", "string" ], "INSERTED STRING", TRUE );
		$this->assertNull( $result, $message );
		$this->assertEquals( $test[ "branch" ][ "array" ][ "array" ][ "string" ], "INSERTED STRING", $message );

		$message = '$result = $test->Property( [ "nested", "container", "array", "string" ], FALSE, TRUE )';
		$result = $test->Property( [ "nested", "container", "array", "string" ], FALSE, TRUE );
		$this->assertEquals( $result, "OTHER STRING", $message );
		$this->assertFalse( $test->offsetExists( "nested" ), $message );

	} // testManageProperty.

	/**
	 * @covers       Container::manageFlagProperty()
	 * @uses		 Container
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
		$this->assertEquals( bin2hex($result), "00000000", $message );
		$this->assertEquals( $result, $test[ "flag" ], $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE )';
		$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE );
		$this->assertEquals( bin2hex($result), "ff000000", $message );
		$this->assertEquals( $result, $test[ "flag" ], $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("ff0f" ), TRUE, TRUE )';
		$result = $test->BitfieldProperty( "flag", hex2bin("ff0f" ), TRUE, TRUE );
		$this->assertEquals( bin2hex($result), "ff000000", $message );
		$this->assertEquals( bin2hex($test[ "flag" ]), "ff0f0000", $message );

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
		$this->assertEquals( bin2hex($result), "ff0f0000", $message );
		$this->assertEquals( bin2hex($test[ "flag" ]), "0f0f0000", $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("0ff0" ), FALSE, TRUE )';
		$result = $test->BitfieldProperty( "flag", hex2bin("0ff0" ), FALSE, TRUE );
		$this->assertEquals( bin2hex($result), "0f0f0000", $message );
		$this->assertEquals( bin2hex($test[ "flag" ]), "000f", $message );

		$message = '$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE, TRUE )';
		$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE, TRUE );
		$this->assertEquals( bin2hex($result), "000f", $message );
		$this->assertEquals( bin2hex($test[ "flag" ]), "ff0f0000", $message );

	} // testManageFlagProperty.

	/**
	 * @covers       Container::nestedPropertyReference()
	 * @uses		 Container
	 */
	public function testNestedPropertyReference()
	{
		//
		// Instantiate test object.
		//
		$test = new test_Container( [
			"nested" => new ArrayObject( [
				"container" => new Container( [
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

//		print_r( $test );
//		exit;

		$message = '$result = & $test->NestedProperty( [ 1 ] );';
		$offsets = [ 1 ];
		$result = &$test->NestedProperty( $offsets );
		$this->assertEquals( $result, "uno", $message );
		$this->assertEquals( $result, $test[ 1 ], $message );
		$this->assertCount( 0, $offsets, $message );

		$message = '$result = $test->NestedProperty( [ "nested", "container", "array", "string" ] );';
		$offsets = [ "nested", "container", "array", "string" ];
		$result = $test->NestedProperty( $offsets );
		$this->assertEquals( $result, "a string", $message );
		$this->assertEquals( $result, $test[ "nested" ][ "container" ][ "array" ][ "string" ], $message );
		$this->assertCount( 0, $offsets, $message );

		$message = '$result = $test->NestedProperty( [ "nested", "container", "array", "UNKNOWN" ] );';
		$offsets = [ "nested", "container", "array", "UNKNOWN" ];
		$result = $test->NestedProperty( $offsets );
		$this->assertEquals( $result, [ "string" => "a string" ], $message );
		$this->assertEquals( $result, $test[ "nested" ][ "container" ][ "array" ], $message );
		$this->assertEquals( $offsets, [ "UNKNOWN" ], $message );

		$message = '$result = $test->NestedProperty( [ "UNKNOWN" ] );';
		$offsets = [ "UNKNOWN" ];
		$result = $test->NestedProperty( $offsets );
		$this->assertEquals( $result, $test->propertyReference(), $message );
		$this->assertEquals( $offsets, [ "UNKNOWN" ], $message );

	} // testNestedPropertyReference.
}
