<?php

/**
 * Container.php
 *
 * This file contains the definition of the {@link Milko\wrapper\Container} class.
 */

namespace Milko\wrapper;

/*=======================================================================================
 *																						*
 *									Container.php	    								*
 *																						*
 *======================================================================================*/

/**
 * <h4>Container base object.</h4><p />
 *
 * This class is the ancestor of objects that act as document containers, the object
 * functions as <tt>ArrayObject</tt>, except that it implements an array data member which,
 * unlike <tt>ArrayObject</tt>, allows access by reference to its elements.
 *
 * The <em>attributes</em> of the object represent transient information which is private
 * to the object itself, this data is stored in the object's data members and is not
 * considered by the persistent framework of this library.
 *
 * The <em>properties</em> of the object represent the persistent information carried by
 * the object, this data is stored in the array data member and are accessed through the
 * various interfaces implemented by this class. The persistence framework of this library
 * uses this data.
 *
 * Properties cannot hold the <tt>NULL</tt> value, setting a property to that value will
 * result in that property being deleted.
 *
 * The class implements an interface that standardises the way attributes and properties
 * are managed:
 *
 * <ul>
 *  <li><em>Attributes</em>: a protected interface can be used to standardise the behaviour
 *      of member accessor methods, in general there should be a single public method for
 *      a specific attribute that will store, retrieve and delete attributes, depending on
 *      the provided value:
 *   <ul>
 *      <li><tt>NULL</tt>: Retrieve the attribute value.
 *      <li><tt>FALSE</tt>: Reset the attribute value to <tt>NULL</tt>.
 *      <li><em>other</em>: Any other type will result in the attribute being set to that
 *          value.
 *   </ul>
 *  <li><em>Properties</em>: a public interface will take care of implementing the standard
 *      behaviour, this to ensure no warnings are issued:
 *   <ul>
 *      <li>Setting a property to <tt>NULL</tt> will delete the property.
 *      <li>Retrieving a property that does not exist will return the <tt>NULL</tt> value.
 *      <li>Deleting a property that does not exist will do nothing.
 *   </ul>
 * </ul>
 *
 * The class implements the {@link \ArrayAccess}, {@link \IteratorAggregate} and
 * {@link \Countable} interfaces and implements a set of selected array functions as object
 * methods:
 *
 * <ul>
 * 	<li><em>Array key and value functions</em>:
 * 	 <ul>
 * 		<li>array <b>array_keys</b>():
 * 			Return all the property offsets at the top level.
 * 		<li>array <b>array_values</b>():
 * 			Return all the property values (equivalent to {@link getArrayCopy()}.
 * 	 </ul>
 * 	<li><em>Array sorting functions</em> (mProperties will be modified):
 * 	 <ul>
 * 		<li>bool <b>asort</b>( [int $sort_flags = SORT_REGULAR ] ):
 * 			Sort the properties and maintain index association.
 * 		<li>bool <b>ksort</b>( [int $sort_flags = SORT_REGULAR ] ):
 * 			Sort the properties by offset.
 * 		<li>bool <b>krsort</b>( [int $sort_flags = SORT_REGULAR ] ):
 * 			Sort the properties by offset in reverse order.
 * 		<li>bool <b>natcasesort</b>():
 * 			Sort the properties using a case insensitive "natural order" algorithm.
 * 		<li>bool <b>natsort</b>():
 * 			Sort the properties using a "natural order" algorithm.
 * 		<li>bool <b>arsort</b>( [int $sort_flags = SORT_REGULAR ] ):
 * 			Sort the properties in reverse order and maintain index association.
 * 	 </ul>
 * 	<li><em>Array stack and list functions</em> (mProperties will be modified):
 * 	 <ul>
 * 		<li>mixed <b>array_push</b>( mixed $value1 [, mixed $... ] ):
 * 			Push one or more elements onto the end of the properties.
 * 		<li>mixed <b>array_pop</b>():
 * 			Pop the element off the end of the properties.
 * 		<li>int <b>array_unshift</b>( mixed $value1 [, mixed $... ] ):
 * 			Prepend one or more elements to the beginning of the properties.
 * 		<li>mixed <b>array_shift</b>():
 * 			Shift an element off the beginning of the properties.
 * 	 </ul>
 * </ul>
 *
 *	@package	Core
 *
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		02/08/2016
 *
 * @example
 * <code>
 * // Test class
 * class Test extends Container {
 * 	private $attribute = NULL;
 * 	private $flag = NULL;
 *
 * 	// Constructor
 * 	public function __construct( $properties = NULL, bool $as_array = FALSE ) {
 * 		parent::__construct( $properties, $as_array );
 * 		$this->flag = hex2bin( '00000000' );
 * 	}
 *
 * 	// Attribute manager.
 * 	function Attribute( $value = NULL, $old = FALSE ) {
 * 		return self::manageAttribute( $this->attribute, $value, $old );
 * 	}
 *
 * 	// Flag attribute manager.
 * 	function FlagAttribute(
 * 		string $mask = NULL, bool $value = NULL, bool $old = FALSE ) {
 * 		return self::manageBitfieldAttribute( $this->flag, $mask, $value, $old );
 * 	}
 *
 * 	// Property manager.
 * 	function Property( $offset, $value = NULL, $old = FALSE ) {
 * 		return $this->manageProperty( $offset, $value, $old );
 * 	}
 *
 * 	// Flag property manager.
 * 	function FlagProperty(
 * 		$offset, string $mask = NULL, bool $value = NULL, bool $old = FALSE ) {
 * 		return $this->manageBitfieldProperty( $offset, $mask, $value, $old );
 * 	}
 * }
 *
 * // Instantiate empty object.
 * $object = new Test();
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0x00000000
 * //     [mProperties:protected] => Array
 * //         (
 * //         )
 * // )
 *
 * // Instantiate object with data.
 * $object = new Test( [1, 2, 3] );
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0x00000000
 * //     [mProperties:protected] => Array
 * //         (
 * //             [0] => 1
 * //             [1] => 2
 * //             [2] => 3
 * //         )
 * // )
 *
 * // Get a property.
 * $result = $object[ 0 ];
 * // (int)1
 *
 * // Get a non existing property.
 * $result = $object[ "UNKNOWN" ];
 * // NULL - will not trigger an alert.
 *
 * // Replace a property.
 * $object[ 0 ] = "REPLACED";
 *
 * // Create a new property.
 * $object[ "NEW" ] = "NEW PROPERTY";
 *
 * // Create a nested property.
 * $object[ [ "nested", "string" ] ] = "a string";
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0x00000000
 * //     [mProperties:protected] => Array
 * //         (
 * //             [0] => REPLACED
 * //             [1] => 2
 * //             [2] => 3
 * //             [NEW] => NEW PROPERTY
 * //             [nested] => Array
 * //                 (
 * //                     [string] => a string
 * //                 )
 * //         )
 * // )
 *
 * // Get nested property.
 * $result = $object[ [ "nested", "string" ] ];
 * $result = $object[ "nested" ][ "string" ];
 * // (string)"a string"
 *
 * // Set nested property.
 * $object[ [ "nested", "string" ] ] = "changed";
 * // ...
 * //             [nested] => Array
 * //                 (
 * //                     [string] => changed
 * //                 )
 * // Cannot use: $object[ "nested" ][ "string" ] = "changed"; - Sorry...
 *
 *
 * // Get property reference.
 * $reference = & $object->propertyReference( 0 );
 * $reference = "CHANGED";
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0x00000000
 * //     [mProperties:protected] => Array
 * //         (
 * //             [0] => CHANGED
 * // ...
 *
 * // Get nested property reference.
 * $reference = & $object->propertyReference( [ "nested", "string" ] );
 * $reference = "modified";
 * // ...
 * //             [nested] => Array
 * //                 (
 * //                     [string] => modified
 * //                 )
 *
 * // Works also with ArrayObject and Container objects.
 * $object[ 0 ] = new ArrayObject([ "container" => new Container([ "number" => 46 ]) ]);
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0x00000000
 * //     [mProperties:protected] => Array
 * //         (
 * //             [0] => ArrayObject Object
 * //                 (
 * //                     [storage:ArrayObject:private] => Array
 * //                         (
 * //                             [container] => Milko\wrapper\Container Object
 * //                                 (
 * //                                     [mProperties:protected] => Array
 * //                                         (
 * //                                             [number] => 46
 * //                                         )
 * //                                 )
 * //                         )
 * //                 )
 * // ...
 * $result = $object[ [ 0, "container", "number" ] ];
 * $result = $object[ 0 ][ "container" ][ "number" ];
 * // (int)46
 * $reference = & $object->propertyReference( [ 0, "container", "number" ] );
 * $reference = 88;
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0x00000000
 * //     [mProperties:protected] => Array
 * //         (
 * //             [0] => ArrayObject Object
 * //                 (
 * //                     [storage:ArrayObject:private] => Array
 * //                         (
 * //                             [container] => Milko\wrapper\Container Object
 * //                                 (
 * //                                     [mProperties:protected] => Array
 * //                                         (
 * //                                             [number] => 88
 * //                                         )
 * //                                 )
 * //                         )
 * //                 )
 * // ...
 *
 * // Should dispose of reference once you are done using it.
 * unset( $reference );
 *
 * // Append an element to a structure.
 * $object[ [ 0, "container", NULL, "appended" ] ] = "value";
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0x00000000
 * //     [mProperties:protected] => Array
 * //         (
 * //             [0] => ArrayObject Object
 * //                 (
 * //                     [storage:ArrayObject:private] => Array
 * //                         (
 * //                             [container] => Milko\wrapper\Container Object
 * //                                 (
 * //                                     [mProperties:protected] => Array
 * //                                         (
 * //                                             [number] => 88
 * //                                             [0] => Array
 * //                                                 (
 * //                                                     [appended] => value
 * //                                                 )
 * //                                         )
 * //                                 )
 * //                         )
 * //                 )
 * // ...
 *
 * // Delete property and all resulting empty collections.
 * $result = $object[ [ "nested", "string" ] ] = NULL;
 * // The following property will be deleted.
 * //             [nested] => Array
 * //                 (
 * //                     [string] => modified
 * //                 )
 *
 * // Set attribute.
 * $result = $object->Attribute( "first" );
 * // $result == "first";
 * //     [attribute:Test:private] => first
 *
 * // Set attribute and return old value.
 * $result = $object->Attribute( "second", TRUE );
 * // $result == "first";
 * //     [attribute:Test:private] => second
 *
 * // Retrieve attribute.
 * $result = $object->Attribute();
 * // $result == "second";
 *
 * // Reset attribute.
 * $result = $object->Attribute( FALSE, TRUE );
 * // $result == "second";
 * //     [attribute:Test:private] => NULL
 *
 * // Get current flag attribute value.
 * $result = $object->FlagAttribute();
 * // $result == 0x00000000;
 *
 * // Turn on masked bits.
 * $result = $object->FlagAttribute( hex2bin("ff0000ff"), TRUE );
 * // $result == 0xff0000ff;
 * //     [flag:Test:private] => 0xff0000ff
 *
 * // Turn off masked bits.
 * $result = $object->FlagAttribute( hex2bin("ff000000"), FALSE );
 * // $result == 0x000000ff;
 * //     [flag:Test:private] => 0x000000ff
 *
 * // Turn on masked bits and return old value.
 * $result = $object->FlagAttribute( hex2bin("ff000000"), TRUE, TRUE );
 * // $result == 0x000000ff;
 * //     [flag:Test:private] => 0xff0000ff
 *
 * // Check flag with mask.
 * $result = $object->FlagAttribute( hex2bin("0000000f") );
 * // $result === (bool)TRUE;
 * $result = $object->FlagAttribute( hex2bin("0000f000") );
 * // $result === (bool)FALSE;
 *
 * // Flatten to array.
 * $object->toArray();
 * // Before:
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0xff0000ff
 * //     [mProperties:protected] => Array
 * //         (
 * //             [0] => ArrayObject Object
 * //                 (
 * //                     [storage:ArrayObject:private] => Array
 * //                         (
 * //                             [container] => Milko\wrapper\Container Object
 * //                                 (
 * //                                     [mProperties:protected] => Array
 * //                                         (
 * //                                             [number] => 88
 * //                                             [0] => Array
 * //                                                 (
 * //                                                     [appended] => value
 * //                                                 )
 * //                                         )
 * //                                 )
 * //                         )
 * //                 )
 * //             [1] => 2
 * //             [2] => 3
 * //             [NEW] => NEW PROPERTY
 * //         )
 * // )
 * // After:
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0xff0000ff
 * //     [mProperties:protected] => Array
 * //         (
 * //             [0] => Array
 * //                 (
 * //                     [container] => Array
 * //                         (
 * //                             [number] => 88
 * //                             [0] => Array
 * //                                 (
 * //                                     [appended] => value
 * //                                 )
 * //                         )
 * //                 )
 * //             [1] => 2
 * //             [2] => 3
 * //             [NEW] => NEW PROPERTY
 * //         )
 * // )
 *
 * // Get property.
 * $result = $object->Property( 1 );
 * // $result == (int)2
 *
 * // Get nested property.
 * $result = $object->Property( [ 0, "container", "number" ] );
 * // $result == (int)88
 *
 * // Set property.
 * $result = $object->Property( 1, 99 );
 * // $result == (int)2
 * // $object[ 1 ] == (int)99
 *
 * // Set nested property and return old value.
 * $result = $object->Property( [ 0, "container", "number" ], 100, TRUE );
 * // $result == (int)2
 * // $object[ 1 ] == (int)100
 *
 * // Reset nested property.
 * $result = $object->Property( [ 0, "container", 0, "appended" ], FALSE );
 * // $result == (string)"value"
 * // $object[ 0 ][ "container" ][ 0 ] will also be deleted.
 *
 * // Reset nested property with all resulting empty structures and return old value.
 * $result = $object->Property( [ 0, "container" ], FALSE, TRUE );
 * // $result == (int)100
 * // Test Object
 * // (
 * //     [attribute:Test:private] => NULL
 * //     [flag:Test:private] => 0xff0000ff
 * //     [mProperties:protected] => Array
 * //         (
 * //             [1] => 99
 * //             [2] => 3
 * //             [NEW] => NEW PROPERTY
 * //         )
 * // )
 * </code>
 */
class Container implements \ArrayAccess, \IteratorAggregate, \Countable
{
	/**
	 * Properties.
	 *
	 * This attribute stores the document properties.
	 *
	 * @var array
	 */
	protected $mProperties = [];




/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate class.</h4>
	 *
	 * The class may be instantiated with an array, an ArrayObject or an instance of this
	 * class.
	 *
	 * If the second parameter is <tt>TRUE</tt>, the provided properties will be converted
	 * to an array ({@link convertToArray()}.
	 *
	 * @param mixed					$theProperties		Properties or <tt>NULL</tt>.
	 * @param bool					$asArray			<tt>TRUE</tt> convert to array.
	 * @throws \InvalidArgumentException
	 *
	 * @uses convertToArray()
	 *
	 * @example
	 * <code>
	 * // Empty container.
	 * $object = new Container();
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 *
	 * // With array.
	 * $object = new Container( [1,2,3] );
	 * // With Container.
	 * $object = new Container( new Container( [1,2,3] ) );
	 * // With ArrayObject.
	 * $object = new Container( new ArrayObject( [1,2,3] ) );
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * //			[0] => 1
	 * //			[1] => 2
	 * //			[2] => 3
	 * // 		)
	 * // )
	 *
	 * // With embedded objects.
	 * $object = new Container(
	 * 	[ 1 => new Container(
	 * 		[ 2 => new ArrayObject(
	 * 			[ 1, 2, 3 ] ) ] ) ] );
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[1] => Milko\wrapper\Container Object
	 * // 				(
	 * // 					[mProperties:protected] => Array
	 * // 						(
	 * // 							[2] => ArrayObject Object
	 * // 								(
	 * // 									[storage:ArrayObject:private] => Array
	 * // 										(
	 * // 											[0] => 1
	 * // 											[1] => 2
	 * // 											[2] => 3
	 * // 										)
	 * // 								)
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 *
	 * // With embedded objects converted to array.
	 * $object = new Container(
	 * 	[ 1 => new Container(
	 * 		[ 2 => new ArrayObject(
	 * 			[ 1, 2, 3 ] ) ] ) ],
	 * 	TRUE );
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[1] => Array
	 * // 				(
	 * // 					[2] => Array
	 * // 						(
	 * // 							[0] => 1
	 * // 							[1] => 2
	 * // 							[2] => 3
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 * </code>
	 */
	public function __construct( $theProperties = NULL, bool $asArray = FALSE )
	{
		//
		// Handle properties.
		//
		if( $theProperties !== NULL )
		{
			//
			// Check container type.
			//
			if( is_array( $theProperties )
			 || ($theProperties instanceof self)
			 || ($theProperties instanceof \ArrayObject) )
			{
				//
				// Flatten to array.
				//
				if( $asArray )
					static::convertToArray( $theProperties );

				//
				// Handle arrays.
				//
				if( is_array( $theProperties ) )
					$this->mProperties = $theProperties;

				//
				// Handle objects.
				//
				else
					$this->mProperties = $theProperties->getArrayCopy();

			} // Valid container type.

			//
			// Handle invalid type.
			//
			elseif( $theProperties !== NULL )
				throw new \InvalidArgumentException(
					"Provided invalid container type."
				);																// !@! ==>

		} // Provided properties.

	} // Constructor.



/*=======================================================================================
 *																						*
 *								ARRAY ACCESS INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	offsetExists																	*
	 *==================================================================================*/

	/**
	 * <h4>Check whether an offset exists.</h4><p />
	 *
	 * The method expects a single parameter that represents the property offset to match:
	 *
	 * <ul>
	 * 	<li><tt>NULL</tt>: Will return <tt>FALSE</tt>.
	 * 	<li><tt>scalar</tt>: Will check whether the offset exists at the top structure
	 * 		level.
	 * 	<li><i>list</i>: Will traverse the structure using the provided sequence of offsets.
	 * 		If the list is empty it is assumed the offset doesn't exist. The list must be
	 * 		provided as an <tt>array</tt>, <tt>Container</tt> or an <tt>ArrayObject</tt>,
	 * 		any other type will raise an exception. If any element of the list is not a
	 * 		scalar or <tt>NULL</tt> the method will trigger an exception.
	 * </ul>
	 *
	 * When providing a list of offsets, <tt>[ 1, 2, 3 ]</tt> is equivalent to
	 * <tt>$object[1][2][3]</tt>.
	 *
	 * <em>The <tt>NULL</tt> offset is used to append an element in {@link offsetSet()}, it
	 * is handled in this method for consistency.</em>
	 *
	 * @param mixed					$theOffset			Offset.
	 * @return bool					<tt>TRUE</tt> the offset exists.
	 * @throws \InvalidArgumentException
	 *
	 * @uses getArrayCopy()
	 * @uses nestedPropertyReference()
	 *
	 * @example
	 * <code>
	 * // Example structure.
	 * $object = new Container( [
	 * 	"offset" => "value",
	 * 	"list" => [ 1, 2 ],
	 * 	"nested" => [
	 * 		1 => [
	 * 			2 => new ArrayObject( [
	 * 				3 => "three" ] )
	 * 		]
	 * 	]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[offset] => value
	 * // 			[list] => Array
	 * // 				(
	 * // 					[0] => 1
	 * // 					[1] => 2
	 * // 				)
	 * // 			[nested] => Array
	 * // 				(
	 * // 					[1] => Array
	 * // 						(
	 * // 							[2] => ArrayObject Object
	 * // 								(
	 * // 									[storage:ArrayObject:private] => Array
	 * // 										(
	 * // 											[3] => three
	 * // 										)
	 * // 								)
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 *
	 * // Will return TRUE.
	 * $test->offsetExists( "offset" );
	 *
	 * // Will return FALSE.
	 * $test->offsetExists( "UNKNOWN" );
	 *
	 * // Will return TRUE.
	 * $test->offsetExists( [ "nested", 1, 2, 3 ] );
	 *
	 * // Will return FALSE.
	 * $test->offsetExists( [ "nested", 1, "UNKNOWN", 3 ] );
	 * </code>
	 */
	public function offsetExists( $theOffset )
	{
		//
		// Intercept append.
		//
		if( $theOffset === NULL )
			return FALSE;															// ==>

		//
		// Handle scalar property.
		//
		if( is_scalar( $theOffset ) )
			return array_key_exists( $theOffset, $this->mProperties );				// ==>

		//
		// Handle nested property.
		//
		if( is_array( $theOffset )
		 || ($theOffset instanceof self)
		 || ($theOffset instanceof \ArrayObject) )
		{
			//
			// Convert to array.
			//
			if( ! is_array( $theOffset ) )
				$theOffset = $theOffset->getArrayCopy();

			//
			// Handle empty list.
			//
			if( ! count( $theOffset ) )
				return FALSE;														// ==>

			//
			// Match offsets.
			//
			$this->nestedPropertyReference( $theOffset );

			return (! (bool)count( $theOffset ) );									// ==>

		} // Nested offset.

		throw new \InvalidArgumentException(
			"Invalid offset type."
		);																		// !@! ==>

	} // offsetExists.


	/*===================================================================================
	 *	offsetGet																		*
	 *==================================================================================*/

	/**
	 * <h4>Return a value at a given offset.</h4><p />
	 *
	 * We implement this method to handle the case in which the offset does not exist: if
	 * that is the case we return <tt>NULL</tt> instead of issuing a warning.
	 *
	 * The method expects a single parameter that represents the property offset to match:
	 *
	 * <ul>
	 * 	<li><tt>NULL</tt>: Will return <tt>NULL</tt>.
	 * 	<li><tt>scalar</tt>: Will check whether the offset exists at the top structure
	 * 		level.
	 * 	<li><i>list</i>: Will traverse the structure using the provided sequence of offsets.
	 * 		If the list is empty it is assumed the offset doesn't exist. The list must be
	 * 		provided as an <tt>array</tt>, <tt>Container</tt> or an <tt>ArrayObject</tt>,
	 * 		any other type will raise an exception. If any element of the list is not a
	 * 		scalar or <tt>NULL</tt> the method will trigger an exception.
	 * </ul>
	 *
	 * When providing a list of offsets, <tt>[ 1, 2, 3 ]</tt> is equivalent to
	 * <tt>$object[1][2][3]</tt>; the latter form can also be used to retrieve properties,
	 * <em>but not to set them</em>.
	 *
	 * <em>The <tt>NULL</tt> offset is used to append an element in {@link offsetSet()}, it
	 * is handled in this method for consistency.</em>
	 *
	 * @param mixed					$theOffset			Offset.
	 * @return mixed				Offset value or <tt>NULL</tt>.
	 * @throws \InvalidArgumentException
	 *
	 * @uses offsetExists()
	 * @uses getArrayCopy()
	 * @uses nestedPropertyReference()
	 *
	 * @example
	 * <code>
	 * // Example structure.
	 * $object = new Container( [
	 * 	"offset" => "value",
	 * 	"list" => [ 1, 2 ],
	 * 	"nested" => [
	 * 		1 => [
	 * 			2 => new ArrayObject( [
	 * 				3 => "three" ] )
	 * 		]
	 * 	]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[offset] => value
	 * // 			[list] => Array
	 * // 				(
	 * // 					[0] => 1
	 * // 					[1] => 2
	 * // 				)
	 * // 			[nested] => Array
	 * // 				(
	 * // 					[1] => Array
	 * // 						(
	 * // 							[2] => ArrayObject Object
	 * // 								(
	 * // 									[storage:ArrayObject:private] => Array
	 * // 										(
	 * // 											[3] => three
	 * // 										)
	 * // 								)
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 *
	 * // Will return "value".
	 * $result = $test->offsetGet( "offset" );
	 *
	 * // Will return NULL.
	 * $result = $test->offsetGet( "UNKNOWN" );
	 *
	 * // Will return "three".
	 * $result = $test->offsetGet( [ "nested", 1, 2, 3 ] );
	 * $result = $test[ "nested" ][ 1 ][ 2 ][ 3 ];
	 *
	 * // Get an object property.
	 * $result = $test[ explode( '.', "object.property.path" ) ];
	 *
	 * // Will return NULL.
	 * $result = $test->offsetGet( [ "nested", 1, 2, "UNKNOWN", 3 ] );
	 * </code>
	 */
	public function offsetGet( $theOffset )
	{
		//
		// Intercept append.
		//
		if( $theOffset === NULL )
			return NULL;															// ==>

		//
		// Handle scalar property.
		//
		if( is_scalar( $theOffset ) )
			return ( $this->offsetExists( $theOffset ) )
				 ? $this->mProperties[ $theOffset ]									// ==>
				 : NULL;															// ==>

		//
		// Handle nested property.
		//
		if( is_array( $theOffset )
		 || ($theOffset instanceof self)
		 || ($theOffset instanceof \ArrayObject) )
		{
			//
			// Convert to array.
			//
			if( ! is_array( $theOffset ) )
				$theOffset = $theOffset->getArrayCopy();

			//
			// Handle empty list.
			//
			if( ! count( $theOffset ) )
				return NULL;														// ==>

			//
			// Match offsets.
			//
			$value = $this->nestedPropertyReference( $theOffset );

			return (! (bool)count( $theOffset ) )
				 ? $value															// ==>
				 : NULL;															// ==>

		} // Nested offset.

		throw new \InvalidArgumentException(
			"Invalid offset type."
		);																		// !@! ==>

	} // offsetGet.


	/*===================================================================================
	 *	offsetSet																		*
	 *==================================================================================*/

	/**
	 * <h4>Set a value at a given offset.</h4><p />
	 *
	 * We implement this method to handle the <tt>NULL</tt> value in the <tt>$theValue</tt>
	 * parameter: if the offset exists it will be deleted, if not, the method will do
	 * nothing.
	 *
	 * The method expects the following parameters:
	 *
	 * <ul>
	 * 	<li><b>$theOffset</b>: The offset to set:
	 *	 <ul>
	 *	 	<li><tt>NULL</tt>: Will append the value to the structure; if the parent
	 * 			property is not a structure, the method will raise an exception.
	 *	 	<li><tt>scalar</tt>: Will create or set the offset at the top structure level
	 * 			with the provided value.
	 * 		<li><i>list</i>: Will traverse the structure using the provided sequence of
	 * 			offsets and create or set the nested structure offset with the provided
	 * 			value. <em>Any intermediate level offset that doesn't yet exist, or that is
	 * 			not an <tt>array</tt>, <tt>Container</tt> or an <tt>ArrayObject</tt>, will
	 * 			be initialised as an <tt>array</tt></em>. If the list is empty the method
	 * 			will do nothing. The list must be provided as an <tt>array</tt>,
	 * 			<tt>Container</tt> or an <tt>ArrayObject</tt>, any other type will raise an
	 * 			exception. If any element of the list is not a scalar or <tt>NULL</tt> the
	 * 			method will trigger an exception.
	 *	 </ul>
	 * 	<li><b>$theValue</b>: The value to set:
	 *	 <ul>
	 *	 	<li><tt>NULL</tt>: If the offset is matched, the method will delete the property
	 * 			referenced by the provided offset: see {@link offsetUnset()}.
	 *	 	<li><i>other</i>: Will set the property referenced by the provided offset with
	 * 			the provided value.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value to set at offset.
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 *
	 * @uses offsetUnset()
	 * @uses getArrayCopy()
	 * @uses nestedPropertyReference()
	 *
	 * @example
	 * <code>
	 * // Instantiate container.
	 * $object = new Container();
	 *
	 * // $object[ "offset' ] = "value";
	 * $object->offsetSet( "offset", "value" );
	 *
	 * // $object[ [ 1, 2, 3 ] ] = "value";
	 * // $object[1][2] and $object[1] will be arrays.
	 * $object->offsetSet( [ 1, 2, 3 ], "value" );
	 *
	 * // Add object structures.
	 * // $object[ explode( '.', "object.property.path" ) ] = "value";
	 * $object->offsetSet(
	 * 	"object",
	 * 	new Container([
	 * 		"property" => new ArrayObject([
	 * 			"path" => "value" ])
	 * 		]) );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => value
	 * //             [1] => Array
	 * //                 (
	 * //                     [2] => Array
	 * //                         (
	 * //                             [3] => value
	 * //                         )
	 * //                 )
	 * //             [object] => Milko\wrapper\Container Object
	 * //                 (
	 * //                     [mProperties:protected] => Array
	 * //                         (
	 * //                             [property] => ArrayObject Object
	 * //                                 (
	 * //                                     [storage:ArrayObject:private] => Array
	 * //                                         (
	 * //                                             [path] => value
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Convert a scalar to an array.
	 * // $object[ explode( '.', "object.property.path.leaf" ) ] = "value";
	 * $object->offsetSet( explode( '.', "object.property.path.leaf" ), "value" );
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => value
	 * //             [1] => Array
	 * //                 (
	 * //                     [2] => Array
	 * //                         (
	 * //                             [3] => value
	 * //                         )
	 * //                 )
	 * //             [object] => Milko\wrapper\Container Object
	 * //                 (
	 * //                     [mProperties:protected] => Array
	 * //                         (
	 * //                             [property] => ArrayObject Object
	 * //                                 (
	 * //                                     [storage:ArrayObject:private] => Array
	 * //                                         (
	 * //                                             [path] => Array
	 * //                                                 (
	 * //                                                     [leaf] => value
	 * //                                                 )
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Append an element.
	 * $object->offsetSet( [ 1, 2, NULL ], "appended" );
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => value
	 * //             [1] => Array
	 * //                 (
	 * //                     [2] => Array
	 * //                         (
	 * //                             [3] => value
	 * //                             [4] => appended
	 * //                         )
	 * //                 )
	 * //             [object] => Milko\wrapper\Container Object
	 * //                 (
	 * //                     [mProperties:protected] => Array
	 * //                         (
	 * //                             [property] => ArrayObject Object
	 * //                                 (
	 * //                                     [storage:ArrayObject:private] => Array
	 * //                                         (
	 * //                                             [path] => Array
	 * //                                                 (
	 * //                                                     [leaf] => value
	 * //                                                 )
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 * // $object->offsetSet( [ 1, 2, 3, NULL ], "appended" ) would raise an exception.
	 *
	 * // Append an element to a structure.
	 * $object->offsetSet( [ "object", "property", NULL, "leaf" ], "appended" );
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => value
	 * //             [1] => Array
	 * //                 (
	 * //                     [2] => Array
	 * //                         (
	 * //                             [3] => value
	 * //                             [4] => appended
	 * //                         )
	 * //                 )
	 * //             [object] => Milko\wrapper\Container Object
	 * //                 (
	 * //                     [mProperties:protected] => Array
	 * //                         (
	 * //                             [property] => ArrayObject Object
	 * //                                 (
	 * //                                     [storage:ArrayObject:private] => Array
	 * //                                         (
	 * //                                             [path] => Array
	 * //                                                 (
	 * //                                                     [leaf] => value
	 * //                                                 )
	 * //                                             [0] => Array
	 * //                                                 (
	 * //                                                     [leaf] => appended
	 * //                                                 )
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Delete $object[ "offset' ].
	 * // $object[ "offset' ] = NULL;
	 * $object->offsetSet( "offset", NULL );
	 * // Equivalent to offsetUnset( "offset" );
	 *
	 * // Delete $object[1][2][3],
	 * // $object[1][2] and $object[1] will also be deleted,
	 * // because they would become empty.
	 * // $object[ [ 1, 2, 3 ] ] = NULL;
	 * $object->offsetSet( [ 1, 2, 3 ], NULL );
	 * // Equivalent to offsetUnset( [ 1, 2, 3 ] );
	 *
	 * // Delete $object["object"]["property"],
	 * // $object["object"] will also be deleted because it would become empty.
	 * $object->offsetSet( [ "object", "property" ], NULL );
	 * // Equivalent to $object[ [ "object", "property" ] ] = NULL;
	 * // Equivalent to offsetUnset( [ "object", "property" ] );
	 *
	 * // Delete $object[ 1 ][ 2 ][ 4 ],
	 * // all structures under $object[ 1 ] will also be deleted
	 * // because they would become empty.
	 * $object[ [ 1, 2, 4 ] ] = NULL;
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 * </code>
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Handle set.
		//
		if( $theValue !== NULL )
		{
			//
			// Append.
			//
			if( $theOffset === NULL )
				$this->mProperties[] = $theValue;

			//
			// Top level property.
			//
			elseif( is_scalar( $theOffset ) )
				$this->mProperties[ $theOffset ] = $theValue;

			//
			// Handle nested property.
			//
			elseif( is_array( $theOffset )
				 || ($theOffset instanceof self)
				 || ($theOffset instanceof \ArrayObject) )
			{
				//
				// Convert to array.
				//
				if( ! is_array( $theOffset ) )
					$theOffset = $theOffset->getArrayCopy();

				//
				// Handle list.
				//
				$theOffset = (array)$theOffset;
				if( count( $theOffset ) )
				{
					//
					// Match offsets.
					//
					$reference = & $this->nestedPropertyReference( $theOffset );

					//
					// Set existing offset.
					//
					if( ! count( $theOffset ) )
						$reference = $theValue;

					//
					// Initialise missing offsets.
					//
					else
					{
						//
						// Iterate missing offsets.
						//
						do
						{
							//
							// Get current offset.
							//
							$offset = array_shift( $theOffset );
							if( ($offset !== NULL)
							 && (! is_scalar( $offset )) )
								throw new \InvalidArgumentException(
									"Provided non scalar nested offset." );		// !@! ==>

							//
							// Handle append offset.
							//
							if( $offset === NULL )
							{
								//
								// Assert structures.
								//
								if( is_array( $reference )
								 || ($reference instanceof self)
								 || ($reference instanceof \ArrayObject) )
								{
									//
									// Handle containers.
									//
									if( $reference instanceof self )
										$reference = & $reference->mProperties;

									//
									// Append an array.
									// We need to do this to get the appended offset,
									// the value will eventually be replaced.
									//
									$reference[] = [];

									//
									// Handle arrays.
									//
									if( is_array( $reference ) )
										$offset
											= array_keys( $reference )
												[ count( $reference ) - 1 ];

									//
									// Handle structures.
									//
									else
										$offset
											= array_keys( $reference->getArrayCopy() )
												[ $reference->count() - 1 ];

								} // Reference is an array.

								else
									throw new \RuntimeException(
										"Unable to append element: " .
										"the target is not a structure." );		// !@! ==>

							} // Append element.

							//
							// Not leaf offset.
							//
							if( count( $theOffset ) )
							{
								//
								// Allocate property.
								//
								$reference[ $offset ] = [];

								//
								// Reference property.
								//
								$reference = & $reference[ $offset ];

							} // Not leaf offset.

						} while( count( $theOffset ) );

						//
						// Overwrite scalar with array.
						//
						if( (! is_array( $reference ))
						 && (! ($reference instanceof self))
						 && (! ($reference instanceof \ArrayObject)) )
							$reference = [];

						//
						// Set value.
						//
						$reference[ $offset ] = $theValue;

					} // Initialising missing offsets.

				} // Non empty list.

			} // Nested offset.

			//
			// Handle invalid offset type.
			//
			else
				throw new \InvalidArgumentException(
					"Invalid offset type."
				);																// !@! ==>

		} // Set.

		//
		// Handle unset.
		//
		else
			$this->offsetUnset( $theOffset );

	} // offsetSet.


	/*===================================================================================
	 *	offsetUnset																		*
	 *==================================================================================*/

	/**
	 * <h4>Reset a value at a given offset.</h4><p />
	 *
	 * We implement this method to prevent warnings when a non-existing offset is provided,
	 * in that case we do nothing.
	 *
	 * The method expects a single parameter that represents the property offset:
	 *
	 * <ul>
	 * 	<li><tt>NULL</tt>: Will do nothing.
	 * 	<li><tt>scalar</tt>: Will delete the property identified by the provided offset at
	 * 		the top level of the structure.
	 * 	<li><i>list</i>: Will traverse the structure using the provided sequence of
	 *		offsets and delete the property identified by the leaf offset in the list. If
	 * 		the list is empty or if any offset is not found, the method will do nothing. The
	 *		list must be provided as an array, Container or an ArrayObject, any other type
	 * 		will raise an exception. If any element of the list is not <tt>NULL</tt> or a
	 * 		scalar, the method will fail with an exception.
	 * </ul>
	 *
	 * <em>When deleting nested properties any structure or array that becomes empty will
	 * also be deleted.</em>
	 *
	 * <em>The <tt>NULL</tt> offset is used to append an element in {@link offsetSet()}, it
	 * is handled in this method for consistency.</em>
	 *
	 * @param string				$theOffset			Offset.
	 *
	 * @uses getArrayCopy()
	 * @uses nestedPropertyReference()
	 * @throws \InvalidArgumentException
	 *
	 * @example
	 * <code>
	 * // Example structure.
	 * $object = new Container( [
	 * 	"offset" => "value",
	 * 	"list" => [ 1, 2 ],
	 * 	"nested" => [
	 * 		1 => [
	 * 			2 => new ArrayObject( [
	 * 				3 => "three" ] )
	 * 		]
	 * 	]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[offset] => value
	 * // 			[list] => Array
	 * // 				(
	 * // 					[0] => 1
	 * // 					[1] => 2
	 * // 				)
	 * // 			[nested] => Array
	 * // 				(
	 * // 					[1] => Array
	 * // 						(
	 * // 							[2] => ArrayObject Object
	 * // 								(
	 * // 									[storage:ArrayObject:private] => Array
	 * // 										(
	 * // 											[3] => three
	 * // 										)
	 * // 								)
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 *
	 * // Will delete the "offset" property.
	 * // Also $object[ "offset" ] = NULL;
	 * $object->offsetUnset( "offset" );
	 *
	 * // Will not raise an alert.
	 * // Also $object[ "UNKNOWN" ] = NULL;
	 * $object->offsetUnset( "UNKNOWN" );
	 *
	 * // Will delete the $object[ "list" ][ 0 ] property.
	 * // Also $object[ [ "list", 0 ] ] = NULL;
	 * $object->offsetUnset( [ "list", 0 ] );
	 *
	 * // Will delete the $object[ "nested" ][ 1 ][ 2 ][ 3 ] property
	 * // and all properties including "nested", since they would be empty.
	 * // Also $object[ [ "nested", 1, 2, 3 ] ] = NULL;
	 * $object->offsetUnset( [ "nested", 1, 2, 3 ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[list] => Array
	 * // 				(
	 * // 					[1] => 2
	 * // 				)
	 * // 		)
	 * // )
	 * </code>
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Intercept append.
		//
		if( $theOffset !== NULL )
		{
			//
			// Top level property.
			//
			if( is_scalar( $theOffset ) )
			{
				//
				// Delete value.
				//
				if( array_key_exists( $theOffset, $this->mProperties ) )
					unset( $this->mProperties[ $theOffset ] );

			} // Scalar.

			//
			// Handle nested property.
			//
			elseif( is_array( $theOffset )
				|| ($theOffset instanceof self)
				|| ($theOffset instanceof \ArrayObject) )
			{
				//
				// Convert to array.
				//
				if( ! is_array( $theOffset ) )
					$theOffset = $theOffset->getArrayCopy();

				//
				// Handle list.
				//
				$theOffset = (array)$theOffset;
				if( count( $theOffset ) )
				{
					//
					// Match offsets.
					//
					$ref = & $this->nestedPropertyReference( $theOffset, TRUE );

					//
					// Handle existing offset.
					//
					if( is_array( $theOffset )
					 && count( $theOffset ) )
					{
						//
						// Delete property and pop leaf offset.
						//
						unset( $ref[ array_pop( $theOffset ) ] );

						//
						// Remove empty structures.
						//
						while( count( $theOffset ) )
						{
							//
							// Get parent property and leaf offset.
							//
							$ref = & $this->nestedPropertyReference( $theOffset, TRUE );
							$key = array_pop( $theOffset );

							//
							// Check if property is a structure.
							//
							if( is_array( $ref[ $key ] )
							 || ($ref[ $key ] instanceof self)
							 || ($ref[ $key ] instanceof \ArrayObject) )
							{
								//
								// Get structure elements count.
								//
								$count = ( is_array( $ref[ $key ] ) )
									   ? count( $ref[ $key ]  )
									   : $ref[ $key ]->count();

								//
								// Exit if not empty.
								//
								if( $count )
									break;										// =>

								//
								// Delete property and pop leaf offset.
								//
								unset( $ref[ $key ] );

							} // Is a structure.

							//
							// Not a structure.
							//
							else
								break;											// =>

						} // Removing empty structures.

					} // All levels match.

				} // Non empty list.

			} // Nested offset.

			//
			// Handle invalid offset type.
			//
			else
				throw new \InvalidArgumentException(
					"Invalid offset type."
				);																// !@! ==>

		} // Not append.

	} // offsetUnset.



/*=======================================================================================
 *																						*
 *								ITERATOR AGGREGATE INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	getIterator																		*
	 *==================================================================================*/

	/**
	 * <h4>Return an iterator.</h4><p />
	 *
	 * This method will return an iterator of the array data member.
	 *
	 * @return \ArrayIterator		The iterator.
	 *
	 * @example
	 * <code>
	 * // Get iterator.
	 * $iterator = $object->getIterator();
	 *
	 * // Work with iterator.
	 * foreach( $iterator as $key => $value )
	 * 	...
	 * </code>
	 */
	public function getIterator()
	{
		return new \ArrayIterator( $this->mProperties );							// ==>

	} // getIterator.



/*=======================================================================================
 *																						*
 *									COUNTABLE INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	count																			*
	 *==================================================================================*/

	/**
	 * <h4>Return the properties count.</h4><p />
	 *
	 * This method will return the count of the array data member.
	 *
	 * @return int					The count.
	 *
	 * @example
	 * <code>
	 * // Example structure.
	 * $object = new Container( [
	 * 	"offset" => "value",
	 * 	"list" => [ 1, 2 ],
	 * 	"nested" => [
	 * 		1 => [
	 * 			2 => new ArrayObject( [
	 * 				3 => "three" ] )
	 * 		]
	 * 	]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[offset] => value
	 * // 			[list] => Array
	 * // 				(
	 * // 					[0] => 1
	 * // 					[1] => 2
	 * // 				)
	 * // 			[nested] => Array
	 * // 				(
	 * // 					[1] => Array
	 * // 						(
	 * // 							[2] => ArrayObject Object
	 * // 								(
	 * // 									[storage:ArrayObject:private] => Array
	 * // 										(
	 * // 											[3] => three
	 * // 										)
	 * // 								)
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 *
	 * // Get number of object properties.
	 * $count = $object->count();
	 * // (int)3
	 * </code>
	 */
	public function count()
	{
		return count( $this->mProperties );											// ==>

	} // count.



/*=======================================================================================
 *																						*
 *								CALLABLE ARRAY INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	__call																			*
	 *==================================================================================*/

	/**
	 * <h4>Use array functions with object.</h4><p />
	 *
	 * This method will allow using array functions as class methods, it supports the
	 * following functions in which the array input parameter is implicitly the object
	 * properties array:
	 *
	 * <ul>
	 * 	<li><em>Array key and value functions</em>:
	 * 	 <ul>
	 * 		<li>array <b>array_keys</b>():
	 * 			Return all the property offsets at the top level.
	 * 		<li>array <b>array_values</b>():
	 * 			Return all the property values (equivalent to {@link getArrayCopy()}.
	 * 	 </ul>
	 * 	<li><em>Array sorting functions</em> (mProperties will be modified):
	 * 	 <ul>
	 * 		<li>bool <b>asort</b>( [int $sort_flags = SORT_REGULAR ] ):
	 * 			Sort the properties and maintain index association.
	 * 		<li>bool <b>ksort</b>( [int $sort_flags = SORT_REGULAR ] ):
	 * 			Sort the properties by offset.
	 * 		<li>bool <b>krsort</b>( [int $sort_flags = SORT_REGULAR ] ):
	 * 			Sort the properties by offset in reverse order.
	 * 		<li>bool <b>natcasesort</b>():
	 * 			Sort the properties using a case insensitive "natural order" algorithm.
	 * 		<li>bool <b>natsort</b>():
	 * 			Sort the properties using a "natural order" algorithm.
	 * 		<li>bool <b>arsort</b>( [int $sort_flags = SORT_REGULAR ] ):
	 * 			Sort the properties in reverse order and maintain index association.
	 * 	 </ul>
	 * 	<li><em>Array stack and list functions</em> (mProperties will be modified):
	 * 	 <ul>
	 * 		<li>mixed <b>array_push</b>( mixed $value1 [, mixed $... ] ):
	 * 			Push one or more elements onto the end of the properties.
	 * 		<li>mixed <b>array_pop</b>():
	 * 			Pop the element off the end of the properties.
	 * 		<li>int <b>array_unshift</b>( mixed $value1 [, mixed $... ] ):
	 * 			Prepend one or more elements to the beginning of the properties.
	 * 		<li>mixed <b>array_shift</b>():
	 * 			Shift an element off the beginning of the properties.
	 * 	 </ul>
	 * </ul>
	 *
	 * If the function is not supported, the method will raise an exception.
	 *
	 * The method only checks for required arguments and casts when possible: any invalid
	 * argument will trigger an error in the function itself.
	 *
	 * @param string				$theFunction		Function name.
	 * @param array					$theArguments		Function arguments.
	 * @return mixed				Function result.
	 * @throws \BadMethodCallException
	 * @throws \InvalidArgumentException
	 */
	public function __call( string $theFunction , array $theArguments  )
	{
		//
		// Accept only callable functions.
		//
		if( is_callable( $theFunction ) )
		{
			//
			// Parse by function name.
			//
			switch( $theFunction )
			{
				case "array_keys":
					return array_keys( $this->mProperties );						// ==>

				case "array_values":
					return array_values( $this->mProperties );						// ==>

				case "asort":
					if( ! count( $theArguments ) )
						return asort(
							$this->mProperties, SORT_REGULAR );						// ==>
					return asort(
						$this->mProperties, $theArguments[ 0 ] );					// ==>

				case "ksort":
					if( ! count( $theArguments ) )
						return ksort(
							$this->mProperties, SORT_REGULAR );						// ==>
					return ksort(
						$this->mProperties, $theArguments[ 0 ] );					// ==>

				case "krsort":
					if( ! count( $theArguments ) )
						return krsort(
							$this->mProperties, SORT_REGULAR );						// ==>
					return krsort(
						$this->mProperties, $theArguments[ 0 ] );					// ==>

				case "arsort":
					if( ! count( $theArguments ) )
						return arsort(
							$this->mProperties, SORT_REGULAR );						// ==>
					return arsort(
						$this->mProperties, $theArguments[ 0 ] );					// ==>

				case "natcasesort":
					return natcasesort( $this->mProperties );						// ==>

				case "natsort":
					return natsort( $this->mProperties );							// ==>

				case "array_pop":
					return array_pop( $this->mProperties );							// ==>

				case "array_push":
					return array_push( $this->mProperties, $theArguments );			// ==>

				case "array_shift":
					return array_shift( $this->mProperties );						// ==>

				case "array_unshift":
					if( count( $theArguments ) )
						return array_unshift( $this->mProperties, $theArguments );	// ==>
					throw new \InvalidArgumentException(
						__CLASS__ . "->" . $theFunction .
						"() ==> Missing required arguments."
					);															// !@! ==>

			} // Parsing by function name.

		} // Callable function.

		throw new \BadMethodCallException(
			__CLASS__ . "->" . $theFunction . "()"
		);																		// !@! ==>

	} // getArrayCopy.



/*=======================================================================================
 *																						*
 *								CUSTOM ARRAY INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	getArrayCopy																	*
	 *==================================================================================*/

	/**
	 * <h4>Return a copy of the object properties.</h4><p />
	 *
	 * This method will return a copy of the object properties array.
	 *
	 * <em>Subdocuments will not be converted to arrays, this means that if you modify the
	 * contents of an embedded object in the copy, the modifications will be also made on
	 * the source object: if you need a safe copy you will have to flatten the structure
	 * into an array with {@link asArray()}</em>.
	 *
	 * @return array				Array copy.
	 *
	 * @example
	 * <code>
	 * // Example structure.
	 * $object = new Container( [
	 * 	"offset" => "value",
	 * 	"list" => [ 1, 2 ],
	 * 	"nested" => [
	 * 		1 => [
	 * 			2 => new ArrayObject( [
	 * 				3 => "three" ] )
	 * 		]
	 * 	]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[offset] => value
	 * // 			[list] => Array
	 * // 				(
	 * // 					[0] => 1
	 * // 					[1] => 2
	 * // 				)
	 * // 			[nested] => Array
	 * // 				(
	 * // 					[1] => Array
	 * // 						(
	 * // 							[2] => ArrayObject Object
	 * // 								(
	 * // 									[storage:ArrayObject:private] => Array
	 * // 										(
	 * // 											[3] => three
	 * // 										)
	 * // 								)
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 *
	 * // Get a copy of the object's properties as an array.
	 * $copy = $object->getArrayCopy();
	 *
	 * // Array
	 * // (
	 * // 	[offset] => value
	 * // 	[list] => Array
	 * // 		(
	 * // 			[0] => 1
	 * // 			[1] => 2
	 * // 		)
	 * // 	[nested] => Array
	 * // 		(
	 * // 			[1] => Array
	 * // 				(
	 * // 					[2] => ArrayObject Object
	 * // 						(
	 * // 							[storage:ArrayObject:private] => Array
	 * // 								(
	 * // 									[3] => three
	 * // 								)
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 * </code>
	 */
	public function getArrayCopy()
	{
		return $this->mProperties;													// ==>

	} // getArrayCopy.


	/*===================================================================================
	 *	propertyReference																*
	 *==================================================================================*/

	/**
	 * <h4>Return a property reference.</h4><p />
	 *
	 * This method functions as the <tt>offsetGet()</tt> method, except that it returns the
	 * property reference instead of its value. If the offset is not matched, the method
	 * will return a reference to a <tt>NULL</tt> value: <em>in that case you should
	 * <b>ignore</b> the reference and <b>not use it</b></em>.
	 *
	 * The only difference with <tt>offsetGet()</tt> is that if you provide <tt>NULL</tt>,
	 * or an empty list, the method will return the reference to the root properties
	 * structure.
	 *
	 * For more information on the parameter, please refer to the {@link offsetGet()}
	 * method documentation.
	 *
	 * <em><b>Important</b>: When you retrieve a reference to a property you are on your
	 * own, it is your responsibility not to make a mess: this is especially important when
	 * retrieving the reference to the {@link mProperties} data member, setting it to
	 * anything other than an array will render the object unusable without warning.</em>
	 *
	 * <em><b>Important</b>: when you use references, such as:</em>
	 * <code>
	 * $reference = & $object->propertyReference( $offset );
	 * $reference = "X";
	 * </code>
	 * <em>The object property $object[ $offset ] will contain a reference to the string
	 * "X". If you use other methods that handle references you might encur into problems,
	 * so, once you are done with the reference, you should dispose of it:</em>
	 * <code>
	 * $reference = & $object->propertyReference( $offset );
	 * $reference = "X";
	 * unset( $reference );
	 * </code>
	 * <em>that way $object[ $offset ] will contain a string rather than a reference; you
	 * can set the reference to another reference before needing to clear it.</em>
	 *
	 * @param mixed					$theOffset			Offset.
	 * @return mixed				The property reference.
	 * @throws \InvalidArgumentException
	 *
	 * @uses offsetExists()
	 * @uses getArrayCopy()
	 * @uses nestedPropertyReference()
	 *
	 * @example
	 * <code>
	 * // Example structure.
	 * $object = new Container( [
	 * 	"offset" => "value",
	 * 	"list" => [ 1, 2 ],
	 * 	"nested" => [
	 * 		1 => [
	 * 			2 => new ArrayObject( [
	 * 				3 => "three" ] )
	 * 		]
	 * 	]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[offset] => value
	 * // 			[list] => Array
	 * // 				(
	 * // 					[0] => 1
	 * // 					[1] => 2
	 * // 				)
	 * // 			[nested] => Array
	 * // 				(
	 * // 					[1] => Array
	 * // 						(
	 * // 							[2] => ArrayObject Object
	 * // 								(
	 * // 									[storage:ArrayObject:private] => Array
	 * // 										(
	 * // 											[3] => three
	 * // 										)
	 * // 								)
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 *
	 * // Will return a reference to $object->mProperties.
	 * $properties = & $object->propertyReference();
	 * $properties = & $object->propertyReference( NULL );
	 * $properties = & $object->propertyReference( [] );
	 * // You should only set the reference to an array!
	 * // $properties = [ 1, 2, 3 ];
	 *
	 * // Clear reference once you are done with it.
	 * unset( $properties );
	 * // Hadn't you done it, the object would look like that:
	 * // object(Milko\wrapper\Container)#3 (1) {
	 * //   ["mProperties":protected]=>
	 * //   &array(3) {
	 * //		...
	 *
	 * // Will return a reference to $object[ "offset" ].
	 * $result = & $object->propertyReference( "offset" );
	 * if( $result !== NULL )
	 * {
	 *     // Will set $object[ "offset" ] to "changed".
	 *     $result = "changed";
	 * }
	 *
	 * // $result will be NULL.
	 * $result = & $object->propertyReference( "UNKNOWN" );
	 * // Never use $result in this case!
	 *
	 * // Will return a reference to $object["nested"][1][2][3].
	 * $result = $object->offsetGet( [ "nested", 1, 2, 3 ] );
	 * if( $result !== NULL )
	 * {
	 *     // Will set $object["nested"][1][2][3] to "changed".
	 *     $result = "changed";
	 * }
	 *
	 * // Will return NULL.
	 * $result = $object->offsetGet( [ "nested", 1, 2, "UNKNOWN", 3 ] );
	 * // Never use $result in this case!
	 *
	 * // Clear reference once you are done with it.
	 * unset( $result );
	 * </code>
	 */
	public function & propertyReference( $theOffset = NULL )
	{
		//
		// Intercept append.
		//
		if( $theOffset === NULL )
			return $this->mProperties;												// ==>

		//
		// Init not found value.
		//
		$scrap = NULL;

		//
		// Handle scalar property.
		//
		if( is_scalar( $theOffset ) )
		{
			if( $this->offsetExists( $theOffset ) )
				return $this->mProperties[ $theOffset ];							// ==>

			return $scrap;															// ==>
		}

		//
		// Handle nested property.
		//
		if( is_array( $theOffset )
		 || ($theOffset instanceof self)
		 || ($theOffset instanceof \ArrayObject) )
		{
			//
			// Convert to array.
			//
			if( ! is_array( $theOffset ) )
				$theOffset = $theOffset->getArrayCopy();

			//
			// Handle empty list.
			//
			if( ! count( $theOffset ) )
				return $this->mProperties;											// ==>

			//
			// Match offsets.
			//
			$value = & $this->nestedPropertyReference( $theOffset );

			if( ! (bool)count( $theOffset ) )
				return $value;														// ==>

			return $scrap;															// ==>

		} // Nested offset.

		throw new \InvalidArgumentException(
			"Invalid offset type."
		);																		// !@! ==>

	} // propertyReference.



/*=======================================================================================
 *																						*
 *								PUBLIC STRUCTURE INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	propertySchema																	*
	 *==================================================================================*/

	/**
	 * <h4>Return the properties schema.</h4><p />
	 *
	 * This method can be used to get the object's property schema, the method will return
	 * an array that lists all the leaf offsets with the relative paths where they are used:
	 *
	 * <ul>
	 *	<li><i>key</i>: The leaf offset name.
	 *	<li><i>value</i>: An array of paths as:
	 * 	 <ul>
	 * 		<li><em>$theToken is NULL</em>: arrays containing all the offsets used in the
	 * 			path in level order.
	 * 		<li><em>$theToken is string</em>: strings containing the sequence of offsets
	 * 			concatenated by the provided token.
	 * 	 </ul>
	 * </ul>
	 *
	 * This method will be used by the persistence framework to determine descriptor
	 * structures and scalar descriptors usage.
	 *
	 * @param string				$theToken			Optional token to return paths.
	 * @return array				Properties schema.
	 *
	 * @uses traverseSchema()
	 *
	 * @example
	 * <code>
	 * // Test object
	 * $object = new Container( [
	 *     "container" => new ArrayObject( [
	 *         "array" => [
	 *             "object" => new Container( [
	 *                 1, 2, 3
	 *             ] )
	 *         ]
	 *     ] ),
	 *     "array" => [
	 *     	new ArrayObject([
	 *     		new Container([
	 *     			"one" => 1,
	 *     			"two" => 2
	 *     		]),
	 *     		new Container([
	 *     			"one" => 3,
	 *     			"two" => 4
	 *     		])
	 *     	]),
	 *     ],
	 * 	"object" => 3,
	 *     "list" => [
	 *     	[ "one" => 1 ],
	 *     	[ "two" => 2 ],
	 *     	[ "object" => "o" ]
	 *     ]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [container] => ArrayObject Object
	 * //                 (
	 * //                     [storage:ArrayObject:private] => Array
	 * //                         (
	 * //                             [array] => Array
	 * //                                 (
	 * //                                     [object] => Milko\wrapper\Container Object
	 * //                                         (
	 * //                                             [mProperties:protected] => Array
	 * //                                                 (
	 * //                                                     [0] => 1
	 * //                                                     [1] => 2
	 * //                                                     [2] => 3
	 * //                                                 )
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //             [array] => Array
	 * //                 (
	 * //                     [0] => ArrayObject Object
	 * //                         (
	 * //                             [storage:ArrayObject:private] => Array
	 * //                                 (
	 * //                                     [0] => Milko\wrapper\Container Object
	 * //                                         (
	 * //                                             [mProperties:protected] => Array
	 * //                                                 (
	 * //                                                     [one] => 1
	 * //                                                     [two] => 2
	 * //                                                 )
	 * //                                         )
	 * //                                     [1] => Milko\wrapper\Container Object
	 * //                                         (
	 * //                                             [mProperties:protected] => Array
	 * //                                                 (
	 * //                                                     [one] => 3
	 * //                                                     [two] => 4
	 * //                                                 )
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //             [object] => 3
	 * //             [list] => Array
	 * //                 (
	 * //                     [0] => Array
	 * //                         (
	 * //                             [one] => 1
	 * //                         )
	 * //                     [1] => Array
	 * //                         (
	 * //                             [two] => 2
	 * //                         )
	 * //                     [2] => Array
	 * //                         (
	 * //                             [object] => o
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Get as arrays.
	 * $result = $object->propertySchema();
	 * // Array
	 * // (
	 * //     [object] => Array
	 * //         (
	 * //             [0] => Array
	 * //                 (
	 * //                     [0] => object
	 * //                 )
	 * //             [1] => Array
	 * //                 (
	 * //                     [0] => list
	 * //                     [1] => object
	 * //                 )
	 * //             [2] => Array
	 * //                 (
	 * //                     [0] => container
	 * //                     [1] => array
	 * //                     [2] => object
	 * //                 )
	 * //         )
	 * //     [one] => Array
	 * //         (
	 * //             [0] => Array
	 * //                 (
	 * //                     [0] => array
	 * //                     [1] => one
	 * //                 )
	 * //             [1] => Array
	 * //                 (
	 * //                     [0] => list
	 * //                     [1] => one
	 * //                 )
	 * //         )
	 * //     [two] => Array
	 * //         (
	 * //             [0] => Array
	 * //                 (
	 * //                     [0] => array
	 * //                     [1] => two
	 * //                 )
	 * //             [1] => Array
	 * //                 (
	 * //                     [0] => list
	 * //                     [1] => two
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Get as paths.
	 * $result = $object->propertySchema( '->' );
	 * print_r( $result );
	 * // Array
	 * // (
	 * //     [object] => Array
	 * //         (
	 * //             [0] => object
	 * //             [1] => list->object
	 * //             [2] => container->array->object
	 * //         )
	 * //     [one] => Array
	 * //         (
	 * //             [0] => array->one
	 * //             [1] => list->one
	 * //         )
	 * //     [two] => Array
	 * //         (
	 * //             [0] => array->two
	 * //             [1] => list->two
	 * //         )
	 * // )
	 * </code>
	 */
	public function propertySchema( string $theToken = NULL )
	{
		//
		// Init local storage.
		//
		$is_array = FALSE;
		$schema = $path = [];

		//
		// Iterate properties.
		//
		foreach( $this->asArray() as $key => $value )
			$this->traverseSchema( $schema, $path, $is_array, $key, $value );

		//
		// Sort schema.
		//
		ksort( $schema );
		foreach( array_keys( $schema ) as $offset )
			usort( $schema[ $offset ], function( $a, $b ) {
				if( count( $a ) == count( $b ) ) return 0;
				return ( count( $a ) > count( $b ) ) ? 1 : -1;
			});

		//
		// Convert to paths.
		//
		if( $theToken !== NULL )
			array_walk( $schema, function( & $element, $key, $token ){
				foreach( $element as $key => $value )
					$element[ $key ]
						= implode( $token, $value );
			}, $theToken);

		return $schema;																// ==>

	} // propertySchema.


	/*===================================================================================
	 *	asArray 																		*
	 *==================================================================================*/

	/**
	 * <h4>Return a copy converted to array.</h4><p />
	 *
	 * This method can be used to get a copy of the current object properties in which all
	 * <tt>Container</tt> and <tt>ArrayObject</tt> instances will be converted to arrays,
	 * including the root object.
	 *
	 * @return array				Converted array copy.
	 *
	 * @uses ConvertToArray()
	 *
	 * @example
	 * <code>
	 * // Test object
	 * $object = new Container( [
	 *     "container" => new ArrayObject( [
	 *         "array" => [
	 *             "object" => new Container( [
	 *                 1, 2, 3
	 *             ] )
	 *         ]
	 *     ] )
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [container] => ArrayObject Object
	 * //                 (
	 * //                     [storage:ArrayObject:private] => Array
	 * //                         (
	 * //                             [array] => Array
	 * //                                 (
	 * //                                     [object] => Milko\wrapper\Container Object
	 * //                                         (
	 * //                                             [mProperties:protected] => Array
	 * //                                                 (
	 * //                                                     [0] => 1
	 * //                                                     [1] => 2
	 * //                                                     [2] => 3
	 * //                                                 )
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Return a copy converted to array.
	 * $result = $object->asArray();
	 * // Array
	 * // (
	 * //     [container] => Array
	 * //         (
	 * //             [array] => Array
	 * //                 (
	 * //                     [object] => Array
	 * //                         (
	 * //                             [0] => 1
	 * //                             [1] => 2
	 * //                             [2] => 3
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 * </code>
	 */
	public function asArray()
	{
		//
		// Make a copy.
		//
		$copy = $this->mProperties;

		//
		// Convert to array.
		//
		self::convertToArray( $copy );

		return $copy;																// ==>

	} // asArray.


	/*===================================================================================
	 *	toArray 																		*
	 *==================================================================================*/

	/**
	 * <h4>Convert embedded ArrayObject properties to arrays.</h4><p />
	 *
	 * This method can be used to convert any embedded ArrayObject property to an array.
	 *
	 * @uses ConvertToArray()
	 *
	 * @example
	 * <code>
	 * // Test object
	 * $object = new Container( [
	 *     "container" => new ArrayObject( [
	 *         "array" => [
	 *             "object" => new Container( [
	 *                 1, 2, 3
	 *             ] )
	 *         ]
	 *     ] )
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [container] => ArrayObject Object
	 * //                 (
	 * //                     [storage:ArrayObject:private] => Array
	 * //                         (
	 * //                             [array] => Array
	 * //                                 (
	 * //                                     [object] => Milko\wrapper\Container Object
	 * //                                         (
	 * //                                             [mProperties:protected] => Array
	 * //                                                 (
	 * //                                                     [0] => 1
	 * //                                                     [1] => 2
	 * //                                                     [2] => 3
	 * //                                                 )
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Convert to array.
	 * $object->toArray();
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [container] => Array
	 * //                 (
	 * //                     [array] => Array
	 * //                         (
	 * //                             [object] => Array
	 * //                                 (
	 * //                                     [0] => 1
	 * //                                     [1] => 2
	 * //                                     [2] => 3
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 * </code>
	 */
	public function toArray()
	{
		self::convertToArray( $this->mProperties );

	} // toArray.



/*=======================================================================================
 *																						*
 *							STATIC SERIALISATION INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	convertToArray 																	*
	 *==================================================================================*/

	/**
	 * <h4>Convert embedded ArrayObjects to array.</h4><p />
	 *
	 * This method can be used to convert any embedded <tt>Container</tt> or
	 * <tt>ArrayObject</tt> instance in the provided parameter to an array, if the parameter
	 * is not an <tt>array</tt>, <tt>Container</tt> or <tt>ArrayObject</tt>, the method will
	 * do nothing. <em>The provided structure itself will also be converted to an
	 * array.</em>
	 *
	 * <em>Note that the conversion is performed on the provided reference, if you need the
	 * original value you must provide a copy to this method, or use the
	 * {@link convertToArrayCopy()} method.</em>
	 *
	 * @param mixed				   &$theStructure		Structure to convert.
	 *
	 * @uses getArrayCopy()
	 *
	 * @example
	 * <code>
	 * // Example structure.
	 * $object = new ArrayObject( [
	 *     "container" => new Container( [
	 *         "array" => [
	 *             "object" => new ArrayObject( [
	 *                 1, 2, 3
	 *             ] )
	 *         ]
	 *     ] );
	 *
	 * // ArrayObject Object
	 * // (
	 * //     [storage:ArrayObject:private] => Array
	 * //         (
	 * //             [container] => Milko\wrapper\Container Object
	 * //                 (
	 * //                     [mProperties:protected] => Array
	 * //                         (
	 * //                             [array] => Array
	 * //                                 (
	 * //                                     [object] => ArrayObject Object
	 * //                                         (
	 * //                                             [storage:ArrayObject:private] => Array
	 * //                                                 (
	 * //                                                     [0] => 1
	 * //                                                     [1] => 2
	 * //                                                     [2] => 3
	 * //                                                 )
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Convert embedded structures to array.
	 * Container::convertToArray( $object );
	 *
	 * // Array
	 * // (
	 * //     [container] => Array
	 * //         (
	 * //             [array] => Array
	 * //                 (
	 * //                     [object] => Array
	 * //                         (
	 * //                             [0] => 1
	 * //                             [1] => 2
	 * //                             [2] => 3
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 * </code>
	 */
	static function convertToArray( &$theStructure )
	{
		//
		// Handle structures.
		//
		if( is_array( $theStructure )
			|| ($theStructure instanceof \ArrayObject)
			|| ($theStructure instanceof self) )
		{
			//
			// Convert to array.
			//
			if( ! is_array( $theStructure ) )
				$theStructure = $theStructure->getArrayCopy();

			//
			// Iterate keys.
			//
			foreach( array_keys( $theStructure ) as $key )
				static::convertToArray( $theStructure[ $key ] );

		} // Provided a structure.

	} // convertToArray.


	/*===================================================================================
	 *	convertToArrayCopy																*
	 *==================================================================================*/

	/**
	 * <h4>Convert embedded ArrayObjects to array making a copy.</h4><p />
	 *
	 * This method is equivalent to the {@link convertToArray()} statis method, except that
	 * it will return a copy of the provided parameter as an array; the aforementioned
	 * method, instead, will convert the provided parameter to an array.
	 *
	 * The method will return a copy of the provided structure as an array.
	 *
	 * @param mixed				   &$theStructure		Structure to convert.
	 * @return array                The structure converted to array.
	 *
	 * @uses convertToArray()
	 */
	static function convertToArrayCopy( &$theStructure )
	{
		//
		// Make a copy of the parameter.
		//
		$copy = ( is_array( $theStructure ) )
			  ? $theStructure
			  : $theStructure->getArrayCopy();

		//
		// Convert to array.
		//
		static::convertToArray( $copy );

		return $copy;                                                               // ==>

	} // convertToArrayCopy.


	/*===================================================================================
	 *	isArray																			*
	 *==================================================================================*/

	/**
	 * <h4>Determine whether the parameter is an array.</h4><p />
	 *
	 * This method can be used to determine whether the provided parameter is an array: the
	 * method will return <tt>TRUE</tt> if the value is an <tt>array</tt> in the strict
	 * sense, meaning that the keys are an increasing <tt>0 .. n-1</tt> number series, this
	 * is also valid for <tt>ArrayObject</tt> and <tt>Container</tt> instances.
	 *
	 * @param mixed					$theValue			Value to probe.
	 * @return bool					<tt>TRUE</tt> means array.
	 *
	 * @example
	 * <code>
	 * // Will return TRUE.
	 * $result = Container::isArray( [ 1, 2, 3 ] );
	 * $result = Container::isArray( [ 0 => 1, 1 => 2, 2 => 3 ] );
	 * $result = Container::isArray( new ArrayObject( [ 1, 2, 3 ] ) );
	 * $result = Container::isArray( new Container( [ 1, 2, 3 ] ) );
	 * $result = Container::isArray( new ArrayObject( [ 0 => 1, 1 => 2, 2 => 3 ] ) );
	 *
	 * // Will return FALSE.
	 * $result = Container::isArray( [ 1 => 1 ] );
	 * $result = Container::isArray( [ 0 => 1, 2 => 2 ] );
	 * $result = Container::isArray( [ "one" => 1 ] );
	 * any scalar value...
	 * </code>
	 */
	static function isArray( $theValue  )
	{
		//
		// Convert structures.
		//
		if( ($theValue instanceof self)
			|| ($theValue instanceof \ArrayObject) )
			$theValue = $theValue->getArrayCopy();

		//
		// Handle arrays.
		//
		if( is_array( $theValue ) )
		{
			$keys = array_keys( $theValue );

			return ( $keys === array_keys( $keys ) );								// ==>

		} // Was a structure.

		return FALSE;																// ==>

	} // isArray.



/*=======================================================================================
 *																						*
 *								STATIC ATTRIBUTE INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	manageAttribute																	*
	 *==================================================================================*/

	/**
	 * <h4>Manage an attribute</h4><p />
	 *
	 * This library implements a standard interface for managing attributes using accessor
	 * methods, attributes are stored in the object's data members, this method implements
	 * this interface:
	 *
	 * <ul>
	 *	<li><tt>&$theMember</tt>: Reference to the object attribute being managed.
	 *	<li><tt>$theValue</tt>: The attribute value or operation:
	 *	 <ul>
	 *		<li><tt>NULL</tt>: Return the current attribute value.
	 *		<li><tt>FALSE</tt>: Reset the attribute to <tt>NULL</tt>.
	 *		<li><em>other</em>: Set the attribute with the provided value.
	 *	 </ul>
	 *	<li><tt>$doOld</tt>: Return value switch:
	 *	 <ul>
	 *		<li><tt>TRUE</tt>: Return the current value.
	 *		<li><tt>FALSE</tt>: Return the old value; irrelevant when returning current.
	 *	 </ul>
	 * </ul>
	 *
	 * @param mixed				   &$theMember			Reference to the data member.
	 * @param mixed					$theValue			Value or operation.
	 * @param bool					$doOld				<tt>TRUE</tt> return old value.
	 * @return mixed				Old or current attribute value.
	 *
	 * @example
	 * <code>
	 * // Example class.
	 * class Test extends Container {
	 * 	private $attr = NULL;
	 * 	public function Attribute( $val = NULL, $old = FALSE ) {
	 * 		return self::manageAttribute( $this->attr, $val, $old );
	 * 	}
	 * }
	 *
	 * // Example structure.
	 * $object = new Test();
	 *
	 * // Test Object
	 * // (
	 * // 	[attr:Test:private] => NULL
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 *
	 * // Get current value.
	 * $result = $object->Attribute();
	 * $result = $object->Attribute( NULL );
	 * // $result === NULL
	 *
	 * // Set attribute to "value" and return current value.
	 * $result = $object->Attribute( "value" );
	 * // $result == string(5) "value"
	 *
	 * // Set attribute to "new" and return old value.
	 * $result = $object->Attribute( "new", TRUE );
	 * // $result == string(5) "value"
	 * // Test Object
	 * // (
	 * // 	[attr:Test:private] => "new"
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 *
	 * // Reset attribute and return current value.
	 * $result = $object->Attribute( FALSE );
	 * // $result === NULL
	 *
	 * $result = $object->Attribute( "temp" );
	 * // Reset attribute and return old value.
	 * $result = $object->Attribute( FALSE, TRUE );
	 * // $result == string(4) "temp"
	 * // Test Object
	 * // (
	 * // 	[attr:Test:private] => NULL
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 * </code>
	 */
	static function manageAttribute( &$theMember, $theValue = NULL, bool $doOld = FALSE )
	{
		//
		// Return current value.
		//
		if( $theValue === NULL )
			return $theMember;														// ==>

		//
		// Save old value.
		//
		if( $doOld )
			$save = $theMember;

		//
		// Set or reset member.
		//
		$theMember = ( $theValue === FALSE )
				   ? NULL
				   : $theValue;

		//
		// Return old value.
		//
		if( $doOld )
			return $save;															// ==>

		return $theMember;															// ==>

	} // manageAttribute.


	/*===================================================================================
	 *	manageBitfieldAttribute															*
	 *==================================================================================*/

	/**
	 * <h4>Manage a bitfield attribute.</h4><p />
	 *
	 * This method can be used to manage a bitfield attribute, the method expects the
	 * following parameters:
	 *
	 * <ul>
	 * 	<li><b>&$theAttribute</b>: Reference of the attribute. The attribute value must be
	 * 		a <em>binary string</em>.
	 * 	<li><b>$theMask</b>: The flag mask or operation:
	 * 	 <ul>
	 * 		<li><tt>NULL</tt> Return the current attribute value (<em>binary string</em>),
	 * 			all following parameters will be ignored.
	 * 		<li><tt>string</tt>: Use value (<em>binary string</em>) as the mask.
	 * 	 </ul>
	 * 	<li><b>$theValue</b>: The new value or operation:
	 * 	 <ul>
	 * 		<li><tt>TRUE</tt> Turn on attribute bits corresponding to mask set bits.
	 * 		<li><tt>FALSE</tt>: Turn off attribute bits corresponding to mask set bits.
	 * 		<li><tt>NULL</tt>: Match attribute with mask bits and return <tt>TRUE</tt> if
	 * 			any bit of the combination is set, or <tt>FALSE</tt>.
	 * 	 </ul>
	 *	<li><tt>$doOld</tt>: Return value switch:
	 *	 <ul>
	 *		<li><tt>TRUE</tt>: Return the current value.
	 *		<li><tt>FALSE</tt>: Return the old value; irrelevant when returning current.
	 *	 </ul>
	 * </ul>
	 *
	 * When setting bits, if the size of the mask is <em>greater</em> than the current
	 * value, the resulting bitfield will have the size of the mask. When resetting bits, if
	 * the size of the mask is <em>smaller</em> than the current value, the resulting
	 * bitfield will have the size of the mask.
	 *
	 * @param string			   &$theAttribute		Bitfield attribute reference.
	 * @param string				$theMask			Flag mask.
	 * @param bool					$theValue			Switch sense or operation.
	 * @param bool					$doOld				<tt>TRUE</tt> return old value.
	 * @return string|bool			Switch value or mask boolean.
	 *
	 * @example
	 * <code>
	 * // Example class.
	 * class Test extends Container {
	 * 	private $attr = NULL;
	 * 	public function __construct( $props = NULL, bool $array = FALSE ) {
	 * 		// Construct parent.
	 * 		parent::__construct( $props, $array );
	 * 		// Initialise flag attribute.
	 * 		$this->attr = hex2bin( '00000000' );
	 * 	}
	 * 	public function Attribute( string $mask = NULL,
	 * 							   bool $value = NULL,
	 * 							   bool $old = FALSE ) {
	 * 		return self::manageBitfieldAttribute( $this->attr, $mask, $value, $old );
	 * 	}
	 * }
	 *
	 * // Example structure.
	 * $object = new Test();
	 *
	 * // Test Object
	 * // (
	 * // 	[attr:Test:private] => 0x00000000
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 *
	 * // Return current value.
	 * $state = $object->Attribute();
	 * // $state == 0x00000000;
	 *
	 * // Turn on masked bits in attribute and return current value.
	 * $state = $object->Attribute( hex2bin( 'ff0000ff' ), TRUE );
	 * // $state == 0xff0000ff;
	 * // Test Object
	 * // (
	 * // 	[attr:Test:private] => 0xff0000ff
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 *
	 * // Turn on masked bits in attribute and return old value.
	 * $state = $object->Attribute( hex2bin( '000ff000' ), TRUE, TRUE );
	 * // $state == 0xff0000ff;
	 * // Test Object
	 * // (
	 * // 	[attr:Test:private] => 0xff0ff0ff
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 *
	 * // Match attribute with mask.
	 * $state = $object->Attribute( hex2bin( '0000000f' ) );
	 * // $state == bool(true);
	 *
	 * // Match attribute with mask.
	 * $state = $object->Attribute( hex2bin( '00f00f00' ) );
	 * // $state == bool(false);
	 *
	 * // Turn off masked bits in attribute and return old value.
	 * $state = $object->Attribute( hex2bin( '00ffff00' ), FALSE, TRUE );
	 * // $state == 0xff0ff0ff;
	 * // Test Object
	 * // (
	 * // 	[attr:Test:private] => 0xff0000ff
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 *
	 * // Reduce mask size.
	 * $state = $object->Attribute( hex2bin( '00ff' ), FALSE );
	 * // Test Object
	 * // (
	 * // 	[attr:Test:private] => 0xff00
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 *
	 * // Increase mask size.
	 * $state = $object->Attribute( hex2bin( '00ff0000' ), TRUE );
	 * // Test Object
	 * // (
	 * // 	[attr:Test:private] => 0xffff0000
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 		)
	 * // )
	 *
	 * //
	 * // Note how I use hex functions: passing hex values will convert them to integers:
	 * // Depending on the machine byte order you will not be able to use the integer sign
	 * // bit.
	 * //
	 * </code>
	 */
	static function manageBitfieldAttribute( string &$theAttribute,
											 string  $theMask = NULL,
											 bool	 $theValue = NULL,
											 bool	 $doOld = FALSE )
	{
		//
		// Return current value.
		//
		if( $theMask === NULL )
			return $theAttribute;													// ==>

		//
		// Return masked state.
		//
		if( $theValue === NULL )
			return
				! ( ($result = ($theAttribute & $theMask))
					== str_repeat( "\0", strlen( $result ) ) );						// ==>

		//
		// Save previous value.
		//
		$save = $theAttribute;

		//
		// Set flag.
		//
		if( $theValue === TRUE )
			$theAttribute |= $theMask;

		//
		// Reset flag.
		//
		else
			$theAttribute &= (~$theMask);

		return ( $doOld )
			 ? $save																// ==>
			 : $theAttribute;														// ==>

	} // manageBitfieldAttribute.



/*=======================================================================================
 *																						*
 *							PROTECTED PROPERTY INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	manageProperty																	*
	 *==================================================================================*/

	/**
	 * <h4>Manage a property</h4><p />
	 *
	 * This library implements a standard interface for managing properties using accessor
	 * methods, properties are stored in the object's {@link mProperties} array, this method
	 * implements this interface:
	 *
	 * <ul>
	 *	<li><tt>$theOffset</tt>: Property offset:
	 *	 <ul>
	 *		<li><tt>NULL</tt>: Return the properties array, all following parameters will be
	 * 			ignored.
	 *		<li><em>other</em>: The property offset, see the <tt>$theOffset</tt> parameter
	 * 			of {@link offsetGet()}, {@link offsetSet()} and {@link offsetUnset()}.
	 *	 </ul>
	 *	<li><tt>$theValue</tt>: The property value or operation:
	 *	 <ul>
	 *		<li><tt>NULL</tt>: Return the current value.
	 *		<li><tt>FALSE</tt>: Delete the property.
	 *		<li><em>other</em>: Set the property with the provided value.
	 *	 </ul>
	 *	<li><tt>$doOld</tt>: Return value switch:
	 *	 <ul>
	 *		<li><tt>TRUE</tt>: Return the current value.
	 *		<li><tt>FALSE</tt>: Return the old value; irrelevant when returning current.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theOffset			Property offset.
	 * @param mixed					$theValue			Value or operation.
	 * @param bool					$doOld				<tt>TRUE</tt> return old value.
	 * @return mixed				Old or current property value.
	 *
	 * @uses offsetGet()
	 * @uses offsetSet()
	 * @uses offsetUnset()
	 *
	 * @example
	 * <code>
	 * // Example class.
	 * class Test extends Container {
	 * 	public function Property( $off = NULL, $val = NULL, bool $old = FALSE ) {
	 * 		return $this->manageProperty( $off, $val, $old );
	 * 	}
	 * }
	 *
	 * // Instantiate class.
	 * $object = new Test();
	 *
	 * // Set "offset" to "value".
	 * $result = $object->Property( "offset", "value" );
	 * // $result = string(5) "value";
	 * // Test Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[offset] => value
	 * // 		)
	 * // )
	 *
	 * // Set "offset" to "new" and return old value.
	 * $result = $object->Property( "offset", "new", TRUE );
	 * // $result = string(5) "value";
	 * // Test Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[offset] => new
	 * // 		)
	 * // )
	 *
	 * // Get "offset" property.
	 * $result = $object->Property( "offset" );
	 * // $result = string(3) "new";
	 *
	 * // Get all properties.
	 * $result = $object->Property();
	 * // Array
	 * // (
	 * // 	[offset] => new
	 * // )
	 *
	 * // Set $object[1][2] to "nested" and return old value.
	 * $result = $object->Property( [ 1, 2 ], "nested", TRUE );
	 * // $result === NULL;
	 * // Test Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => new
	 * //             [1] => Array
	 * //                 (
	 * //                     [2] => nested
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Delete $object[1][2].
	 * $result = $object->Property( [ 1, 2 ], FALSE );
	 * // $result === NULL;
	 * // Test Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => new
	 * //         )
	 * // )
	 *
	 * // Delete $object[ "offset" ] and return old value.
	 * $result = $object->Property( "offset", FALSE, TRUE );
	 * // $result === string(3) "new";
	 * // Test Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //         )
	 * // )
	 * </code>
	 */
	protected function manageProperty( 	 	$theOffset = NULL,
									   	 	$theValue = NULL,
									   bool $doOld = FALSE )
	{
		//
		// Return all properties.
		//
		if( $theOffset === NULL )
			return $this->mProperties;												// ==>

		//
		// Save old value.
		//
		$save = $this->offsetGet( $theOffset );

		//
		// Return current value.
		//
		if( $theValue === NULL )
			return $save;															// ==>

		//
		// Reset property.
		//
		if( $theValue === FALSE )
		{
			//
			// Reset property.
			//
			$this->offsetUnset( $theOffset );

			if( ! $doOld )
				return NULL;														// ==>
		}

		//
		// Set property.
		//
		else
		{
			//
			// Set property.
			//
			$this->offsetSet( $theOffset, $theValue );

			if( ! $doOld )
				return $theValue;													// ==>
		}

		return $save;																// ==>

	} // manageProperty.


	/*===================================================================================
	 *	manageBitfieldProperty															*
	 *==================================================================================*/

	/**
	 * <h4>Manage a bitfield property</h4><p />
	 *
	 * This method can be used to manage a bitfield attribute, the method expects the
	 * following parameters:
	 *
	 * <ul>
	 *	<li><tt>$theOffset</tt>: Property offset in the same format as for
	 * 		{@link offsetGet()} and {@link offsetSet()}; if the offset is not found, the
	 * 		method will return <tt>NULL</tt>.
	 * 	<li><b>$theMask</b>: The flag mask or operation:
	 * 	 <ul>
	 * 		<li><tt>NULL</tt> Return the current property value (<em>binary string</em>),
	 * 			all following parameters will be ignored.
	 * 		<li><tt>string</tt>: Use value (<em>binary string</em>) as the mask.
	 * 	 </ul>
	 * 	<li><b>$theValue</b>: The new value or operation:
	 * 	 <ul>
	 * 		<li><tt>TRUE</tt> If the offset exists turn on property bits corresponding to
	 * 			mask set bits; if the offset doesn't exist create a property with the mask
	 * 			set bits.
	 * 		<li><tt>FALSE</tt> If the offset exists turn off property bits corresponding to
	 * 			mask set bits; if the offset doesn't exist create a property with the mask
	 * 			inverted bits.
	 * 		<li><tt>NULL</tt>: Match property with mask bits and return <tt>TRUE</tt> if
	 * 			any bit of the combination is set, <tt>FALSE</tt> if not and <tt>NULL</tt>
	 * 			if the offset doesn't exist.
	 * 	 </ul>
	 *	<li><tt>$doOld</tt>: Return value switch:
	 *	 <ul>
	 *		<li><tt>TRUE</tt>: Return the current value.
	 *		<li><tt>FALSE</tt>: Return the old value; irrelevant when returning current.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theOffset			Property offset.
	 * @param string				$theMask			Flag mask.
	 * @param bool					$theValue			Switch sense or operation.
	 * @param bool					$doOld				<tt>TRUE</tt> return old value.
	 * @return string|bool|NULL		Switch value, mask boolean or <tt>NULL</tt>.
	 *
	 * @uses offsetGet()
	 * @uses offsetSet()
	 *
	 * @example
	 * <code>
	 * // Example class.
	 * class Test extends Container {
	 * 	public function Property( $off,
	 * 							  string $mask = NULL,
	 * 							  bool  $value = NULL,
	 * 							  bool $old = FALSE ) {
	 * 		return $this->manageBitfieldProperty( $off, $mask, $value, $old );
	 * 	}
	 * }
	 *
	 * // Example structure.
	 * $object = new Test();
	 *
	 * // Return current value.
	 * $state = $object->Property( "flag" );
	 * // $state === NULL;
	 *
	 * // Set property with mask and return current value.
	 * $state = $object->Property( "flag", hex2bin( 'ff0000ff' ), TRUE );
	 * // $state == 0xff0000ff;
	 * // Test Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * //			[flag] => 0xff0000ff
	 * // 		)
	 * // )
	 *
	 * // Set property with inverted mask and return old value.
	 * $state = $object->Property( "flag", hex2bin( '00ffff00' ), FALSE, TRUE );
	 * // $state == 0xff0000ff;
	 * // Test Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * //			[flag] => 0xff0000ff
	 * // 		)
	 * // )
	 *
	 * // Turn on masked bits in attribute and return old value.
	 * $state = $object->Property( "flag", hex2bin( '000ff000' ), TRUE, TRUE );
	 * // $state == 0xff0000ff;
	 * // Test Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * //			[flag] => 0xff0ff0ff
	 * // 		)
	 * // )
	 *
	 * // Match attribute with mask.
	 * $state = $object->Property( "flag", hex2bin( '0000000f' ) );
	 * // $state == bool(true);
	 *
	 * // Match attribute with mask.
	 * $state = $object->Property( "flag", hex2bin( '00f00f00' ) );
	 * // $state == bool(false);
	 *
	 * // Match attribute with non existant offset.
	 * $state = $object->Property( "UNKNOWN", hex2bin( '00f00f00' ) );
	 * // $state === NULL;
	 *
	 * // Turn off masked bits in attribute and return old value.
	 * $state = $object->Property( "flag", hex2bin( '00ffff00' ), FALSE, TRUE );
	 * // $state == 0xff0ff0ff;
	 * // Test Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * //			[flag] => 0xff0000ff
	 * // 		)
	 * // )
	 *
	 * //
	 * // Note how I use hex functions: passing hex values will convert them to integers:
	 * // Depending on the machine byte order you will not be able to use the integer sign
	 * // bit.
	 * //
	 * </code>
	 */
	protected function manageBitfieldProperty(		  $theOffset,
											   string $theMask = NULL,
											   bool   $theValue = NULL,
											   bool   $doOld = FALSE )
	{
		//
		// Save current value.
		//
		$save = $this->offsetGet( $theOffset );

		//
		// Return current value.
		//
		if( $theMask === NULL )
			return $save;															// ==>

		//
		// Return masked state.
		//
		if( $theValue === NULL )
		{
			if( $save !== NULL )
				return
					! ( ($result = ($save & $theMask))
						== str_repeat( "\0", strlen( $result ) ) );					// ==>

			return NULL;															// ==>
		}

		//
		// Set flag.
		//
		if( $theValue === TRUE )
		{
			if( $save !== NULL )
				$this->offsetSet( $theOffset, $save | $theMask );
			else
				$this->offsetSet( $theOffset, $theMask );
		}

		//
		// Reset flag.
		//
		else
		{
			if( $save !== NULL )
				$this->offsetSet( $theOffset, $save & (~$theMask) );
			else
				$this->offsetSet( $theOffset, ~$theMask );
		}

		return ( $doOld )
			? $save																	// ==>
			: $this->offsetGet( $theOffset );										// ==>

	} // manageBitfieldProperty.



/*=======================================================================================
 *																						*
 *									PROTECTED INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	nestedPropertyReference															*
	 *==================================================================================*/

	/**
	 * <h4>Return the reference of a nested property.</h4><p />
	 *
	 * This method expects a list of offsets that represents the depth levels of the
	 * structure, the method will traverse the structure and return the reference of the
	 * last matched property and strip from the provided offsets array all matched offsets.
	 *
	 * The method will function as follows:
	 *
	 * <ul>
	 * 	<li><em>Provided an empty list</em>:
	 * 	 <ul>
	 * 		<li><em>Reference</em>: Root properties structure.
	 * 		<li><em>List</em>: Will be set to <tt>NULL</tt>.
	 * 	 </ul>
	 * 	<li><em>All offsets match</em>:
	 * 	 <ul>
	 * 		<li><em>Reference</em>: Reference to leaf offset property.
	 * 		<li><em>List</em>: Will be an empty array.
	 * 	 </ul>
	 * 	<li><em>At least one offset doesn't match</em>:
	 * 	 <ul>
	 * 		<li><em>Reference</em>: Reference to the last matching property.
	 * 		<li><em>List</em>: Will start with the first non matching offset (matching
	 * 			offsets stripped from list).
	 * 	 </ul>
	 * 	<li><em>The first offset doesn't match</em>:
	 * 	 <ul>
	 * 		<li><em>Reference</em>: Root properties structure.
	 * 		<li><em>List</em>: Will remain unchanged.
	 * 	 </ul>
	 * </ul>
	 *
	 * If the second parameter is set to <tt>TRUE</tt>, the method will return the parent
	 * reference and set the list to the leaf offset:
	 *
	 * <ul>
	 * 	<li><em>Provided an empty list</em>:
	 * 	 <ul>
	 * 		<li><em>Reference</em>: Root properties structure.
	 * 		<li><em>List</em>: Will be set to <tt>NULL</tt>.
	 * 	 </ul>
	 * 	<li><em>All offsets match</em>:
	 * 	 <ul>
	 * 		<li><em>Reference</em>: Reference to parent offset property.
	 * 		<li><em>List</em>: Will remain unchanged.
	 * 	 </ul>
	 * 	<li><em>At least one offset doesn't match</em>:
	 * 	 <ul>
	 * 		<li><em>Reference</em>: Reference to the last matching property.
	 * 		<li><em>List</em>: Will be set to an empty array.
	 * 	 </ul>
	 * 	<li><em>The first offset doesn't match</em>:
	 * 	 <ul>
	 * 		<li><em>Reference</em>: Root properties structure.
	 * 		<li><em>List</em>: Will be set to an empty array.
	 * 	 </ul>
	 * </ul>
	 *
	 * Essentially, when the second parameter is <tt>FALSE</tt> you have a full match if
	 * the list is set to an empty array; when the second parameter is set to <tt>TRUE</tt>,
	 * if the list is set to an array and it is not empty.
	 *
	 * If any of the elements of the offsets list is not a scalar or <tt>NULL</tt>, the
	 * method will raise an exception.
	 *
	 * @param mixed				   &$theOffsets			Offsets list.
	 * @param bool					$getParent			<tt>TRUE</tt> return parent.
	 * @return mixed				The property reference.
	 * @throws \InvalidArgumentException
	 *
	 * @example
	 * <code>
	 * // Example class.
	 * class Test extends Container {
	 * 	public function & Property( array & $off, bool $par = FALSE ) {
	 * 		return $this->nestedPropertyReference( $off, $par );
	 * 	}
	 * }
	 *
	 * // Example structure.
	 * $object = new Test( [
	 * 	"list" => [ 1, 2 ],
	 * 	"nested" => [
	 * 		1 => [
	 * 			2 => new ArrayObject( [
	 * 				3 => "three" ] )
	 * 		]
	 * 	]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[list] => Array
	 * // 				(
	 * // 					[0] => 1
	 * // 					[1] => 2
	 * // 				)
	 * // 			[nested] => Array
	 * // 				(
	 * // 					[1] => Array
	 * // 						(
	 * // 							[2] => ArrayObject Object
	 * // 								(
	 * // 									[storage:ArrayObject:private] => Array
	 * // 										(
	 * // 											[3] => three
	 * // 										)
	 * // 								)
	 * // 						)
	 * // 				)
	 * // 		)
	 * // )
	 *
	 * //
	 * // Get reference.
	 * //
	 *
	 * // Will return a reference to $object->mProperties.
	 * $offsets = [];
	 * $reference = & $object->Property( $offsets );
	 * // $reference == & $object->mProperties
	 * // !!! You can only set the $reference to an array !!!
	 * // $offsets == [];
	 *
	 * // All offsets match.
	 * $offsets = [ "nested", 1, 2, 3 ];
	 * $reference = & $object->Property( $offsets );
	 * // $reference == "three"; (& $object["nested"][1][2][3])
	 * // $offsets == [];
	 *
	 * // At least one offset doesn't match.
	 * $offsets = [ "nested", 1, "UNKNOWN", 3 ];
	 * $reference = & $object->Property( $offsets );
	 * // $reference: (& $object["nested"][1])
	 * // $offsets == [ "UNKNOWN", 3 ];
	 *
	 * // First offset doesn't match.
	 * $offsets = [ "UNKNOWN", 1, 2, 3 ];
	 * $reference = & $object->Property( $offsets );
	 * // $reference == & $object->mProperties
	 * // !!! You can only set the $reference to an array !!!
	 * // $offsets remains unchanged.
	 *
	 * //
	 * // Get parent reference.
	 * //
	 *
	 * // Will return a reference to $object->mProperties.
	 * $offsets = [];
	 * $reference = & $object->Property( $offsets, TRUE );
	 * // $reference == & $object->mProperties
	 * // !!! You can only set the $reference to an array !!!
	 * // $offsets == NULL;
	 *
	 * // All offsets match.
	 * $offsets = [ "nested", 1, 2, 3 ];
	 * $reference = & $object->Property( $offsets, TRUE );
	 * // $reference == "three"; (& $object["nested"][1][2])
	 * // $offsets remain unchanged.
	 *
	 * // At least one offset doesn't match.
	 * $offsets = [ "nested", 1, "UNKNOWN", 3 ];
	 * $reference = & $object->Property( $offsets, TRUE );
	 * // $reference: (& $object["nested"][1])
	 * // $offsets == [];
	 *
	 * // First offset doesn't match.
	 * $offsets = [ "UNKNOWN", 1, 2, 3 ];
	 * $reference = & $object->Property( $offsets, TRUE );
	 * // $reference == & $object->mProperties
	 * // !!! You can only set the $reference to an array !!!
	 * // $offsets == [];
	 * </code>
	 */
	protected function & nestedPropertyReference( array & $theOffsets,
												  bool	  $getParent = FALSE )
	{
		//
		// Init local storage.
		//
		$reference = & $this->mProperties;
		$save = & $this->mProperties;

		//
		// Handle empty list.
		//
		if( ! count( $theOffsets ) )
		{
			$theOffsets = NULL;

			return $reference;														// ==>
		}

		//
		// Scan offsets.
		//
		for( $i = 0; $i < count( $theOffsets ); $i++ )
		{
			//
			// Init local storage.
			//
			$save = & $reference;
			$offset = $theOffsets[ $i ];

			//
			// Check append.
			//
			if( $offset === NULL )
				break;															// =>

			//
			// Check offset.
			//
			if( ! is_scalar( $offset ) )
				throw new \InvalidArgumentException(
					"Provided non scalar nested offset." );						// !@! ==>

			//
			// Handle containers.
			//
			if( $reference instanceof self )
				$reference = & $reference->mProperties;

			//
			// Check property offset.
			//
			if( is_array( $reference ) )
				$found = array_key_exists( $offset, $reference );
			elseif( $reference instanceof \ArrayObject )
				$found = $reference->offsetExists( $offset );
			else
				$found = FALSE;

			//
			// Handle unknown offset.
			//
			if( ! $found )
				break;															// ==>

			//
			// Reference property.
			//
			$reference = & $reference[ $offset ];

		} // Traversing structure.

		//
		// Handle parent reference.
		//
		if( $getParent )
		{
			//
			// Update list.
			//
			if( $i < count( $theOffsets ) )
				$theOffsets = [];

			return $save;															// ==>

		} // Return parent reference.

		//
		// Clear save.
		//
		unset( $save );

		//
		// Update list.
		//
		$theOffsets = array_slice( $theOffsets, $i );

		return $reference;															// ==>

	} // nestedPropertyReference.


	/*===================================================================================
	 *	traverseSchema																	*
	 *==================================================================================*/

	/**
	 * <h4>Traverse schema and load property offsets.</h4><p />
	 *
	 * This method can be used to return the list of leaf offset paths, that is, the path
	 * to the structure offsets that represent scalar leaf values in the structure.
	 *
	 * The method expects the following parameters:
	 *
	 * <ul>
	 * 	<li><b>$theSchema</b>: It is an array that will be populated bu the method, the
	 * 		first time the method is called it should be empty, its elements will be
	 * 		structured as follows:
	 * 	 <ul>
	 * 		<li><i>key</i>: The leaf offset name.
	 * 		<li><i>value</i>: An array of arrays containing all the paths in the structure
	 * 			leading to the offset in the key.
	 * 	 </ul>
	 * 	<li><b>$thePath</b>: It is a stack that will be used by the method to collect path
	 * 		elements, it should be empty the first time the method is called.
	 * 	<li><b>$isArray</b>: It is a boolean value indicating whether the parent structure
	 * 		is a strict array or not. By strict we mean an array whose keys are a
	 * 		consecutive sequence of numbers with the <tt>0 .. n-1</tt> range.
	 * 	<li><b>$theKey</b>: The current structure element key.
	 * 	<li><b>$theValue</b>: The current structure element value.
	 * </ul>
	 *
	 * The resulting paths will contain only offsets from structure elements and offsets of
	 * scalar values.
	 *
	 * The method is used by {@link propertySchema()} to retrieve the object's properties
	 * schema, you can use this method to traverse other structures if needed.
	 *
	 * See {@link propertySchema()} for an example of how it functions.
	 *
	 * @param array				   &$theSchema			Receives schema.
	 * @param array				   &$thePath			Receives offsets path.
	 * @param bool					$isArray			<tt>TRUE</tt> parent is array.
	 * @param mixed					$theKey				Current property offset.
	 * @param mixed					$theValue			Current property value.
	 *
	 * @uses is_array()
	 */
	protected function traverseSchema( array & $theSchema,
									   array & $thePath,
									   bool	   $isArray,
									   		   $theKey,
									   		   $theValue )
	{
		//
		// Convert structures.
		//
		if( ($theValue instanceof self)
		 || ($theValue instanceof \ArrayObject) )
			$theValue = $theValue->getArrayCopy();

		//
		// Handle structures.
		//
		if( is_array( $theValue ) )
		{
			//
			// Save path.
			//
			$path = $thePath;

			//
			// Add key to path.
			//
			if( ! $isArray )
				$thePath[] = $theKey;

			//
			// Iterate array.
			//
			foreach( $theValue as $key => $value )
				$this->traverseSchema(
					$theSchema, $thePath, static::isArray( $theValue ), $key, $value );

			//
			// Reset path.
			//
			$thePath = $path;

		} // Structure.

		//
		// Handle leaf.
		//
		else
		{
			//
			// Determine final path.
			//
			$path = ( $isArray )
				  ? $thePath
				  : array_merge( $thePath, [ $theKey ] );

			//
			// Determine leaf offset.
			//
			$offset = $path[ count( $path ) - 1 ];

			//
			// Add path to schema.
			//
			if( ! array_key_exists( $offset, $theSchema ) )
				$theSchema[ $offset ] = [ $path ];
			elseif( array_search( $path, $theSchema[ $offset ], TRUE ) === FALSE )
				$theSchema[ $offset ][] = $path;

		} // Leaf offset.

	} // traverseSchema.




} // class Container.


?>
