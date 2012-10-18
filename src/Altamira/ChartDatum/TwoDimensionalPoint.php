<?php 

namespace Altamira\ChartDatum;

/**
 * Class for abstracting chart data with an X, a Y, and a label.
 * @author relwell
 *
 */

class TwoDimensionalPoint extends ChartDatumAbstract
{
    /**
     * Constructor method
     * @param array       $dimensions with x and y keys
     * @param string|null $label
     * @throws \InvalidArgumentException
     */
    public function __construct( array $dimensions, $label = null ) 
    { 
        if ( $label !== null ) {
            $this->setLabel( $label );
        }
        
        if (! ( isset( $dimensions['x'] ) && isset( $dimensions['y'] ) ) ) {
            throw new \InvalidArgumentException( 'Altamira\ChartDatum\BubbleDatum requires array keys for x and y values in argument 1.' );
        }
        
        $this['x'] = $dimensions['x'];
        $this['y'] = $dimensions['y'];
    }
    
    /**
     * (non-PHPdoc)
     * @see Altamira\ChartDatum.ChartDatumAbstract::toArray()
     */
    public function toArray( $useLabel = false )
    {
        return array($this['x'], $this['y']) + ($useLabel ? array($this->getLabel()) : array());
    }
    
}