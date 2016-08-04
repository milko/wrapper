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
	 * $test = new Container();
	 *
	 * // With array.
	 * $test = new Container( [1,2,3] );
	 *
	 *  // With Container.
	 * $test = new Container( new Container( [1,2,3] ) );
	 *
	 * // With ArrayObject.
	 * $test = new Container( new ArrayObject( [1,2,3] ) );
	 *
	 * // With ArrayObject converted to array.
	 * $test = new Container( new ArrayObject( [1,2,3] ), TRUE );
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
	 * 	"nested" => [ 1 => [ 2 => new ArrayObject( [ 3 => "three" ] ) ] ]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => value
	 * //             [list] => Array
	 * //                 (
	 * //                     [0] => 1
	 * //                     [1] => 2
	 * //                 )
	 * //             [nested] => Array
	 * //                 (
	 * //                     [1] => Array
	 * //                         (
	 * //                             [2] => ArrayObject Object
	 * //                                 (
	 * //                                     [storage:ArrayObject:private] => Array
	 * //                                         (
	 * //                                             [3] => three
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
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
	 * 	"nested" => [ 1 => [ 2 => new ArrayObject( [ 3 => "three" ] ) ] ]
	 * ] );
	 *
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => value
	 * //             [list] => Array
	 * //                 (
	 * //                     [0] => 1
	 * //                     [1] => 2
	 * //                 )
	 * //             [nested] => Array
	 * //                 (
	 * //                     [1] => Array
	 * //                         (
	 * //                             [2] => ArrayObject Object
	 * //                                 (
	 * //                                     [storage:ArrayObject:private] => Array
	 * //                                         (
	 * //                                             [3] => three
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
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
	 * $test = new Container();
	 *
	 * // Set $test[ "offset' ] with "value".
	 * $test->offsetSet( "offset", "value" );
	 *
	 * // Set $test[1][2][3] with "value", $test[1][2] and $test[1] will be arrays.
	 * $test->offsetSet( [ 1, 2, 3 ], "value" );
	 *
	 * // Delete $test[ "offset' ].
	 * $test->offsetSet( "offset", NULL );
	 * // Equivalent to offsetUnset( "offset" );
	 *
	 * // Delete $test[1][2][3],
	 * // $test[1][2] and $test[1] will also be deleted,
	 * // because they would become empty.
	 * $test->offsetSet( [ 1, 2, 3 ], NULL );
	 * // Equivalent to offsetUnset( [ 1, 2, 3 ] );
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
	 * 	"nested" => [ 1 => [ 2 => new ArrayObject( [ 3 => "three" ] ) ] ]
	 * ] );
	 *
	 * * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => value
	 * //             [list] => Array
	 * //                 (
	 * //                     [0] => 1
	 * //                     [1] => 2
	 * //                 )
	 * //             [nested] => Array
	 * //                 (
	 * //                     [1] => Array
	 * //                         (
	 * //                             [2] => ArrayObject Object
	 * //                                 (
	 * //                                     [storage:ArrayObject:private] => Array
	 * //                                         (
	 * //                                             [3] => three
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Will delete the "offset" property.
	 * $object->offsetUnset( "offset" );
	 *
	 * // Will not raise an alert.
	 * $object->offsetUnset( "UNKNOWN" );
	 *
	 * // Will delete the $object[ "list" ][ 0 ] property.
	 * $object->offsetUnset( [ "list", 0 ] );
	 *
	 * // Will delete the $object[ "nested" ][ 1 ][ 2 ][ 3 ] property
	 * // and all properties including "nested", since they would be empty.
	 * $object->offsetUnset( [ "nested", 1, 2, 3 ] );
	 *
	 * // Resulting object:
	 * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [list] => Array
	 * //                 (
	 * //                     [1] => 2
	 * //                 )
	 * //         )
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
	 * This method will return an array containing a copy of the properties of the object.
	 *
	 * @return array				Array copy.
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
	 * 	"nested" => [ 1 => [ 2 => new ArrayObject( [ 3 => "three" ] ) ] ]
	 * ] );
	 *
	 * * // Milko\wrapper\Container Object
	 * // (
	 * //     [mProperties:protected] => Array
	 * //         (
	 * //             [offset] => value
	 * //             [list] => Array
	 * //                 (
	 * //                     [0] => 1
	 * //                     [1] => 2
	 * //                 )
	 * //             [nested] => Array
	 * //                 (
	 * //                     [1] => Array
	 * //                         (
	 * //                             [2] => ArrayObject Object
	 * //                                 (
	 * //                                     [storage:ArrayObject:private] => Array
	 * //                                         (
	 * //                                             [3] => three
	 * //                                         )
	 * //                                 )
	 * //                         )
	 * //                 )
	 * //         )
	 * // )
	 *
	 * // Will return a reference to $test->mProperties.
	 * $result = & $test->propertyReference();
	 * $result = & $test->propertyReference( NULL );
	 * $result = & $test->propertyReference( [] );
	 *
	 * // Will return a reference to $test[ "offset" ].
	 * $result = & $test->propertyReference( "offset" );
	 * if( $result !== NULL )
	 * {
	 *     // Will set $test[ "offset" ] to "changed".
	 *     $result = "changed";
	 * }
	 *
	 * // $result will be NULL.
	 * $result = & $test->propertyReference( "UNKNOWN" );
	 * // Never use $result in this case!
	 *
	 * // Will return a reference to $test["nested"][1][2][3].
	 * $result = $test->offsetGet( [ "nested", 1, 2, 3 ] );
	 * if( $result !== NULL )
	 * {
	 *     // Will set $test["nested"][1][2][3] to "changed".
	 *     $result = "changed";
	 * }
	 *
	 * // Will return NULL.
	 * $result = $test->offsetGet( [ "nested", 1, 2, "UNKNOWN", 3 ] );
	 * // Never use $result in this case!
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
			return ( $this->offsetExists( $theOffset ) )
				 ? $this->mProperties[ $theOffset ]									// ==>
				 : $scrap;															// ==>

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

			return ( ! (bool)count( $theOffset ) )
				 ? $value															// ==>
				 : $scrap;															// ==>

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
	 * This method can be used to retrieve a copy of the object properties in which all
	 * ArrayObject and instances of this class will be converted to an array.
	 *
	 * @return array				Converted array copy.
	 *
	 * @uses getArrayCopy()
	 * @uses convertToArray()
	 *
	 * @example
	 * <code>
	 * $copy = $test->asArray(); // $copy contains an array converted copy of $test.
	 * </code>
	 */
	public function asArray()
	{
		//
		// Make copy.
		//
		$copy = $this->getArrayCopy();

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
	 * @uses convertToArray()
	 * @uses propertyReference()
	 *
	 * @example
	 * <code>
	 * $test->asArray(); // Any embedded object property in $test will be converted to array.
	 * </code>
	 */
	public function toArray()
	{
		self::convertToArray( $this->propertyReference() );

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
	 * is not an <tt>array</tt>,<tt>Container</tt> or <tt>ArrayObject</tt>, the method will
	 * do nothing.
	 *
	 * Note that the conversion is performed on the provided reference, if you need the
	 * original value you must provide a copy to this method.
	 *
	 * @param mixed				   &$theStructure		Structure to convert.
	 *
	 * @uses getArrayCopy()
	 *
	 * @example
	 * <code>
	 * Container::convertToArray( $test ); // Any object element in $test will be converted to array.
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
	 *	<li><tt>&$theMember</tt>: Reference to the object property being managed.
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
	 * $this->manageAttribute( $member, "value" );       // Set new value.
	 * $this->manageAttribute( $member, "value", true ); // Set new value and return old one.
	 * $this->manageAttribute( $member, NULL );          // Return current value, or NULL.
	 * $this->manageAttribute( $member, FALSE );         // Reset attribute to NULL.
	 * $this->manageAttribute( $member, FALSE, TRUE );   // Reset attribute to NULL and return old value.
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
	 *	manageFlagAttribute																*
	 *==================================================================================*/

	/**
	 * <h4>Manage a flag attribute.</h4><p />
	 *
	 * This method can be used to manage a bitfield attribute, the method expects the
	 * following parameters:
	 *
	 * <ul>
	 * 	<li><b>&$theAttribute</b>: Reference of the attribute.
	 * 	<li><b>$theValue</b>: The switch new value or operation:
	 * 	 <ul>
	 * 		<li><tt>NULL</tt>: Retrieve the current state.
	 * 		<li><tt>TRUE</tt> Set the current state and return the previous state.
	 * 		<li><tt>FALSE</tt>: Reset the current state and return the previous state.
	 * 	 </ul>
	 * 	<li><b>$theMask</b>: The flag mask.
	 * </ul>
	 *
	 * @param bitfield			   &$theAttribute		Bitfield attribute reference.
	 * @param bitfield				$theMask			Flag mask.
	 * @param mixed					$theValue			New value or operation.
	 * @return boolean				Current or previous attribute switch value.
	 *
	 * @example
	 * <code>
	 * $this->manageFlagAttribute( $attribute, $mask );     // Will return TRUE if any bit in $attribute matches $mask.
	 * $this->manageFlagAttribute( $offset, $mask, TRUE );  // Will set $attribute bits matching set $mask bits.
	 * $this->manageFlagAttribute( $offset, $mask, FALSE ); // Will reset $attribute bits matching set $mask bits.
	 * </code>
	 */
	protected function manageFlagAttribute( &$theAttribute, $theMask, $theValue = NULL )
	{
		//
		// Return state.
		//
		if( $theValue === NULL )
			return ($theAttribute & $theMask);										// ==>

		//
		// Save previous value.
		//
		$save = (bool)($theAttribute & $theMask);

		//
		// Set flag.
		//
		if( $theValue )
			$theAttribute |= $theMask;

		//
		// Reset flag.
		//
		else
			$theAttribute &= (~$theMask);

		return $save;																// ==>

	} // manageFlag.


	/*===================================================================================
	 *	manageProperty																	*
	 *==================================================================================*/

	/**
	 * <h4>Manage a property</h4><p />
	 *
	 * This library implements a standard interface for managing properties using accessor
	 * methods, properties are stored in the object's inherited array, this method
	 * implements this interface:
	 *
	 * <ul>
	 *	<li><tt>$theOffset</tt>: Property offset, see the <tt>$theOffset</tt> parameter of
	 * 		{@link offsetGet()}, {@link offsetSet()} and {@link offsetUnset()}.
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
	 * $this->manageProperty( $offset, "value" );       // Set value in offset.
	 * $this->manageProperty( $offset, "value", TRUE ); // Set value in offset and return old value.
	 * $this->manageProperty( $offset, NULL );          // Return current offset value.
	 * $this->manageProperty( $offset, FALSE );         // Delete offset and return NULL.
	 * $this->manageProperty( $offset, FALSE, TRUE );   // Delete offset and return old value.
	 * </code>
	 */
	protected function manageProperty( $theOffset, $theValue = NULL, bool $doOld = FALSE )
	{
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
	 * // Get reference to $object properties.
	 * $test = & $object->nestedGet( $list = [] );
	 *
	 * // Get reference to "offset" property.
	 * $test = & $object->nestedGet( $list = [ "offset" ] );
	 *
	 * // Get reference to $object[1][2][3].
	 * $test = & $object->nestedGet( [ 1, 2, 3 ] );
	 * $test = "new"; // Sets $object[1][2][3] property to "new".
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
			if( ! array_key_exists( $offset, $reference ) )
				break;															// =>

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
		// Update list.
		//
		$theOffsets = array_slice( $theOffsets, $i );

		return $reference;															// ==>

	} // nestedPropertyReference.




} // class Container.


?>
