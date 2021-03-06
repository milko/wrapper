<?php

/**
 * Container class test class.
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		02/08/2016
 */

//
// Test class.
//
class test_Container extends \Milko\wrapper\Container
{
	//
	// Declare test attributes.
	//
	var $attribute = NULL;
	var $flag = NULL;

	//
	// Declare constructor.
	//
	public function __construct( $props = NULL, bool $array = FALSE ) {
		parent::__construct( $props, $array );
		$this->flag = hex2bin( '00000000' );
	}

	//
	// Declare attribute accessor method.
	//
	public function Attribute( $theValue = NULL, $getOld = FALSE )
	{
		return self::manageAttribute( $this->attribute, $theValue, $getOld );
	}

	//
	// Declare bitfield attribute accessor method.
	//
	public function BitfieldAttribute( string  $theMask = NULL,
								bool	$theValue = NULL,
								bool	$doOld = FALSE )
	{
		return self::manageBitfieldAttribute(
			$this->flag, $theMask, $theValue, $doOld );
	}

	//
	// Declare property accessor method.
	//
	public function Property( $theOffset, $theValue = NULL, $getOld = FALSE )
	{
		return $this->manageProperty( $theOffset, $theValue, $getOld );
	}

	//
	// Declare bitfield property accessor method.
	//
	public function BitfieldProperty( $theOffset,
							   string  $theMask = NULL,
							   bool	$theValue = NULL,
							   bool	$doOld = FALSE )
	{
		return $this->manageBitfieldProperty(
			$theOffset, $theMask, $theValue, $doOld );
	}

	//
	// Declare nested property accessor method.
	//
	public function & NestedProperty( array & $theOffsets, bool $getParent = FALSE )
	{
		return $this->nestedPropertyReference( $theOffsets, $getParent );
	}

	//
	// Reset object properties.
	//
	public function ResetProperties()
	{
		$this->mProperties = [];
	}
}


?>

