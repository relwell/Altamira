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
               Ability\Shadowable,
               Ability\Labelable,
               Ability\Lineable
{
    const LIBRARY = 'jqplot';
    
    protected $typeNamespace = '\\Altamira\\Type\\JqPlot\\';
    
    public function generateScript()
    {
        $output  = '$(document).ready(function(){';
        $output .= '$.jqplot.config.enablePlugins = true;';
        
        $num = 0;
        $vars = array();        
        
        foreach($this->chart->getSeries() as $series) {
            $num++;
            $data        = $series->getData();
            $dataPrepped = array();
            $title       = $series->getTitle();
            $labelCopy   = null;
            
            if (isset($this->seriesLabels[$title]) 
                && !empty($this->seriesLabels[$title])) {
                $labelCopy = $this->seriesLabels[$title];
            }
            
            foreach ($data as &$datum) {
                if ( $labelCopy !== null ) {
                    $datum->setLabel( array_shift( $labelCopy ) );
                }
                $dataPrepped[] = $datum->getRenderData();
            }
            $varname = 'plot_' . $this->chart->getName() . '_' . $num;
            $vars[] = '#' . $varname . '#';
            $output .= $varname . ' = ' . $this->makeJSArray($dataPrepped) . ';';
        }
        $output .= 'plot = $.jqplot("' . $this->chart->getName() . '", ';
        $output .= $this->makeJSArray($vars);
        $output .= ', ';
        $output .= $this->getOptionsJS();
        $output .= ');';
        $output .= '});';
        
        return $output;
        
    }
    
    public function useHighlighting(array $opts = array('size'=>7.5))
    {
        extract($opts);
        $size = isset($size) ? $size : 7.5;
        
        $this->files = array_merge_recursive(array('jqplot.highlighter.min.js'), $this->files);
        $this->options['highlighter'] = array('sizeAdjust' => $size);

        return $this;
    }
    
    public function useZooming(array $options = array('mode'=>'xy'))
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
    
    public function setShadow($series, $opts = array('use'=>true, 
                                                     'angle'=>45, 
                                                     'offset'=>1.25, 
                                                     'depth'=>3, 
                                                     'alpha'=>0.1))
    {
        extract($opts);
        
        $use = isset($use) ? $use : true;
        $angle = isset($angle) ? $angle : 45;
        $offset = isset($offset) ? $offset : 1.25; 
        $depth = isset($depth) ? $depth : 3; 
        $alpha = isset($alpha) ? $alpha : 0.1;
        
        $this->options['seriesStorage'][$series]['shadow'] = $use;
		$this->options['seriesStorage'][$series]['shadowAngle'] = $angle;
		$this->options['seriesStorage'][$series]['shadowOffset'] = $offset;
		$this->options['seriesStorage'][$series]['shadowDepth'] = $depth;
		$this->options['seriesStorage'][$series]['shadowAlpha'] = $alpha;
		
		return $this;
    }
    
    public function setFill($series, $opts = array('use' => true, 
                                                   'stroke' => false, 
                                                   'color' => null, 
                                                   'alpha' => null
                                                  )
                            ) 
    {
        extract($opts);
        
        $use = isset($use) ? $use : true;
        $stroke = isset($stroke) ? $stroke : false;
        $color = isset($color) ? $color : null;
        $alpha = isset($alpha) ? $alpha : null;
        
        $this->options['seriesStorage'][$series]['fill'] = $use;
        $this->options['seriesStorage'][$series]['fillAndStroke'] = $stroke;
        
        if($color !== null) {
            $this->options['seriesStorage'][$series]['fillColor'] = $color;
        }
        if($alpha !== null) {
            $this->options['seriesStorage'][$series]['fillAlpha'] = $alpha;
        }
        
        return $this;
    }
    
    public function setGrid(array $opts)
    {
        extract($opts);
        $on = isset($on) ? $on : true; 
        $color = isset($color) ? $color : null;
        $background = isset($background) ? $background : null;
        
        $this->options['grid']['drawGridLines'] = $on;
        if($color !== null) {
            $this->options['grid']['gridLineColor'] = $color;
        }
        if($background !== null) {
            $this->options['grid']['background'] = $background;
        }
    
        return $this;
    }
    
    public function setLegend(array $opts = array('on' => 'true', 
                                                  'location' => 'ne', 
                                                  'x' => 0, 
                                                  'y' => 0))
    {
        extract($opts);
        
        $on = isset($on) ? $on : 'true';
        $location = isset($location) ? $location : 'ne';
        $x = isset($x) ? $x : 0;
        $y = isset($y) ? $y : 0;
        
        
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
    
    public function setAxisOptions($axis, $name, $value)
    {
        if(strtolower($axis) === 'x' || strtolower($axis) === 'y') {
            $axis = strtolower($axis) . 'axis';
        
            if (in_array($name, array('min', 'max', 'numberTicks', 'tickInterval', 'numberTicks'))) {
                $this->options['axes'][$axis][$name] = $value;
            } elseif(in_array($name, array('showGridline', 'formatString'))) {
                $this->options['axes'][$axis]['tickOptions'][$name] = $value;
            }
        }
        
        return $this;
    }
    
    protected function getTypeOptions(array $options)
    {
        if(isset($this->types['default'])) {
            $options = array_merge_recursive($options, $this->types['default']->getOptions());
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
        $types = $this->types;
        $defaults = array(  'highlighter' => array('show' => false),
			                'cursor'      => array('showTooltip' => false, 'show' => false),
                            'pointLabels' => array('show' => false)
                             );
        if(isset($types['default'])) {
            $renderer = $types['default']->getRenderer();
            if(isset($renderer)) {
                $defaults['renderer'] = $renderer;
            }
            $defaults['rendererOptions'] = $types['default']->getRendererOptions();
            if(count($defaults['rendererOptions']) == 0) {
                unset($defaults['rendererOptions']);
            }
            $options['seriesDefaults'] = $defaults;
        }
        
        $seriesOptions = array();
        if (isset($this->options['seriesStorage'])) {
            foreach($this->options['seriesStorage'] as $title => $opts) {
                if(isset($types[$title])) {
                    $type = $types[$title];
                    $opts['renderer'] = $type->getRenderer();
                    array_merge_recursive($opts, $type->getSeriesOptions());
                }
                $opts['label'] = $title;
                
                $seriesOptions[] = $opts;
            }
        }
        
        $options['seriesStorage'] = $seriesOptions;
        $options['seriesDefaults'] = $defaults;
        
        return $options;
    }
    
    public function getOptionsJS()
    {
        $opts = $this->options;
        $opts['series'] = $opts['seriesStorage'];
        unset($opts['seriesStorage']);
        return $this->makeJSArray( $opts );
    }
    
    /**
     * Initializes default settings for using labels
     * @param string|\Altamira\Series $series
     * @param array $labels an array of strings for labels, in order
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useSeriesLabels( $series, array $labels = array() )
    {
        if ( !in_array( 'jqplot.pointLabels.min.js', $this->files ) ) {
            $this->files[] = 'jqplot.pointLabels.min.js';
        }
        $seriesTitle = $this->getSeriesTitle( $series );
        $this->seriesLabels[$seriesTitle] = $labels;
        return $this->setRecursiveOptVal( $this->options, 'seriesStorage', $seriesTitle, 'pointLabels', 'show', true )
                    ->setRecursiveOptVal( $this->options, 'seriesStorage', $seriesTitle, 'pointLabels', 'labels', $labels )
                    ->setRecursiveOptVal( $this->options, 'seriesStorage', $seriesTitle, 'pointLabels', 'edgeTolerance', 3 );
    }
    
    /**
     * Sets label setting option values
     * @param string $series
     * @param string $name
     * @param mixed $value
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesLabelSetting( $series, $name, $value )
    {
        if (  ( $name === 'location' && in_array( $value, array( 'n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw' ) ) ) 
            ||( in_array( $name, array( 'xpadding', 'ypadding', 'edgeTolerance', 'stackValue' ) ) ) ) {
            return $this->setRecursiveOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'pointLabels', $name, $value );
        }
        return $this;
    }
    
    /**
     * Determines the width of the line we will show, if we're showing it
     * @param string $series
     * @param mixed $value
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesLineWidth( $series, $value )
    {
        return $this->setRecursiveOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'lineWidth', $value );
    }
    
    /**
     * Determines whether we show the line for a series
     * @param string|\Altamira\Series $series
     * @param bool $bool
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesShowLine( $series, $bool )
    {
        return $this->setRecursiveOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'showLine', $bool );
    }
    
    /**
     * Determines whether we show the marker for a series
     * @param string|\Altamira\Series $series
     * @param bool $bool
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesShowMarker( $series, $bool )
    {
        return $this->setRecursiveOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'showMarker', $bool );
    }
    
    /**
     * Sets the style of the marker
     * @param string|\Altamira\Series $series
     * @param string $value
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesMarkerStyle( $series, $value )
    {
        return $this->setRecursiveOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'markerOptions', 'style', $value );
    }
    
    /**
     * Sets the size of the marker
     * @param string|\Altamira\Series $series
     * @param mixed $value
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesMarkerSize( $series, $value )
    {
        return $this->setRecursiveOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'markerOptions', 'size', $value );
    }
    
    /**
     * Sets the labels demarcated on a given axis
     * @param string $axis
     * @param array $ticks
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setAxisTicks( $axis, array $ticks )
    {
        return $this->setRecursiveOptVal( $this->options, 'axes', $axis.'axis', 'ticks', $ticks );
    }
    
}