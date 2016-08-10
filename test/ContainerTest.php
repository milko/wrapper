<?php

/**
 * Created by PhpStorm.
 * User: milko
 * Date: 11/08/16
 * Time: 00:11
 */

use Milko\wrapper\Container;

class ContainerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test instantiating object.
	 */
	public function testConstructor()
	{
		//
		// Empty object.
		//
		$object = new Container();
		$this->assertEquals(
			$object->getArrayCopy(),
			[],
			"Construct empty object"
		);

		$object = new Container([]);
		$this->assertEquals(
			$object->getArrayCopy(),
			[],
			"Construct empty object from array"
		);

		$object = new Container(new ArrayObject([]));
		$this->assertEquals(
			$object->getArrayCopy(),
			[],
			"Construct empty object from ArrayObject"
		);

		$object = new Container(new Container([]));
		$this->assertEquals(
			$object->getArrayCopy(),
			[],
			"Construct empty object from Container"
		);

		//
		// From array.
		//
		$object = new Container([
			1, 2, 3
		]);
		$this->assertEquals(
			$object->getArrayCopy(),
			[ 1, 2, 3 ],
			"Construct from array"
		);
		$object = new Container([
			"uno" => new ArrayObject([
				1, 2, 3
			]),
			"due" => 2
		]);
		$this->assertEquals(
			$object->getArrayCopy(),
			[
				"uno" => new ArrayObject([
					1, 2, 3
				]),
				"due" => 2
			],
			"Construct from array"
		);

		//
		// From ArrayObject.
		//
		$object = new Container(
			new ArrayObject([ 1, 2, 3 ])
		);
		$this->assertEquals(
			$object->getArrayCopy(),
			[ 1, 2, 3 ],
			"Construct from ArrayObject"
		);

		//
		// From Container.
		//
		$object = new Container(
			new Container([ 1, 2, 3 ])
		);
		$this->assertEquals(
			$object->getArrayCopy(),
			[ 1, 2, 3 ],
			"Construct from Container"
		);
	}

	/**
	 * Test offsetExists().
	 */
	public function testOffsetExists()
	{
		//
		// Instantiate object.
		//
		$test = new Container(
			[
				"uno",
				"due",
				"tre",
				"array" => [
					1, 2, 3, "obj" => new ArrayObject([
						3, 4, 5, "obj" => new Container([
							9, 8, 7 ]) ]) ],
				"nested" => [
					"one" => new ArrayObject([
						"two" => [ "three" => 3 ] ]) ]
			]
		);

		//
		// Test.
		//
		$this->assertTrue( $test->offsetExists( 1 ), '$test->offsetExists( 1 );' );
		$this->assertFalse( $test->offsetExists( NULL ), '$test->offsetExists( NULL );' );
		$this->assertFalse( $test->offsetExists( "UNKNOWN" ), '$test->offsetExists( "UNKNOWN" );' );

		//
		// Test nested.
		//
		$this->assertTrue( $test->offsetExists( [ "array", "obj", "obj", 1 ] ), '$test->offsetExists( [ "array", "obj", "obj", 1 ] );' );
		$this->assertFalse( $test->offsetExists( [ "array", "obj", NULL, 1 ] ), '$test->offsetExists( [ "array", "obj", NULL, 1 ] );' );
		$this->assertFalse( $test->offsetExists( [ "array", "obj", "UNKNOWN", 1 ] ), '$test->offsetExists( [ "array", "obj", "UNKNOWN", 1 ] );' );

		return $test;
	}

	/**
	 * Test offsetGet().
	 *
	 * @depends testOffsetExists
	 */
	public function testOffsetGet( Container $test)
	{
		//
		// Test.
		//
		$this->assertTrue( $test->offsetExists( 1 ), '$test->offsetExists( 1 );' );
		$this->assertFalse( $test->offsetExists( NULL ), '$test->offsetExists( NULL );' );
		$this->assertFalse( $test->offsetExists( "UNKNOWN" ), '$test->offsetExists( "UNKNOWN" );' );

		//
		// Test nested.
		//
		$this->assertTrue( $test->offsetExists( [ "array", "obj", "obj", 1 ] ), '$test->offsetExists( [ "array", "obj", "obj", 1 ] );' );
		$this->assertFalse( $test->offsetExists( [ "array", "obj", NULL, 1 ] ), '$test->offsetExists( [ "array", "obj", NULL, 1 ] );' );
		$this->assertFalse( $test->offsetExists( [ "array", "obj", "UNKNOWN", 1 ] ), '$test->offsetExists( [ "array", "obj", "UNKNOWN", 1 ] );' );
	}
}
