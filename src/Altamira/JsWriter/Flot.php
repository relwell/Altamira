<?php 

namespace Altamira\JsWriter;

class Flot extends JsWriterAbstract
{
    protected $dateAxes = array('x'=>false, 'y'=>false);
    
    protected function generateScript()
    {
        $name = $this->chart->getName();
        
        $jsArray = '[';
        foreach ($this->chart->getSeries() as $title=>$series) {
            
            $jsArray .= $counter++ > 0 ? ', ' : '';
            
            $jsArray .= '{';
            
            // associate Xs with Ys in cases where we need it
            $data = $series->getData();
            foreach ($data as $key=>$val) { 
                $data[$key] = is_array($val) ? $val : array($key, $val);
                foreach ($this->dateAxes as $axis=>$flag) { 
                    if ($flag) {
                        switch ($axis) {
                            case 'x':
                                $date = \DateTime::createFromFormat('m/d/Y', $data[$key][0]);
                                $data[$key][0] = $date->getTimestamp();
                                break;
                            case 'y':
                                $date = \DateTime::createFromFormat('m/d/Y', $data[$key][1]);
                                $data[$key][0] = $date->getTimestamp();
                                break;
                        }
                    }
                }
            };
            
            if ($title) {
                $jsArray .= 'label: "'.str_replace('"', '\\"', $title).'", ';
            }
            
            $jsArray .= 'data: '.$this->makeJSArray($data);

            $jsArray .= '}';
        }
        
        
        $jsArray .= ']';
        
        $optionsJs = ($js = $this->getOptionsJs()) ? ", {$js}" : '';
        
        return <<<ENDSCRIPT
jQuery(document).ready(function() {
    jQuery.plot(jQuery('#{$name}'), {$jsArray}{$optionsJs});
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
                if ($renderer = $type->getRenderer()) {
                    $opts['renderer'] = $renderer;
                }
                array_merge_recursive($opts, $type->getSeriesOptions());
            }
            $opts['label'] = $title;
            $seriesOptions[] = $opts;
        }
        $options['series'] = $seriesOptions;
        
        return $options;
    }
    
    public function getOptionsJS()
    {
        //@todo actually get this formatted right
        $transformedOptions = $this->options;
        
        return $this->makeJSArray($transformedOptions);
    }
    
    public function useHighlighting($size = 7.5)
    {
        $this->options['highlighter'] = array('sizeAdjust' => $size);
    
        return $this;
    }
    
    public function useZooming()
    {
        $this->options['cursor'] = array('zoom' => true, 'show' => true);
    
        return $this;
    }
    
    public function useCursor()
    {
        $this->options['cursor'] = array('show' => true, 'showTooltip' => true);
    
        return $this;
    }
    
    public function useDates($axis = 'x')
    {
        $this->dateAxes[$axis] = true;
        
        $this->options[$axis.'axis']['mode'] = 'time';
    
        return $this;
    }
    
    
}