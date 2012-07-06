<?php 

namespace Altamira\JsWriter;

abstract class JsWriterAbstract
{
    protected $chart;
    
    public function __construct(\Altamira\Chart $chart)
    {
        $this->chart = $chart;
    }
    
    public function makeJSArray($array)
    {
        $options = json_encode($array);
        return preg_replace('/"#(.*?)#"/', '$1', $options);
    }
        
    abstract public function getScript();
    
}