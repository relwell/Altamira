<?php 

namespace Altamira;

class ChartRenderer
{
    
    public static function render( Chart $chart, array $styleOptions = array() )
    {
        $style = self::renderStyle( $styleOptions );
        $data = self::renderData( $chart );
        
        return <<<ENDDIV
<div class="jqPlot" id="{$chart->getName()}" style="{$style}"{$data}></div>
ENDDIV;
        
    }
    
    public static function renderStyle( array $styleOptions = array() )
    {
        $style = '';
        foreach ( $styleOptions as $key=>$val ) {
            $style .= "$key: $val; ";
        }
        return $style;
    }
    
    public static function renderData( Chart $chart )
    {
        
        
        
    }
    
}