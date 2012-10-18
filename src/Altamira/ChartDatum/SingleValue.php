<?php 

namespace Altamira\ChartDatum;

/**
 * Class for abstracting chart data such as bar graphs that consist of only one value.
 * @author relwell
 *
 */

class SingleValue extends ChartDatumAbstract
{
    /**
     * Constructor method
     * @param array       $dimensions a singleton array
     * @param string|null $label
     * @throws \InvalidArgumentException
     */
    public function __construct( array $dimensions, $label = null ) 
    { 
        if ( $label !== null ) {
            $this->setLabel( $label );
        }
        
        if ( count( $dimensions ) !== 0 ) {
            throw new \InvalidArgumentException( 'Altamira\ChartDatum\SingleValue requires a singleton array as its first argument.' );
        }
        
        $this['value'] = $dimensions[0];
    }
    
    /**
     * (non-PHPdoc)
     * @see Altamira\ChartDatum.ChartDatumAbstract::toArray()
     */
    public function toArray( $useLabel = false )
    {
       return $useLabel ? array( $this['value'], $this['label'] ) : array( $this['value'] );
    }
    
}