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
     * @see Altamira\ChartDatum.ChartDatumAbstract::getRenderData
     */
    public function getRenderData( $useLabel = false )
    {
        $typeName = '';
        if ( $type = $this->jsWriter->getType( $this->series->getTitle() ) ) {
            $typeName = preg_replace('/.*\\\(.*)$/', '$1', get_class($type));
        }
        
        if ( $typeName == 'Donut' && $this->jsWriter instanceof \Altamira\JsWriter\Flot ) {
            $value = array( 1, $this['y'] );
        } else {
            $value = array($this['x'], $this['y']);
            if ( $useLabel ) {
                $value[] = $this->getLabel();
            }
        }
        return $value;
    }
    
}