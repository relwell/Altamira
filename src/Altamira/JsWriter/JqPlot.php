<?php

namespace Altamira\JsWriter;

use \Altamira\JsWriter\Ability;

class JqPlot 
    extends \Altamira\JsWriter\JsWriterAbstract
    implements Ability\Cursorable, 
               Ability\Datable, 
               Ability\Fillable, 
               Ability\Griddable, 
               Ability\Highlightable, 
               Ability\Legendable, 
               Ability\Shadowable
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
    
    public function setShadow($series, $use = true, $angle = 45, $offset = 1.25, $depth = 3, $alpha = 0.1)
    {
        $this->options['series'][$series]['shadow'] = $use;
		$this->options['series'][$series]['shadowAngle'] = $angle;
		$this->options['series'][$series]['shadowOffset'] = $offset;
		$this->options['series'][$series]['shadowDepth'] = $depth;
		$this->options['series'][$series]['shadowAlpha'] = $alpha;
		
		return $this;
    }
    
    public function setFill($series, $use = true, $stroke = false, $color = null, $alpha = null) 
    {
        $this->options['series'][$series]['fill'] = $use;
        $this->options['series'][$series]['fillAndStroke'] = $stroke;
        
        if(isset($color)) {
            $this->options['series'][$series]['fillColor'] = $color;
        }
        if(isset($alpha)) {
            $this->options['series'][$series]['fillAlpha'] = $alpha;
        }
        
        return $this;
    }
    
    public function setGrid($on = true, $color = null, $background = null)
    {
        $this->options['grid']['drawGridLines'] = $on;
        if(isset($color)) {
            $this->options['grid']['gridLineColor'] = $color;
        }
        if(isset($background)) {
            $this->options['grid']['background'] = $background;
        }
    
        return $this;
    }
    
    public function setLegend($on = true, $location = 'ne', $x = 0, $y = 0)
    {
        if(!$on) {
            unset($this->options['legend']);
        } else {
            $legend = array();
            $legend['show'] = true;
            if($location == 'outside' || $location == 'outsideGrid') {
                $legend['placement'] = $location;
            } else {
                $legend['location'] = $location;
            }
            if($x != 0)
                $legend['xoffset'] = $x;
            if($y != 0)
                $legend['yoffset'] = $y;
            $this->options['legend'] = $legend;
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
            $title = $series->getTitle();
            $opts = $this->options['series'][$title];
            
            if(isset($types[$title])) {
                $type = $types[$title];
                $opts['renderer'] = $type->getRenderer();
                array_merge_recursive($opts, $type->getSeriesOptions());
            }
            $opts['label'] = $title;
            
            if($series->usesLabels()) {
                $this->options['pointLabels']['show'] = true;
            }
            
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