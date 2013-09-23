<?php
require_once('GribParser.php');

class GribGridDescriptionSectionParser extends GribParser
{
	public static function parse($rawData)
	{
		$section = new GribGridDescriptionSection();
		$section->sectionLength = self::_getUInt($rawData, 0, 3);
		$section->numberOfVerticalCoordinateParameters = self::_getUInt($rawData, 3, 1);
		$section->pvOrPl = self::_getUInt($rawData, 4, 1);
		$section->dataRepresentationType = self::_getUInt($rawData, 5, 1);
		
		if ($section->dataRepresentationType == 0) {
			$gridDescription = substr($rawData, 6);
			$section->gridDescription = self::_parseLatLonGridDescription($gridDescription);
		} else {
			throw new GribParserException('Not implemented!!');
		}
		
		return $section;
	}
	
	protected static function _parseGridDescriptionSection($rawData)
	{
		$section = new GribGridDescriptionSection();
		$section->sectionLength = self::_getUInt($rawData, 0, 3);
		$section->numberOfVerticalCoordinateParameters = self::_getUInt($rawData, 3, 1);
		$section->pvOrPl = self::_getUInt($rawData, 4, 1);
		$section->dataRepresentationType = self::_getUInt($rawData, 5, 1);
		
		if ($section->dataRepresentationType == 0) {
			$gridDescription = substr($rawData, 6);
			$section->gridDescription = self::_parseLatLonGridDescription($gridDescription);
		} else {
			throw new GribParserException('Not implemented!!');
		}
		
		return $section;
	}
	
	protected static function _parseLatLonGridDescription($rawData)
	{
		$description = new GribLatLonGridDescription();
		
		$description->pointsAlongLatitude = self::_getUInt($rawData, 0, 2);
		$description->pointsAlongLongitude = self::_getUInt($rawData, 2, 2);
		
		list($description->latitudeFirstPointIsSouth, $description->latitudeFirstPoint)
				= self::_getLatLonWithHemisphere($rawData, 4);
		list($description->longitudeFirstPointIsWest, $description->longitudeFirstPoint)
				= self::_getLatLonWithHemisphere($rawData, 7);
		
		$description->directionIncrementGiven = self::_isFlagSet(128, $rawData, 10);
		$description->earthModel = (self::_isFlagSet(64, $rawData, 10) ?
			GribLatLonGridDescription::EARTH_SPHEROID : 
			GribLatLonGridDescription::EARTH_SPHERICAL);
		
		$description->componentsDirection = (self::_isFlagSet(8, $rawData, 10) ?
			GribLatLonGridDescription::DIRECTION_BY_GRID : 
			GribLatLonGridDescription::DIRECTION_NORTH_EAST);
		
		list($description->latitudeLastPointIsSouth, $description->latitudeLastPoint)
				= self::_getLatLonWithHemisphere($rawData, 11);
		list($description->longitudeLastPointIsWest, $description->longitudeLastPoint)
				= self::_getLatLonWithHemisphere($rawData, 14);
		
		$description->longitudinalIncrement = self::_getUInt($rawData, 17, 2);
		$description->latitudinalIncrement = self::_getUInt($rawData, 19, 2);
		
		$description->scanNegativeI = self::_isFlagSet(128, $rawData, 21);
		$description->scanNegativeJ = self::_isFlagSet(64, $rawData, 21);
		$description->scanJConsecutive = self::_isFlagSet(32, $rawData, 21);
		
		return $description;
	}
}