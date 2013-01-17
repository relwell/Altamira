<?php
/**
 * Class definition for \Altamira\Type\D3\Bar
 */
namespace Altamira\Type\D3;
/**
 * Responsible for providing the model for bar charts
 * @author relwell
 */
class Bar extends D3TypeAbstract
{
    /**
     * Defintes the discrete bar model
     * @var string
     */
    protected $chartDirective = "var chart = nv.models.#model#().x(function(d) { return d.label ? d.label : '' }).y(function(d) { return d.value })\n";
    
    /**
     * Allows us to identify the correct chart model
     * @var string
     */
    protected $chartModel = 'discreteBarChart';
    
    /**
     * Generates the appropriate directive for generating the correct NVD3 model
     * Overruled here so we can use the options we set to determine what model we'll send
     * @return string;
     */
    public function getChart()
    {
        return str_replace( '#model#', $this->chartModel, $this->chartDirective );
    }
    
    /**
     * Configures the chart directive based on the options set
     * @see \Altamira\Type\TypeAbstract::setOption()
     * @param string $key
     * @param mixed $val
     * @return \Altamira\Type\D3\Bar
     */
    public function setOption( $key, $val )
    {
        switch ( $key ) {
            case 'horizontal':
                $this->chartModel = 'multiBarHorizontalChart';
                return $this;
            case 'stackSeries':
                $this->chartModel = 'multiBarChart';
                $this->chartDirective .= ".stacked(true)\n";
                return $this;
            default:
                return parent::setOption( $key, $val );
        }
        return $this;
    }
}