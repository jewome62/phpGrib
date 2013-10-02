<?php
/**
 * GribBinaryDataSectionDecoder class file
 * 
 * @author Eduardo P de Sousa <edupsousa@gmail.com>
 * @copyright Copyright (c) 2013, Eduardo P de Sousa
 * @license http://opensource.org/licenses/GPL-3.0 GNU Public License 3.0
 */

require_once('GribDecoder.php');

/**
 * GribBinaryDataSectionDecoder is used to decode the GRIB Message Binary 
 * Data Section from a binary string.
 */
class GribBinaryDataSectionDecoder extends GribDecoder
{
	/**
	 * Decode a binary string containing the Binary Data Section (BDS).
	 * Return a GribBinaryDataSection on success or throw a
	 * GribDecoderException on error.
	 * 
	 * @param string $rawData The binary string to decode
	 * @return GribBinaryDataSection The Binary Data Section representation
	 * @throws GribDecoderException
	 */
	public static function decode($rawData)
	{
		$section = new GribBinaryDataSection();
		$section->sectionLength = self::_getUInt($rawData, 0, 3);
		
		$isHarmonicPacking = self::_isFlagSet(128, $rawData, 3);
		$isComplexPacking = self::_isFlagSet(64, $rawData, 3);
		
		if (!$isHarmonicPacking && !$isComplexPacking) {
			$section->packingFormat = GribBinaryDataSection::SIMPLE_PACKING;
		} else if ($isHarmonicPacking && !$isComplexPacking) {
			$section->packingFormat = GribBinaryDataSection::HARMONIC_SIMPLE_PACKING;
		} else if ($isComplexPacking && !$isHarmonicPacking) {
			$section->packingFormat = GribBinaryDataSection::COMPLEX_PACKING;
		} else {
			throw new GribDecoderException('Invalid packing method!');
		}
		
		$section->originalDataWereInteger = self::_isFlagSet(32, $rawData, 3);
		$section->hasAdditionalFlags = self::_isFlagSet(16, $rawData, 3);
		$section->unusedBytesAtEnd = (self::_getUInt(3, 3, 1)) & 15;
		$section->binaryScaleFactor = self::_getSignedInt($rawData, 4, 2);
		$section->referenceValue = self::_getSingle($rawData, 6);
		$section->datumPackingBits = self::_getUInt($rawData, 10, 1);
		
		if ($section->packingFormat == GribBinaryDataSection::SIMPLE_PACKING) {
			$section->rawBinaryData = substr($rawData, 11);
		} else if ($section->packingFormat == GribBinaryDataSection::HARMONIC_SIMPLE_PACKING) {
			throw new GribDecoderException('Harmonic packing decoder not implemented!');
		} else {
			throw new GribDecoderException('Complex packing decoder not implemented!');
		}
		
		return $section;
	}
}