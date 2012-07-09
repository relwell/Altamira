<?php

namespace Altamira\JsWriter;

class JqPlot extends \Altamira\JsWriter\JsWriterAbstract
{
    
    public function generateScript()
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
        $output .= $this->getOptionsJS();
        $output .= ');';
        $output .= '});';
        
        return $output;
        
    }
    
    public function useHighlighting($size = 7.5)
    {
        $this->files = array_merge_recursive(array('jqplot.highlighter.min.js'), $this->files);
        $this->options['highlighter'] = array('sizeAdjust' => $size);

        return $this;
    }
    
    public function useZooming()
    {
        $this->files = array_merge_recursive(array('jqplot.cursor.min.js'), $this->files);
        $this->options['cursor'] = array('zoom' => true, 'show' => true);
    
        return $this;
    }
    
    public function useCursor()
    {
        $this->files = array_merge_recursive(array('jqplot.cursor.min.js'), $this->files);
        $this->options['cursor'] = array('show' => true, 'showTooltip' => true);
        
        return $this;
    }
    
    public function useDates($axis = 'x')
    {
        $this->files = array_merge_recursive(array('jqplot.dateAxisRenderer.min.js'), $this->files);
        if(strtolower($axis) === 'x') {
            $this->options['axes']['xaxis']['renderer'] = '#$.jqplot.DateAxisRenderer#';
        } elseif(strtolower($axis) === 'y') {
            $this->options['axes']['yaxis']['renderer'] = '#$.jqplot.DateAxisRenderer#';
        }
    
        return $this;
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
        foreach($this->chart->getSeries() as $series) {
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
        
        if ($options['pointLabels']) {
            $this->files[] = 'jqplot.pointLabels.min.js';
        }
        
        return $options;
    }
    
    public function getOptionsJS()
    {
        return $this->makeJSArray($this->options);
    }
    
}