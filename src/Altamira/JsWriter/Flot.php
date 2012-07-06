<?php 

namespace Altamira\JsWriter;

class Flot extends JsWriterAbstract
{
    // @todo labels, title, etc
    public function getScript()
    {
        $name = $this->chart->getName();
        
        $jsArray = '[';
        foreach ($this->chart->getSeries() as $title=>$series) {
            $jsArray .= $counter++ > 0 ? ', ' : '';
            
            $data = $series->getData();
            array_walk($data, function($val,$key) use (&$data) { $data[$key] = array($key, $val); });
            $jsArray .= $this->makeJSArray($data);            
        }
        $jsArray .= ']';
        
        return <<<ENDSCRIPT
jQuery(document).ready(function() {
    jQuery.plot(jQuery('#{$name}'), {$jsArray});
});
        
ENDSCRIPT;
        
    }
    
    
}