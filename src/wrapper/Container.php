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
 * This class is a custom implementation of ArrayObject that uses an array data member
 * rather than the ArrayObject private member. This is because we need references to array
 * members, a thing that ArrayObject does not provide.
 *
 * The <em>attributes</em> of the object represent transient information which is private
 * to the object itself, this data is stored in the object's data members and is not
 * considered by the persistent framework of this library.
 *
 * The <em>properties</em> of the object represent the persistent information carried by
 * the object, this data is stored in the array data member and are accessed through the
 * various interfaces implemented by this class. The persistence framework of this
 * library uses this data.
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
 *	@package	Core
 *
 *	@author		Milko Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		02/08/2016
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
	 * to an array ({@link ConvertToArray()}.
	 *
	 * @param mixed					$theProperties		Properties or <tt>NULL</tt>.
	 * @param bool					$asArray			<tt>TRUE</tt> convert to array.
	 * @throws \InvalidArgumentException
	 *
	 * @uses ConvertToArray()
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
					static::ConvertToArray( $theProperties );

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
	 * We overload this method to handle the case in which the offset does not exist: if
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
	 * <em>The <tt>NULL</tt> offset is used to append an element in {@link offsetSet()}, it
	 * is handled in this method for consistency.</em>
	 *
	 * @param mixed					$theOffset			Offset.
	 * @return mixed				Offset value or <tt>NULL</tt>.
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
	 * We overload this method to handle the <tt>NULL</tt> value in the <tt>$theValue</tt>
	 * parameter: if the offset exists it will be deleted, if not, the method will do
	 * nothing.
	 *
	 * The method expects the following parameters:
	 *
	 * <ul>
	 * 	<li><b>$theOffset</b>: The offset to set:
	 *	 <ul>
	 *	 	<li><tt>NULL</tt>: Will append the value to the structure.
	 *	 	<li><tt>scalar</tt>: Will create or set the offset at the top structure level
	 * 			with the provided value.
	 * 		<li><i>list</i>: Will traverse the structure using the provided sequence of
	 * 			offsets and create or set the nested structure offset with the provided
	 * 			value. <em>Any intermediate level offset that doesn't yet exist will be
	 * 			initialised as an <tt>array</tt></em>. If the list is empty the method will
	 * 			do nothing. The list must be provided as an <tt>array</tt>,
	 * 			<tt>Container</tt> or an <tt>ArrayObject</tt>, any other type will raise an
	 * 			exception. If any element of the list is not a scalar or <tt>NULL</tt> the
	 * 			method will trigger an exception.
	 *	 </ul>
	 * 	<li><b>$theValue</b>: The value to set:
	 *	 <ul>
	 *	 	<li><tt>NULL</tt>: Will delete the property referenced by the provided offset,
	 * 			if found: see {@link offsetUnset()}.
	 *	 	<li><i>other</i>: Will set the property referenced by the provided offset with
	 * 			the provided value.
	 *	 </ul>
	 * </ul>
	 *
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value to set at offset.
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
	 * // Set an object property.
	 * // $object[ explode( '.', "object.property.path" ) ] = "value";
	 * $object->offsetSet( explode( '.', "object.property.path" ), "value" );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * // 	[mProperties:protected] => Array
	 * // 		(
	 * // 			[offset] => value
	 * // 			[1] => Array
	 * // 				(
	 * // 					[2] => Array
	 * // 						(
	 * // 							[3] => value
	 * // 						)
	 * // 				)
	 * // 			[object] => Array
	 * // 				(
	 * // 					[property] => Array
	 * // 						(
	 * // 							[path] => value
	 * // 						)
	 * // 				)
	 * // 		)
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
	 * // Delete $object["object"]["property"]["path"],
	 * // $object["property"]["path"] and $object["object"] will also be deleted,
	 * // because they would become empty.
	 * // $object[ explode( '.', "object.property.path" ) ] = NULL;
	 * $object->offsetSet( explode( '.', "object.property.path" ), NULL );
	 * // Equivalent to offsetUnset( explode( '.', "object.property.path" ) );
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
								$offset = ( is_array( $reference ) )
										? count( $reference )
										: $reference->count();

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
	 * We overload this method to prevent warnings when a non-existing offset is provided,
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
	 * ...
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
 *								CUSTOM ARRAY INTERFACE									*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	array_keys																		*
	 *==================================================================================*/

	/**
	 * <h4>Return the property offsets.</h4><p />
	 *
	 * This method will return an array with the list of property offsets at the top
	 * structure level.
	 *
	 * <em>Note: I was unable to use the documented default parameters of the array_keys()
	 * function: whenever added, the function would not return any keys.</em>
	 *
	 * @return array				List of keys.
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
	 * // Get property offsets.
	 * $offsets = $object->array_keys();
	 *
	 * // Array
	 * // (
	 * // 	[0] => offset
	 * // 	[1] => list
	 * // 	[2] => nested
	 * // )
	 * </code>
	 */
	public function array_keys()
	{
		return array_keys( $this->mProperties );									// ==>

	} // array_keys.


	/*===================================================================================
	 *	array_values																	*
	 *==================================================================================*/

	/**
	 * <h4>Return the property values as an array.</h4><p />
	 *
	 * This method will return an array with all the property values, the property offsets
	 * at the top level will be replaced with the <tt>0 .. n-1</tt> array keys.
	 *
	 * @return array				List of values.
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
	 * // Get property values.
	 * $values = $object->array_values();
	 *
	 * // Array
	 * // (
	 * // 	[0] => value
	 * // 	[1] => Array
	 * // 		(
	 * // 			[0] => 1
	 * // 			[1] => 2
	 * // 		)
	 * // 	[2] => Array
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
	public function array_values()
	{
		return array_values( $this->mProperties );									// ==>

	} // array_values.


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
 *							PUBLIC SERIALISATION INTERFACE								*
 *																						*
 *======================================================================================*/



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
	 * @uses getArrayCopy()
	 * @uses ConvertToArray()
	 *
	 * @example
	 * <code>
	 * $object = new Container( [
	 * 	"container" => new ArrayObject( [
	 * 		"array" => [
	 * 			"object" => new Container( [
	 * 				1, 2, 3
	 * 			] )
	 * 		]
	 * 	] )
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
	 *
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
	public function asArray()
	{
		//
		// Make a copy.
		//
		$copy = $this->mProperties;

		//
		// Convert to array.
		//
		self::ConvertToArray( $copy );

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
	 * @uses propertyReference()
	 *
	 * @example
	 * <code>
	 * $object = new Container( [
	 * 	"container" => new ArrayObject( [
	 * 		"array" => [
	 * 			"object" => new Container( [
	 * 				1, 2, 3
	 * 			] )
	 * 		]
	 * 	] )
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
	 *
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
		self::ConvertToArray( $this->propertyReference() );

	} // toArray.



/*=======================================================================================
 *																						*
 *							STATIC SERIALISATION INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	ConvertToArray 																	*
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
	 * original value you must provide a copy to this method.</em>
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
	 * Container::ConvertToArray( $object );
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
	static function ConvertToArray( &$theStructure )
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
				static::ConvertToArray( $theStructure[ $key ] );

		} // Provided a structure.

	} // ConvertToArray.



/*=======================================================================================
 *																						*
 *						PROTECTED ATTRIBUTE MANAGEMENT INTERFACE						*
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
	 * 		return $this->manageAttribute( $this->attr, $val, $old );
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
	protected function manageAttribute( &$theMember, $theValue = NULL, bool $doOld = FALSE )
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
	 * @param bool					$theValue			New value or operation.
	 * @param bool					$doOld				<tt>TRUE</tt> return old value.
	 * @return string				Current or previous attribute switch value.
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
	 * 		return $this->manageBitfieldAttribute( $this->attr, $mask, $value, $old );
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
	protected function manageBitfieldAttribute( string &$theAttribute,
												string  $theMask = NULL,
												bool	$theValue = NULL,
												bool	$doOld = FALSE )
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
				! ( ($result = $theAttribute & $theMask)
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
	protected function manageProperty( 		$theOffset = NULL,
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
	 * 		{@link offsetGet()} and {@link offsetSet()}.
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
	 * @param bool					$theValue			New value or operation.
	 * @param bool					$doOld				<tt>TRUE</tt> return old value.
	 * @return string				Current or previous property switch value.
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
	protected function manageBitfieldProperty( 		  $theOffset,
											   string $theMask = NULL,
													  $theValue = NULL,
											   bool	  $doOld = FALSE )
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
					! ( ($result = $save & $theMask)
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
	 * @uses offsetExists()
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
			// Check property.
			//
//			if( ! array_key_exists( $offset, $reference ) )
//				break;															// =>
			if( $reference instanceof self )
				$reference = & $reference->mProperties;
			if( is_array( $reference ) )
				$found = array_key_exists( $offset, $reference );
			elseif( $reference instanceof \ArrayObject )
				$found = $reference->offsetExists( $offset );
			else
				$found = FALSE;
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




} // class Container.


?>
