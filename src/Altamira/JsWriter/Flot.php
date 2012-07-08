<?php 

namespace Altamira\JsWriter;

class Flot extends JsWriterAbstract
{
    
    protected function generateScript()
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
    

    protected function getTypeOptions(array $options)
    {
        $types = $this->chart->getTypes();
    
        if(isset($types['default'])) {
            $options = array_merge_recursive($options, $types['default']->getOptions());
        }
    
        if(isset($options['axes'])) {
            foreach($options['axes'] as $axis => $contents) {
                if(isset($options['axes'][$axis]['renderer']) && is_array($options['axes'][$axis]['renderer'])) {
                    $options['axes'][$axis]['renderer'] = $options['axes'][$axis]['renderer'][0];
                }
            }
        }
        
        return $options;
    }
    
    protected function getSeriesOptions(array $options)
    {
        $types = $this->chart->getTypes();
    
        if(isset($types['default'])) {
            $defaults = $options['seriesDefaults'];
            $renderer = $types['default']->getRenderer();
            if(isset($renderer))
                $defaults['renderer'] = $renderer;
            $defaults['rendererOptions'] = $types['default']->getRendererOptions();
            if(count($defaults['rendererOptions']) == 0)
                unset($defaults['rendererOptions']);
            $options['seriesDefaults'] = $defaults;
        }
    
        $seriesOptions = array();
        foreach($this->series as $series) {
            $opts = $series->getOptions();
            $title = $series->getTitle();
            if(isset($types[$title])) {
                $type = $types[$title];
                $opts['renderer'] = $type->getRenderer();
                array_merge_recursive($opts, $type->getSeriesOptions());
            }
            $opts['label'] = $title;
            $seriesOptions[] = $opts;
        }
        $options['series'] = $seriesOptions;
        
        return $options;
    }
    
    public function getOptionsJS(Chart $chart)
    {
        return $this->makeJSArray($chart->getOptions());
    }
    
    
}