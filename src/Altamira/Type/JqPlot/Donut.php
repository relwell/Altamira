<?php

namespace Altamira\Type\JqPlot;

class Donut extends Pie
{
    protected $options = array('seriesDefaults'=>array('rendererOptions'=>array()));
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function setOption($name, $value)
    {
        $this->options['seriesDefaults']['rendererOptions'][$name] = $value;
        return $this; 
    }
}

?>