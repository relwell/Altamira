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
    implements Ability\Fillable
{
    /**
     * Identifies the string value of which library this jsWriter is responsible for
     * @var string
     */
    const LIBRARY = 'd3';

    /**
     * Used to identify the type namespace for this particualr JsWriter 
     * @var string
     */
    protected $typeNamespace = '\\Altamira\\Type\\D3\\';

    /**
     * Holds on to additional directives that the setting of specific options may register
     * @var array
     */
    protected $extraDirectives = array();
    
    /** 
     * Not sure what to do with this yet. NVD3 is more functional than the other libs
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
         return sprintf( self::ADD_GRAPH, $this->getType()->getChart(),
                                          implode( "\n", $this->extraDirectives ),
                                          $this->chart->getName(),
                                          $this->writeData(),
                                          $this->chart->getName()
                       );
         
         
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
     * Used for filling series in charts
     * @param string|\Altamira\Chart $series
     * @param array $opts
     */
    public function setFill($series, $opts = array('use'    => true, 
                                                   'stroke' => false, 
                                                   'color'  => null, 
                                                   'alpha'  => null
                                                  )
                            )
    {
        if ( isset( $opts['color'] ) ) {
            $this->setSeriesOption( $series, 'color', $opts['color'] );
        }
    }
    
    /**
     * Provided data registered in a series, declare the data for that series
     * @return string
     */
    protected function writeData()
    {
        $jsonBuffer = "[\n";
        $counter = 0;
        foreach ( $this->chart->getSeries() as $series )
        {
            if ( $counter++ > 0 ) {
                $jsonBuffer .= "\t,\n";
            }
            $title = $series->getTitle();
            $data = array(
                    'values' => array(),
                    'key' => $title,
                    );
            if ( $color = $this->getNestedOptVal( $this->options, 'seriesStorage', $title, 'color' ) ) {
                $data['color'] = $color;
            }
            foreach ( $series->getData() as $datum )
            {
                $datumArray = $datum->toArray();
                // reformat bubble radius to size
                if ( isset( $datumArray['radius'] ) ) {
                    $datumArray['size'] = $datumArray['radius'];
                    unset( $datumArray['radius'] );
                }
                $data['values'][] = $datumArray;
            }
            $jsonBuffer .= "\t".json_encode( $data )."\n";
        }
        
        $jsonBuffer .= "\n]";
        
        return $jsonBuffer;
    }

    
    const ADD_GRAPH = <<<ENDSCRIPT
nv.addGraph(function() {  
    %s
    %s
    d3.select('#%s svg')
      .datum(%s)
      .transition()
      .duration(500)
      .call(chart);
    nv.utils.windowResize(function() { d3.select('#%s svg').call(chart) });
    return chart;
});
ENDSCRIPT;
}