<?php

/**
 * ISO_4217_Iterator.php
 *
 * This file contains the definition of the {@link ISO_4217_Iterator} class.
 */

namespace Milko\utils;

/*=======================================================================================
 *																						*
 *								ISO_4217_Iterator.php									*
 *																						*
 *======================================================================================*/

use Milko\utils\ISOCodesIterator;

/**
 * <h4>ISO 4217 codes iterator.</h4><p />
 *
 * This is a <em>concrete</em> implementation of the {@link ISOCodesIterator} class, it will
 * iterate and translate the ISO 4217 standard.
 *
 * You instantiate this class by calling the
 * <tt>ISOCodes::getIterator( ISOCodes::k4217 )</tt> method.
 *
 *	@package	Utils
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		26/08/2016
 */
class ISO_4217_Iterator extends ISOCodesIterator
{



/*=======================================================================================
 *																						*
 *							PUBLIC MEMBER ACCESSOR INTERFACE	   						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	DefaultCode																		*
	 *==================================================================================*/

	/**
	 * <h4>Return default code property.</h4><p />
	 *
	 * We implement this method by returning the <tt>alpha_3</tt> property name.
	 *
	 * @return string				Default code property name.
	 */
	public function DefaultCode()
	{
		return "alpha_3";															// ==>

	} // DefaultCode.


	/*===================================================================================
	 *	Translated																		*
	 *==================================================================================*/

	/**
	 * <h4>Return list of translated properties.</h4><p />
	 *
	 * We implement this method by returning the translatable property names.
	 *
	 * @return array				List of translatable properties.
	 */
	public function Translated()
	{
		return [
			"name"
		];																			// ==>

	} // Translated.




} // class ISO_4217_Iterator.


?>
