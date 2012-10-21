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
        
        if ( count( $dimensions ) !== 1 ) {
            throw new \InvalidArgumentException( 'Altamira\ChartDatum\SingleValue requires a singleton array as its first argument.' );
        }
        
        $this['value'] = $dimensions[0];
    }
    
    /**
     * (non-PHPdoc)
     * @see Altamira\ChartDatum.ChartDatumAbstract::getRenderData()
     */
    public function getRenderData( $useLabel = false )
    {
       return $this['value'];
    }
    
}