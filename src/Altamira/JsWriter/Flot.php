<?php 

namespace Altamira\JsWriter;

use Altamira\JsWriter\Ability;

class Flot 
    extends JsWriterAbstract
    implements Ability\Cursorable
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
        $newOpts = array();
        
        $getOptVal = function(array $opts, $option){
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
        };
        
        $setOpt = function(array &$opts, $mapperString, $val){
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
        };
        
        foreach ($this->optsMapper as $opt => $mapped)
        {
            if (($currOpt = $getOptVal($this->options, $opt)) && ($currOpt !== null)) {
                $setOpt(&$newOpts, $mapped, $currOpt);
            }
        }
        
        
        
        return $this->makeJSArray($newOpts);
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
                                'label.location'          => 'legend.position',
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