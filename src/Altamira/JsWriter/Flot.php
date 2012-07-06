<?php 

namespace Altamira\JsWriter;

class Flot extends JsWriterAbstract
{
    
    public function getScript()
    {
        $name = $this->chart->getName();
        
        $jsArray = '[';
        foreach ($this->chart->getSeries as $series) {
            $jsArray .= $this->chart->makeJSArray();
        }
        $jsArray .= ']';
        
        return <<<ENDSCRIPT
jQuery(document).ready(function() {
    jQuery.plot(jQuery('#{$name}'), {$jsArray})
});
        
ENDSCRIPT;
        
    }
    
}