<?php

namespace Altamira\JsWriter;

class JqPlot extends \Altamira\JsWriter\JsWriterAbstract
{
    
    public function getScript()
    {
        
        $output  = '$(document).ready(function(){';
        $output .= '$.jqplot.config.enablePlugins = true;';
        
        $num = 0;
        $vars = array();
        
        $types = $this->chart->getTypes();
        
        $useTags = (isset($types['default']) 
                 && $types['default']->getUseTags()) 
                 || ($this->chart->getUseTags());
        
        
        foreach($this->chart->getSeries() as $series) {
            $num++;
            $data = $series->getData($useTags);
        
            $varname = 'plot_' . $this->chart->getName() . '_' . $num;
            $vars[] = '#' . $varname . '#';
            $output .= $varname . ' = ' . $this->makeJSArray($data) . ';';
        }
        
        $output .= 'plot = $.jqplot("' . $this->chart->getName() . '", ';
        $output .= $this->makeJSArray($vars);
        $output .= ', ';
        $output .= $this->chart->getOptionsJS();
        $output .= ');';
        $output .= '});';
        
        return $output;
        
    }
    
}