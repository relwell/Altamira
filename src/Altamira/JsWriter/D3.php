<?php
/**
 * Class definition for \Altamira\JsWriter\D3
 * @author relwell
 */
namespace Altamira\JsWriter;
use Altamira\JsWriter\Ability;
/**
 * JsWriter responsible for storing options and 
 * rendering values to cause D3 to render a specific chart.
 * @namespace \Altamira\JsWriter
 * @package JsWriter
 * @author relwell
 */
class D3
    extends JsWriterAbstract
{
    /**
     * Identifies the string value of which library this jsWriter is responsible for
     * @var string
     */
    const LIBRARY = 'd3';
    
    /**
     * Tracks the javascript commands sent to D3
     */
    protected $outputBuffer = array();
    
    /**
     * Used to identify the type namespace for this particualr JsWriter 
     * @var string
     */
    protected $typeNamespace = '\\Altamira\\Type\\D3\\';
    
    /**
     * Sets the chart margin
     * @var int
     */
    protected $margin = 20;
    
    /**
     * Gives us a consistent variable name for this chart's data
     * @var string
     */
    protected $dataName;
    
    /** 
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getOptionsJS()
     */
    protected function getOptionsJS() 
    {
        return array();
    }

    /** 
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getScript()
     */
     public function getScript() 
     {
         return  $this->writeData()
              .  $this->writeMargin()
              .  $this->writeSvg()
              .  $this->writeAxisTicks()
              .  $this->getType()->write( $this->getDataName() )
         ;
     }
     
     
    /**
     * Responsible for setting the tick labels on a given axis
     * @param string $axis
     * @param array $ticks
     * @return \Altamira\JsWriter\D3
     */
    public function setAxisTicks($axis, array $ticks = array() )
    {
        $opposites = array( 'x' => 'y', 'y' => 'x' );
        if ( in_array($axis, array('x', 'y') ) ) {
            $oppositeAxis = $opposites[$axis];
            $length = count( $ticks );
            $this->outputBuffer['axisticks'.$axis] = <<<ENDCODE
g.selectAll(".{$axis}Ticks")
    .data(x.ticks({$length}))
    .enter().append("svg:line")
    .attr("class", "{$axis}ticks")
    .attr("{$axis}1", function(d) { return {$axis}(d); })
    .attr("{$oppositeAxis}1", -1 * {$oppositeAxis}(0))
    .attr("{$axis}2", function(d) { return {$axis}(d); })
    .attr("{$oppositeAxis}2", -1 * {$oppositeAxis}(-0.3))
ENDCODE;
            
        }

        return $this;
    }
    
    public function writeAxisTicks()
    {
        return  ( isset( $this->outputBuffer['axisticksx'] ) ? $this->outputBuffer['axisticksx'] : '' )
             .  ( isset( $this->outputBuffer['axisticksy'] ) ? $this->outputBuffer['axisticksy'] : '' );
    }
    
    /**
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getType()
     */
    public function getType( $series = null )
    {
        if ( ! isset( $this->types['default'] ) ) {
            $this->setType( 'Line' );
        }
        return $this->types['default'];
    }
    
    /**
     * Provided data registered in a series, declare the data for that series
     */
    protected function writeData()
    {
        $counter = 0;
        $ob = '';
        $dataVars = array();
        $chartName = $this->chart->getName();
        foreach ( $this->chart->getSeries() as $series ) {
            $seriesData = array();
            $sig = sprintf( '%s_%s', $chartName, $counter++ );
            foreach ( $series->getData() as $datum )
            {
                $seriesData[] = $datum->getRenderData();
            }
            $dataVars[] = $sig;
            $ob .= sprintf( "var %s = %s;\n", $sig, json_encode( $seriesData ) );
        }
        return sprintf( "%svar %s = [%s]\n", $ob, $this->getDataName(), implode( ',', $dataVars ) );
    }
    
    public function setMargin( $margin )
    {
        $this->margin = $margin;
    }
    
    protected function writeMargin()
    {
        return sprintf( "margin = %s;\n", $this->margin );
    }
    
    protected function getDataName()
    {
        if (! isset( $this->dataName ) ) {
            $this->dataName = $this->chart->getName() . '_data'; 
        }
        return $this->dataName; 
    }
    
    protected function writeSvg()
    {
        $chartName = $this->chart->getName();
        $dataName = $this->getDataName();
        return <<<ENDSCRIPT
var h = div.height;
var w = div.width;
y = d3.scale.linear().domain([0, d3.max({$dataName})]).range([0 + margin, h - margin]);
x = d3.scale.linear().domain([0, {$dataName}.length]).range([0 + margin, w - margin]);
var div = d3.select("#{$chartName}");
var vis = div
    .append("svg:svg")
    .attr("width", w)
    .attr("height", h)
var g = vis.append("svg:g")
    .attr("transform", "translate(0, 200)");
ENDSCRIPT;
        
    }
}