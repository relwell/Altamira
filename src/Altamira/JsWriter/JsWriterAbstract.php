<?php 

namespace Altamira\JsWriter;

abstract class JsWriterAbstract
{
    protected $chart;
    
    public function __construct(\Altamira\Chart $chart)
    {
        $this->chart = $chart;
    }
    
    public function getScript()
    {
        $this->runSeriesOptions();
        $this->runTypeOptions();
        return $this->generateScript();
    }
    
    
    public function makeJSArray($array)
    {
        $options = json_encode($array);
        return preg_replace('/"#(.*?)#"/', '$1', $options);
    }
    
    abstract protected function runSeriesOptions();
    abstract protected function runTypeOptions();
    abstract protected function generateScript();
}