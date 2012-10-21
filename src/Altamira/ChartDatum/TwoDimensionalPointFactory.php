<?php 

namespace Altamira\ChartDatum;

use Altamira\ChartDatum\TwoDimensionalPoint;

class TwoDimensionalPointFactory
{
    public static function getFromYValues( $oneDimensionalArray, $result = array() ) {
        foreach ($oneDimensionalArray as $x => $y ) {
            $result[] = new TwoDimensionalPoint( array('x' => $x+1, 'y' => $y ) );
        }
        return $result;
    }
    
    public static function getFromXValues( $oneDimensionalArray, $result = array() ) {
        foreach ($oneDimensionalArray as $y => $x ) {
            $result[] = new TwoDimensionalPoint( array('x' => $x, 'y' => $y+1 ) );
        }
        return $result;
    }
    
    public static function getFromNested( $nestedArray, $result = array() ) {
        foreach ( $nestedArray as $array ) {
            $result[] = new TwoDimensionalPoint( array('x' => $array[0], 'y' => $array[1] ) );
        }
        return $result;
    }
}