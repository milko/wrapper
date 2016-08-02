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
	 * $test = new Container();                                 // Empty container.
	 * $test = new Container( [1,2,3] );                        // With array.
	 * $test = new Container( [1,2,3], TRUE );                  // With array, flattened.
	 * $test = new Container( new ArrayObject( [1,2,3] ) );     // With ArrayObject.
	 * $test = new Container( new Container( [1,2,3] ), TRUE ); // With Container flattened.
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
	 * We implement this method to use the properties member array.
	 *
	 * @param mixed					$theOffset			Offset.
	 * @return bool					<tt>TRUE</tt> the offset exists.
	 */
	public function offsetExists( $theOffset )
	{
		return array_key_exists( $theOffset, $this->mProperties );					// ==>

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
	 * @param mixed					$theOffset			Offset.
	 * @return mixed				Offset value or <tt>NULL</tt>.
	 *
	 * @uses offsetExists()
	 *
	 * @example
	 * <code>
	 * $test->offsetGet( "offset" );  // Will return the value at that offset.
	 * $test->offsetSet( "UNKNOWN" ); // Will not generate a warning and return NULL.
	 * </code>
	 */
	public function offsetGet( $theOffset )
	{
		//
		// Matched offset.
		//
		if( $this->offsetExists( $theOffset ) )
			return $this->mProperties[ $theOffset ];								// ==>

		return NULL;																// ==>

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
	 * @param string				$theOffset			Offset.
	 * @param mixed					$theValue			Value to set at offset.
	 *
	 * @uses offsetUnset()
	 *
	 * @example
	 * <code>
	 * $test->offsetSet( "offset", "value" ); // Will set a value in that offset.
	 * $test->offsetSet( "offset", NULL );    // Will unset that offset.
	 * </code>
	 */
	public function offsetSet( $theOffset, $theValue )
	{
		//
		// Skip deletions.
		//
		if( $theValue !== NULL )
		{
			if( $theOffset !== NULL )
				$this->mProperties[ $theOffset ] = $theValue;
			else
				$this->mProperties[] = $theValue;
		}

		//
		// Handle delete.
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
	 * @param string				$theOffset			Offset.
	 *
	 * @example
	 * <code>
	 * $test->offsetUnset( "UNKNOWN" ); // Will not generate a warning.
	 * </code>
	 */
	public function offsetUnset( $theOffset )
	{
		//
		// Delete value.
		//
		if( array_key_exists( $theOffset, $this->mProperties ) )
			unset( $this->mProperties[ $theOffset ] );

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
	 * <h4>Return array keys.</h4><p />
	 *
	 * This method is a proxy to the array data member.
	 *
	 * <em>Note: I was unable to use the documented default parameters of the array_keys()
	 * function: whenever added, the function would not return any keys.</em>
	 *
	 * @param mixed					$theSearch			Search value.
	 * @param bool					$doStrict			Strict comparaison flag.
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
	 * <h4>Return array values.</h4><p />
	 *
	 * This method is a proxy to the array data member.
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
	 * This method will return an array copy containing the properties of the object.
	 *
	 * @return array				Array copy.
	 */
	public function getArrayCopy()
	{
		return $this->mProperties;													// ==>

	} // getArrayCopy.



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
	 * @uses referenceGet()
	 * @uses convertToArray()
	 *
	 * @example
	 * <code>
	 * $test->asArray(); // Any embedded object property in $test will be converted to array.
	 * </code>
	 */
	public function toArray()
	{
		self::convertToArray( $this->referenceGet() );

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
	 * This method can be used to convert any embedded ArrayObject to an array in the
	 * provided parameter, if the parameter is not an array or ArrayObject, the method will
	 * do nothing.
	 *
	 * Note that the conversion is in place, if you want to get a converted copy, provide
	 * a copy.
	 *
	 * @param mixed				   &$theStructure		Structure to convert.
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
	 *	<li><tt>$theOffset</tt>: Property offset.
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


	/*===================================================================================
	 *	manageIndexedProperty															*
	 *==================================================================================*/

	/**
	 * <h4>Manage an indexed property</h4><p />
	 *
	 * This method can be used to manage a property structured as an associative array, the
	 * method accepts the following parameters:
	 *
	 * <ul>
	 *	<li><tt>$theOffset</tt>: Property offset.
	 *	<li><tt>$theKey</tt>: The property key or operation:
	 *	 <ul>
	 *		<li><tt>NULL</tt>: Return the full array, the next parameter is ignored.
	 *		<li><tt>FALSE</tt>: Delete the full array, the next parameter is ignored.
	 *		<li><tt>scalar</tt>: Use the value as the associative array key, the next
	 * 			parameter will be considered the value or operation.
	 *		<li><em>other</em>: Will raise an exception.
	 *	 </ul>
	 *	<li><tt>$theValue</tt>: The property value or operation:
	 *	 <ul>
	 *		<li><tt>NULL</tt>: Return the property value at the provided key.
	 *		<li><tt>FALSE</tt>: Delete the property at the provided key.
	 *		<li><em>other</em>: Set the property at the provided key with the value.
	 *	 </ul>
	 *	<li><tt>$doOld</tt>: Return value switch:
	 *	 <ul>
	 *		<li><tt>TRUE</tt>: Return the current value.
	 *		<li><tt>FALSE</tt>: Return the old value; irrelevant when returning current.
	 *	 </ul>
	 * </ul>
	 *
	 * If an indexed property becomes empty, it will be deleted.
	 *
	 * @param string				$theOffset			Property offset.
	 * @param string				$theKey				Key or operation.
	 * @param mixed					$theValue			Value or operation.
	 * @return mixed				Old or current property value.
	 * @throws \InvalidArgumentException
	 *
	 * @uses manageProperty()
	 * @uses offsetExists()
	 *
	 * @example
	 * <code>
	 * $this->manageIndexedProperty( $offset );                 // Will retrieve the property at offset.
	 * $this->manageIndexedProperty( $offset, FALSE );          // Will remove the property at offset.
	 * $this->manageIndexedProperty( $offset, 'key', 'value' ); // Will set the property at key "key" to "value".
	 * $this->manageIndexedProperty( $offset, 'key' );          // Will retrieve the property value at key "key".
	 * $this->manageIndexedProperty( $offset, 'key', FALSE );   // Will reset the property at key "key".
	 * </code>
	 */
	protected function manageIndexedProperty( $theOffset,
											  $theKey = NULL, $theValue = NULL,
											  bool $doOld = FALSE )
	{
		//
		// Return or reset full array.
		//
		if( ($theKey === NULL)
		 || ($theKey === FALSE) )
			return $this->manageProperty( $theOffset, $theKey, $doOld );			// ==>

		//
		// Assert key type.
		//
		if( is_scalar( $theKey ) )
		{
			//
			// Init local storage.
			//
			$has_key = ( $has_property = $this->offsetExists( $theOffset ) )
					 ? array_key_exists( $theKey, $this->mProperties[ $theOffset ] )
					 : FALSE;

			//
			// Return current value.
			//
			if( $theValue === NULL )
			{
				//
				// Handle found key.
				//
				if( $has_key )
					return $this->mProperties[ $theOffset ][ $theKey ];				// ==>

				return NULL;														// ==>

			} // Return current value.

			//
			// Save current value.
			//
			$save = ( $has_key )
				? $this->mProperties[ $theOffset ][ $theKey ]
				: NULL;

			//
			// Set value.
			//
			if( $theValue !== FALSE )
			{
				//
				// Init property.
				//
				if( ! $has_property )
					$this->mProperties[ $theOffset ] = [];

				//
				// Set value.
				//
				$this->mProperties[ $theOffset ][ $theKey ] = $theValue;

				if( ! $doOld )
					return $theValue;												// ==>

			} // Set value.

			//
			// Reset.
			//
			else
			{
				//
				// Unset element.
				//
				if( $has_key )
				{
					unset( $this->mProperties[ $theOffset ][ $theKey ] );
					if( ! count( $this->mProperties[ $theOffset ] ) )
						unset( $this->mProperties[ $theOffset ] );
				}

				//
				// Return old value.
				//
				if( ! $doOld )
					return NULL;													// ==>

			} // Reset.

			return $save;															// ==>

		} // Scalar key.

		throw new \InvalidArgumentException(
			"Unable to manage indexed property [$theOffset]: " .
			"expecting a scalar key."
		);																		// !@! ==>

	} // manageIndexedProperty.


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



/*=======================================================================================
 *																						*
 *							PROTECTED SERIALISATION INTERFACE							*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	referenceGet																	*
	 *==================================================================================*/

	/**
	 * <h4>Return a property reference.</h4><p />
	 *
	 * This method will return a reference to a specific property, if the property offset
	 * does not exist, the method will raise an exception.
	 *
	 * If you provide <tt>NULL</tt> as the offset, the method will return a reference to
	 * the object's array.
	 *
	 * @param mixed					$theOffset			Offset.
	 * @return mixed				The property reference.
	 * @throws \InvalidArgumentException
	 *
	 * @uses offsetExists()
	 *
	 * @example
	 * <code>
	 * $test = & $object->referenceGet( "offset" ); // Get reference to "offset" property.
	 * $test = "new";                               // Sets "offset" property in $object to "new".
	 * </code>
	 */
	protected function & referenceGet( $theOffset = NULL )
	{
		//
		// Get array reference.
		//
		if( $theOffset === NULL )
			return $this->mProperties;												// ==>

		//
		// Check property.
		//
		if( $this->offsetExists( $theOffset ) )
			return $this->mProperties[ $theOffset ];								// ==>

		throw new \InvalidArgumentException(
			"Unknown offset [$theOffset]."
		);																		// !@! ==>

	} // referenceGet.




} // class Container.


?>
