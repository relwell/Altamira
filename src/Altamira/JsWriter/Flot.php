<?php 

namespace Altamira\JsWriter;

use Altamira\JsWriter\Ability;

class Flot 
    extends JsWriterAbstract
    implements Ability\Cursorable,
               Ability\Datable,
               Ability\Fillable,
               Ability\Griddable,
               Ability\Highlightable,
               Ability\Legendable,
               Ability\Shadowable,
               Ability\Zoomable
{
    protected $dateAxes = array('x'=>false, 'y'=>false);
    protected $zooming = false;
    
    protected function generateScript()
    {
        $name = $this->chart->getName();
        $dataArrayJS = '[';
        foreach ($this->chart->getSeries() as $title=>$series) {
            
            $dataArrayJS .= $counter++ > 0 ? ', ' : '';
            
            $dataArrayJS .= '{';
            
            // associate Xs with Ys in cases where we need it
            $data = $series->getData();
            
            $oneDimensional = array_keys($data) == range(0, count($data)-1, 1);
            
            foreach ($data as $key=>$val) { 
                // sorry, no point labels in flot
                $data[$key] = is_array($val) ? array_slice($val, 0, 2) : array(($oneDimensional? $key+1 : $key), $val);
                
                foreach ($this->dateAxes as $axis=>$flag) { 
                    if ($flag) {
                        switch ($axis) {
                            case 'x':
                                
                                $date = \DateTime::createFromFormat('m/d/Y', $data[$key][0]);
                                $data[$key][0] = $date->getTimestamp() * 1000;
                                break;
                            case 'y':
                                $date = \DateTime::createFromFormat('m/d/Y', $data[$key][1]);
                                $data[$key][0] = $date->getTimestamp() * 1000;
                                break;
                        }
                    }
                }
                
            };
            
            if ($title) {
                $dataArrayJS .= 'label: "'.str_replace('"', '\\"', $title).'", ';
            }
            
            $dataArrayJS .= 'data: '.$this->makeJSArray($data);
            
            if ($series->usesLabels()) {
                $dataArrayJS .= ", points: {'show': 'true'}";
            }
            
            $dataArrayJS .= ", lines: {'show': 'true'}";

            $dataArrayJS .= '}';
        }
        
        
        $dataArrayJS .= ']';
        
        $optionsJs = ($js = $this->getOptionsJs()) ? ", {$js}" : ', {}';
        
        $extraFunctionCalls = array();
        
        if ($this->zooming) {
            $extraFunctionCalls[] = <<<ENDJS
placeholder.bind("plotselected", function (event, ranges) {
    jQuery.plot(placeholder, {$dataArrayJS},
      $.extend(true, {}{$optionsJs}, {
      xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
      yaxis: { min: ranges.yaxis.from, max: ranges.yaxis.to }
  }));
});
placeholder.on('dblclick', function(){ plot.clearSelection(); jQuery.plot(placeholder, {$dataArrayJS}{$optionsJs}); });     
ENDJS;
            
        }
        
        $extraFunctionCallString = implode('', $extraFunctionCalls);
        
        return <<<ENDSCRIPT
jQuery(document).ready(function() {
    var placeholder = jQuery('#{$name}');
    var plot = jQuery.plot(placeholder, {$dataArrayJS}{$optionsJs});
    {$extraFunctionCallString}
});
        
ENDSCRIPT;
        
    }
    
    public function setAxisOptions($axis, $name, $value)
    {
        if(strtolower($axis) === 'x' || strtolower($axis) === 'y') {
            $axis = strtolower($axis) . 'axis';
    
            if (isset($this->nativeOpts[$axis][$name])) {
                $this->options[$axis][$name] = $value;
            } else {
                $key = 'axes.'.$axis.'.'.$name;

                if (isset($this->optsMapper[$key])) {
                    $this->setOpt($this->options, $this->optsMapper[$key], $value);
                }
                
                if ($name == 'formatString') {
                    $this->options[$axis]['tickFormatter'] = $this->getCallbackPlaceholder('function(val, axis){return "'.$value.'".replace(/%d/, val);}');
                }
                
            }
        }

        return $this;
    }

    //@todo handle type options correctly
    protected function getTypeOptions(array $options)
    {return $options;
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
    
    //@todo handle series default transformations
    protected function getSeriesOptions(array $options)
    {return $options; 
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
        foreach ($this->optsMapper as $opt => $mapped)
        {
            if (($currOpt = $this->getOptVal($this->options, $opt)) && ($currOpt !== null)) {
                $this->setOpt(&$this->options, $mapped, $currOpt);
                $this->unsetOpt($this->options, $opt);
            }
        }
        
        return $this->makeJSArray($this->options);
    }
    
    // these are helper functions to transform jqplot options to flot
    private function getOptVal(array $opts, $option)
    {
        $ploded = explode('.', $option);
        $arr = $opts;
        $val = null;
        while ($curr = array_shift($ploded)) {
            if (isset($arr[$curr])) {
                if (is_array($arr[$curr])) {
                    $arr = $arr[$curr];
                } else {
                    return $arr[$curr];
                }
            } else {
                return null;
            }
        }
        return $arr;
    }
    
    private function setOpt(array &$opts, $mapperString, $val)
    {
        $ploded = explode('.', $mapperString);
        $arr = &$opts;
        while ($curr = array_shift($ploded)) {
            if (isset($arr[$curr])) {
                if (is_array($arr[$curr])) {
                    $arr = &$arr[$curr];
                } else {
                    $arr[$curr] = $val;
                }
            } else {
                $arr[$curr] = empty($ploded) ? $val : array();
                $arr = &$arr[$curr];
            }
        }
    }
    
    private function unsetOpt(array &$opts, $mapperString)
    {
        $ploded = explode('.', $mapperString);
        $arr = &$opts;
        while ($curr = array_shift($ploded)) {
            if (isset($arr[$curr])) {
                if (is_array($arr[$curr])) {
                    $arr = &$arr[$curr];
                } else {
                    unset($arr[$curr]);
                }
            }
        }
    }
    
    public function useHighlighting(array $opts = array('size'=>7.5))
    {
        $this->options['grid']['hoverable'] = 'true';
        $this->options['grid']['autoHighlight'] = 'true';
    
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
        $this->options[$axis.'axis']['timeformat'] = '%d-%b-%y';
        
        array_push($this->files, 'jquery.flot.time.js');
    
        return $this;
    }
    
    public function useZooming( array $options = array('mode'=>'xy') )
    {
        $this->zooming = true;
        $this->options['selection'] = array('mode' => $options['mode'] );
        $this->files[] = 'jquery.flot.selection.js';
    }
    
    public function setGrid(array $opts)
    {
        
        $gridMapping = array('on'=>'show', 
                             'background'=>'backgroundColor'
                            );
        
        foreach ($opts as $key=>$value) {
            if ( in_array($key, $this->nativeOpts['grid']) ) {
                $this->options['grid'][$key] = $value;
            } else if ( in_array($key, $gridMapping) ) {
                $this->options['grid'][$gridMapping[$key]] = $value;
            }
        }
        
        return $this;
        
    }
    
    public function setLegend(array $opts = array('on' => 'true', 
                                                  'location' => 'ne', 
                                                  'x' => 0, 
                                                  'y' => 0))
    {        
        $opts['on'] = isset($opts['on']) ? $opts['on'] : 'true';
        $opts['location'] = isset($ops['location']) ? $opts['location'] : 'ne';

        $legendMapper = array('on' => 'show',
                              'location' => 'position');
        
        foreach ($opts as $key=>$val) {
            if ( in_array($key, $this->nativeOpts['legend']) ) {
                $this->options['legend'][$key] = $val;
            } else if ( in_array($key, $legendMapper) ) {
                $this->options['legend'][$legendMapper[$key]] = $val;
            }
        }
        
        
        return $this;
    }
    
    public function setFill($series, $opts = array('use' => true,
                                                   'stroke' => false,
                                                   'color' => null,
                                                   'alpha' => null
                                                  ))
    {
        
        // @todo add a method of telling flot whether the series is a line, bar, point
        if (isset($opts['use']) && $opts['use'] == true) {
            $this->options['series'][$series]['line']['fill'] = 'true';
            
            if (isset($opts['color'])) {
                $this->options['series'][$series]['line']['fillColor'] = $opts['color'];
            }
        }
        
        return $this;
    }
    
    public function setShadow($series, $opts = array('use'=>true,
                                                     'angle'=>45,
                                                     'offset'=>1.25,
                                                     'depth'=>3,
                                                     'alpha'=>0.1))
    {
        
        if (isset($opts['use']) && $opts['use']) {
            $this->options['series'][$series]['shadowSize'] = isset($opts['depth']) ? $opts['depth'] : 3;
        }
        
        return $this;
    }
    

    // maps jqplot-originating option data structure to flot
    private $optsMapper = array('axes.xaxis.tickInterval' => 'xaxis.tickSize',
                                'axes.xaxis.min'          => 'xaxis.min',
                                'axes.xaxis.max'          => 'xaxis.max',
                                'axes.xaxis.mode'         => 'xaxis.mode',
                                'axes.xaxis.ticks'        => 'xaxis.ticks',
            
                                'axes.yaxis.tickInterval' => 'yaxis.tickSize',
                                'axes.yaxis.min'          => 'yaxis.min',
                                'axes.yaxis.max'          => 'yaxis.max',
                                'axes.yaxis.mode'         => 'yaxis.mode',
                                'axes.yaxis.ticks'        => 'yaxis.ticks',
                                
                                'legend.show'             => 'legend.show',
                                'legend.location'         => 'legend.position',
                                'seriesColors'            => 'colors',
                                );
    
    
    // api-native functionality
    private $nativeOpts = array('legend' => array(  'show'=>null,
                                                    'labelFormatter'=>null,
                                                    'labelBoxBorderColor'=>null,
                                                    'noColumns'=>null,
                                                    'position'=>null,
                                                    'margin'=>null,
                                                    'backgroundColor'=>null,
                                                    'backgroundOpacity'=>null,
                                                    'container'=>null),

                                'xaxis' => array(   'show'=>null,
                                                    'position'=>null,
                                                    'mode'=>null,
                                                    'color'=>null,
                                                    'tickColor'=>null,
                                                    'min'=>null,
                                                    'max'=>null,
                                                    'autoscaleMargin'=>null,
                                                    'transform'=>null,
                                                    'inverseTransform'=>null,
                                                    'ticks'=>null,
                                                    'tickSize'=>null,
                                                    'minTickSize'=>null,
                                                    'tickFormatter'=>null,
                                                    'tickDecimals'=>null,
                                                    'labelWidth'=>null,
                                                    'labelHeight'=>null,
                                                    'reserveSpace'=>null,
                                                    'tickLength'=>null,
                                                    'alignTicksWithAxis'=>null,
                                                ),
                                                
                                'yaxis' => array(   'show'=>null,
                                                    'position'=>null,
                                                    'mode'=>null,
                                                    'color'=>null,
                                                    'tickColor'=>null,
                                                    'min'=>null,
                                                    'max'=>null,
                                                    'autoscaleMargin'=>null,
                                                    'transform'=>null,
                                                    'inverseTransform'=>null,
                                                    'ticks'=>null,
                                                    'tickSize'=>null,
                                                    'minTickSize'=>null,
                                                    'tickFormatter'=>null,
                                                    'tickDecimals'=>null,
                                                    'labelWidth'=>null,
                                                    'labelHeight'=>null,
                                                    'reserveSpace'=>null,
                                                    'tickLength'=>null,
                                                    'alignTicksWithAxis'=>null,
                                                ),
                                                
                                 'xaxes' => null,
                                 'yaxes' => null,

                                 'series' => array(
                                                    'lines' => array('show'=>null, 'lineWidth'=>null, 'fill'=>null, 'fillColor'=>null),
                                                    'points'=> array('show'=>null, 'lineWidth'=>null, 'fill'=>null, 'fillColor'=>null),
                                                    'bars' => array('show'=>null, 'lineWidth'=>null, 'fill'=>null, 'fillColor'=>null),
                                                  ),
                                                  
                                 'points' => array('radius'=>null, 'symbol'=>null),
                                 
                                 'bars' => array('barWidth'=>null, 'align'=>null, 'horizontal'=>null),
                                 
                                 'lines' => array('steps'=>null),
                                
                                 'shadowSize' => null,                                
                                
                                 'colors' => null,
                                 
                                 'grid' =>  array(  'show'=>null,
                                                    'aboveData'=>null,
                                                    'color'=>null,
                                                    'backgroundColor'=>null,
                                                    'labelMargin'=>null,
                                                    'axisMargin'=>null,
                                                    'markings'=>null,
                                                    'borderWidth'=>null,
                                                    'borderColor'=>null,
                                                    'minBorderMargin'=>null,
                                                    'clickable'=>null,
                                                    'hoverable'=>null,
                                                    'autoHighlight'=>null,
                                                    'mouseActiveRadius'=>null
                                                )

                                 
                                );
    
}