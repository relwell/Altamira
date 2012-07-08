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
            
            $jsArray .= '{';
            
            // associate Xs with Ys in cases where we need it
            $data = $series->getData();
            array_walk($data, function($val,$key) use (&$data) { $data[$key] = is_array($val) ? $val : array($key, $val); });
            
            if ($title) {
                $jsArray .= 'label: "'.str_replace('"', '\\"', $title).'", ';
            }
            
            $jsArray .= 'data: '.str_replace('"', '\\"', $this->makeJSArray($data));

            $jsArray .= '}';
        }
        $jsArray .= ']';
        
        return <<<ENDSCRIPT
jQuery(document).ready(function() {
    jQuery.plot(jQuery('#{$name}'), {$jsArray});
});
        
ENDSCRIPT;
        
    }
    
    
}