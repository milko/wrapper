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

/**
 * Include test class.
 */
require_once(dirname(__DIR__) . "/TestUrlClass.php");

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
	 *	provideConstructorExceptions													*
	 *==================================================================================*/

	/**
	 * Provide data to URL() test.
	 *
	 * The data elements are:
	 *
	 * <ul>
	 * 	<li><tt>$1</tt>: Data parameter.
	 * </ul>
	 */
	public function provideConstructorExceptions()
	{
		//
		// Return test data.
		//
		return [
			[
				''
			],
			[
				'prot://host:port'
			],
			[
				'prot://host:port?=val'
			],
			[
				'prot://host:port?=val#uno#due'
			]
		];

	} // provideConstructorExceptions.


	/*===================================================================================
	 *	providePortExceptions															*
	 *==================================================================================*/

	/**
	 * Provide data to Port() test.
	 *
	 * The data elements are:
	 *
	 * <ul>
	 * 	<li><tt>$1</tt>: Data parameter.
	 * </ul>
	 */
	public function providePortExceptions()
	{
		//
		// Return test data.
		//
		return [
			[
				"port"
			],
			[
				[ "port" ]
			],
			[
				[ 8080, "" ]
			]
		];

	} // providePortExceptions.



/*=======================================================================================
 *																						*
 *								PUBLIC TEST INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	testConstructor																	*
	 *==================================================================================*/

	/**
	 * Test URL()
	 *
	 * @covers       Url::URL()
	 */
	public function testConstructor()
	{
		//
		// Make tests.
		//
		$message = "new Url()";
		$test = new test_Url();
		$this->assertNull( $test->Protocol(), $message );
		$this->assertNull( $test->Host(), $message );
		$this->assertNull( $test->Port(), $message );
		$this->assertNull( $test->User(), $message );
		$this->assertNull( $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( '', $test->URL(), $message );

		$parameter = 'prot://host';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertNull( $test->Port(), $message );
		$this->assertNull( $test->User(), $message );
		$this->assertNull( $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://host:8080';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertNull( $test->User(), $message );
		$this->assertNull( $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://user@host:8080';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertNull( $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://user:pass@host:8080';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://user:pass@host:8080/dir';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir', $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://user:pass@host:8080/dir/file';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://user:pass@host:8080/dir/file?key=val';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[ 'key' => 'val' ],
			$test->Options(),
			$message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://user:pass@host:8080/dir/file?key=val&arg=val&uni';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[
				"key" => "val",
				"arg" => "val",
				"uni" => NULL
			],
			$test->Options(),
			$message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://user:pass@host:8080/dir/file?key=val&arg=val&uni#frag';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[
				"key" => "val",
				"arg" => "val",
				"uni" => NULL
			],
			$test->Options(),
			$message );
		$this->assertEquals( 'frag', $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://user:pass@host1:8080,host2:9090/dir/file?key=val&arg=val&uni#frag';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( [ "host1", "host2" ], $test->Host(), $message );
		$this->assertEquals( [ 8080, 9090 ], $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[
				"key" => "val",
				"arg" => "val",
				"uni" => NULL
			],
			$test->Options(),
			$message );
		$this->assertEquals( 'frag', $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

		$parameter = 'prot://user:pass@host1:8080,host2:8181,host3:8282/dir/file?key=val&arg=val&uni#frag';
		$message = "new Url( '$parameter' )";
		$test = new test_Url( $parameter );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( [ "host1", "host2", "host3" ], $test->Host(), $message );
		$this->assertEquals( [ 8080, 8181, 8282 ], $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[
				"key" => "val",
				"arg" => "val",
				"uni" => NULL
			],
			$test->Options(),
			$message );
		$this->assertEquals( 'frag', $test->Fragment(), $message );
		$this->assertEquals( $parameter, $test->URL(), $message );

	} // testConstructor.


	/*===================================================================================
	 *	testConstructorAttributes														*
	 *==================================================================================*/

	/**
	 * Test URL()
	 *
	 * @covers       Url::URL()
	 * @covers       Url::Protocol()
	 * @covers       Url::Host()
	 * @covers       Url::Port()
	 * @covers       Url::User()
	 * @covers       Url::Password()
	 * @covers       Url::Path()
	 * @covers       Url::Options()
	 * @covers       Url::Fragment()
	 */
	public function testConstructorAttributes()
	{
		//
		// Make tests.
		//
		$url = "prot://";
		$parameter = 'prot';
		$message = "Protocol( $parameter )";
		$test = new test_Url();
		$test->Protocol( $parameter );
		$this->assertEquals( $parameter, $test->Protocol(), $message );
		$this->assertNull( $test->Host(), $message );
		$this->assertNull( $test->Port(), $message );
		$this->assertNull( $test->User(), $message );
		$this->assertNull( $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = "prot://host";
		$parameter = 'host';
		$message = "Host( '$parameter' )";
		$test = new test_Url();
		$test->Host( $parameter );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertNull( $test->Port(), $message );
		$this->assertNull( $test->User(), $message );
		$this->assertNull( $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = "prot://host:8080";
		$parameter = '8080';
		$message = "Port( '$parameter' )";
		$test = new test_Url();
		$test->Port( $parameter );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertNull( $test->User(), $message );
		$this->assertNull( $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user@host:8080';
		$parameter = 'user';
		$message = "new Url( '$parameter' )";
		$test = new test_Url();
		$test->User( $parameter );
		$test->Port( 8080 );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertNull( $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host:8080';
		$parameter = 'pass';
		$message = "Password( '$parameter' )";
		$test = new test_Url();
		$test->Password( $parameter );
		$test->User( 'user' );
		$test->Port( 8080 );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertNull( $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host:8080/dir';
		$parameter = '/dir';
		$message = "Path( '$parameter' )";
		$test = new test_Url();
		$test->Path( $parameter );
		$test->Password( 'pass' );
		$test->User( 'user' );
		$test->Port( 8080 );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir', $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host:8080/dir/file';
		$parameter = '/dir/file';
		$message = "Path( '$parameter' )";
		$test = new test_Url();
		$test->Path( $parameter );
		$test->Password( 'pass' );
		$test->User( 'user' );
		$test->Port( 8080 );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertNull( $test->Options(), $message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host:8080/dir/file?key=val';
		$parameter = 'key=val';
		$message = "Options( '$parameter' )";
		$test = new test_Url();
		$test->Options( $parameter );
		$test->Path( '/dir/file' );
		$test->Password( 'pass' );
		$test->User( 'user' );
		$test->Port( 8080 );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[ 'key' => 'val' ],
			$test->Options(),
			$message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host:8080/dir/file?key=val';
		$parameter = '[ "key" => "val" ]';
		$message = "Options( '$parameter' )";
		$test = new test_Url();
		$test->Options( [ "key" => "val" ] );
		$test->Path( '/dir/file' );
		$test->Password( 'pass' );
		$test->User( 'user' );
		$test->Port( 8080 );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[ 'key' => 'val' ],
			$test->Options(),
			$message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host:8080/dir/file?key=val&arg=val&uni';
		$parameter = 'key=val&arg=val&uni';
		$message = "Options( '$parameter' )";
		$test = new test_Url();
		$test->Options( $parameter );
		$test->Path( '/dir/file' );
		$test->Password( 'pass' );
		$test->User( 'user' );
		$test->Port( 8080 );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[
				"key" => "val",
				"arg" => "val",
				"uni" => NULL
			],
			$test->Options(),
			$message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host:8080/dir/file?key=val&arg=val&uni';
		$parameter = 'key=val&arg=val&uni';
		$message = "Options( [ \"key\" => \"val\", \"arg\" => \"val\", \"uni\" => NULL ] )";
		$test = new test_Url();
		$test->Options( [ "key" => "val", "arg" => "val", "uni" => NULL ] );
		$test->Path( '/dir/file' );
		$test->Password( 'pass' );
		$test->User( 'user' );
		$test->Port( 8080 );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[
				"key" => "val",
				"arg" => "val",
				"uni" => NULL
			],
			$test->Options(),
			$message );
		$this->assertNull( $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host:8080/dir/file?key=val&arg=val&uni#frag';
		$parameter = 'frag';
		$message = "new Url( '$parameter' )";
		$test = new test_Url();
		$test->Fragment( $parameter );
		$test->Options( 'key=val&arg=val&uni' );
		$test->Path( '/dir/file' );
		$test->Password( 'pass' );
		$test->User( 'user' );
		$test->Port( 8080 );
		$test->Host( 'host' );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( 'host', $test->Host(), $message );
		$this->assertEquals( 8080, $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[
				"key" => "val",
				"arg" => "val",
				"uni" => NULL
			],
			$test->Options(),
			$message );
		$this->assertEquals( 'frag', $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host1:8080,host2:9090/dir/file?key=val&arg=val&uni#frag';
		$parameter = '[ "host1", "host2" ]';
		$message = "Host( '$parameter' )";
		$test = new test_Url();
		$test->Fragment( 'frag' );
		$test->Options( 'key=val&arg=val&uni' );
		$test->Path( '/dir/file' );
		$test->Password( 'pass' );
		$test->User( 'user' );
		$test->Port( [ 8080, 9090 ] );
		$test->Host( [ "host1", "host2" ] );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( [ "host1", "host2" ], $test->Host(), $message );
		$this->assertEquals( [ 8080, 9090 ], $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[
				"key" => "val",
				"arg" => "val",
				"uni" => NULL
			],
			$test->Options(),
			$message );
		$this->assertEquals( 'frag', $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

		$url = 'prot://user:pass@host1:8080,host2:8181,host3:8282/dir/file?key=val&arg=val&uni#frag';
		$parameter = '[ "host1", "host2", "host3" ]';
		$message = "Host( '$parameter' )";
		$test = new test_Url();
		$test->Fragment( 'frag' );
		$test->Options( 'key=val&arg=val&uni' );
		$test->Path( '/dir/file' );
		$test->Password( 'pass' );
		$test->User( 'user' );
		$test->Port( [ 8080, 8181, 8282 ] );
		$test->Host( [ "host1", "host2", "host3" ] );
		$test->Protocol( 'prot' );
		$this->assertEquals( 'prot', $test->Protocol(), $message );
		$this->assertEquals( [ "host1", "host2", "host3" ], $test->Host(), $message );
		$this->assertEquals( [ 8080, 8181, 8282 ], $test->Port(), $message );
		$this->assertEquals( 'user', $test->User(), $message );
		$this->assertEquals( 'pass', $test->Password(), $message );
		$this->assertEquals( 'dir/file', $test->Path(), $message );
		$this->assertEquals(
			[
				"key" => "val",
				"arg" => "val",
				"uni" => NULL
			],
			$test->Options(),
			$message );
		$this->assertEquals( 'frag', $test->Fragment(), $message );
		$this->assertEquals( $url, $test->URL(), $message );

	} // testConstructorAttributes.


	/*===================================================================================
	 *	testConstructorExceptions														*
	 *==================================================================================*/

	/**
	 * Test URL() exceptions.
	 *
	 * @covers       Url::URL()
	 * @expectedException InvalidArgumentException
	 * @dataProvider provideConstructorExceptions
	 *
	 * @param $parameter
	 */
	public function testConstructorExceptions( $parameter )
	{
		//
		// Test exceptions.
		//
		$this->mObject->URL( $parameter );

	} // testConstructorExceptions.


	/*===================================================================================
	 *	testProtocol																	*
	 *==================================================================================*/

	/**
	 * Test Protocol() exceptions.
	 *
	 * @covers       Url::Protocol()
	 * @expectedException BadMethodCallException
	 */
	public function testProtocol( )
	{
		//
		// Test exceptions.
		//
		$this->mObject->Protocol( FALSE );

	} // testProtocol.


	/*===================================================================================
	 *	testHost																		*
	 *==================================================================================*/

	/**
	 * Test Host() exceptions.
	 *
	 * @covers       Url::Host()
	 * @expectedException BadMethodCallException
	 */
	public function testHost( )
	{
		//
		// Test exceptions.
		//
		$this->mObject->Host( FALSE );

	} // testHost.


	/*===================================================================================
	 *	testPort																		*
	 *==================================================================================*/

	/**
	 * Test Port() exceptions.
	 *
	 * @covers       Url::Port()
	 * @dataProvider providePortExceptions
	 * @expectedException InvalidArgumentException
	 */
	public function testPort( $parameter )
	{
		//
		// Test exceptions.
		//
		$this->mObject->Port( $parameter );

	} // testPort.


	/*===================================================================================
	 *	testUser																		*
	 *==================================================================================*/

	/**
	 * Test User() exceptions.
	 *
	 * @covers       Url::User()
	 */
	public function testUser( )
	{
		//
		// Test reset password.
		//
		$message = '$this->mObject->User( FALSE )';
		$this->mObject->User( FALSE );
		$this->assertNull( $this->mObject->Password(), $message );
		$this->assertEquals(
			'protocol://host:80/directory/file?key=val#frag',
			$this->mObject->URL(),
			$message );

	} // testUser.


	/*===================================================================================
	 *	testOptions																		*
	 *==================================================================================*/

	/**
	 * Test Options() exceptions.
	 *
	 * @covers       Url::Options()
	 */
	public function testOptions( )
	{
		//
		// Test query.
		//
		$message = '$this->mObject->Options( "key=val&uni" )';
		$this->mObject->Options( "key=val&uni" );
		$this->assertEquals(
			[ "key" => "val", "uni" => NULL ],
			$this->mObject->Options(),
			$message );

		$message = '$this->mObject->Options( [ "key" => "val", "uni" => NULL ] )';
		$this->mObject->Options( [ "key" => "val", "uni" => NULL ] );
		$this->assertEquals(
			[ "key" => "val", "uni" => NULL ],
			$this->mObject->Options(),
			$message );

	} // testOptions.


	/*===================================================================================
	 *	testURL																			*
	 *==================================================================================*/

	/**
	 * Test URL() exceptions.
	 *
	 * @covers       Url::URL()
	 */
	public function testURL( )
	{
		//
		// Test single URL exceptions.
		//
		$message = 'Exclude from URL [ PROT ]';
		$result = $this->mObject->URL( NULL, [ test_Url::kTAG_PROT ] );
		$this->assertSame(
			'user:password@host:80/directory/file?key=val#frag',
			$result,
			$message );

		$message = 'Exclude from URL [ HOST ]';
		$result = $this->mObject->URL( NULL, [ test_Url::kTAG_HOST ] );
		$this->assertSame(
			'protocol://user:password@/directory/file?key=val#frag',
			$result,
			$message );

		$message = 'Exclude from URL [ PORT ]';
		$result = $this->mObject->URL( NULL, [ test_Url::kTAG_PORT ] );
		$this->assertSame(
			'protocol://user:password@host/directory/file?key=val#frag',
			$result,
			$message );

		$message = 'Exclude from URL [ USER ]';
		$result = $this->mObject->URL( NULL, [ test_Url::kTAG_USER ] );
		$this->assertSame(
			'protocol://host:80/directory/file?key=val#frag',
			$result,
			$message );

		$message = 'Exclude from URL [ PASS ]';
		$result = $this->mObject->URL( NULL, [ test_Url::kTAG_PASS ] );
		$this->assertSame(
			'protocol://user@host:80/directory/file?key=val#frag',
			$result,
			$message );

		$message = 'Exclude from URL [ PATH ]';
		$result = $this->mObject->URL( NULL, [ test_Url::kTAG_PATH ] );
		$this->assertSame(
			'protocol://user:password@host:80?key=val#frag',
			$result,
			$message );

		$message = 'Exclude from URL [ OPTS ]';
		$result = $this->mObject->URL( NULL, [ test_Url::kTAG_OPTS ] );
		$this->assertSame(
			'protocol://user:password@host:80/directory/file#frag',
			$result,
			$message );

		$message = 'Exclude from URL [ FRAG ]';
		$result = $this->mObject->URL( NULL, [ test_Url::kTAG_FRAG ] );
		$this->assertSame(
			'protocol://user:password@host:80/directory/file?key=val',
			$result,
			$message );

		//
		// Test multiple URL exceptions.
		//
		$message = 'Exclude from URL [ PATH, OPTS, FRAG ]';
		$result = $this->mObject->URL(
			NULL,
			[
				test_Url::kTAG_PATH,
				test_Url::kTAG_OPTS,
				test_Url::kTAG_FRAG
			]
		);
		$this->assertSame(
			'protocol://user:password@host:80',
			$result,
			$message );

		$message = 'Exclude from URL [ USER, PATH, OPTS ]';
		$result = $this->mObject->URL(
			NULL,
			[
				test_Url::kTAG_USER,
				test_Url::kTAG_PATH,
				test_Url::kTAG_OPTS
			]
		);
		$this->assertSame(
			'protocol://host:80#frag',
			$result,
			$message );

	} // testURL.



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
		$this->mObject = new test_Url(
			'protocol://user:password@host:80/directory/file?key=val#frag'
		);

	} // testConstructor.




} // class UrlTest.


?>
