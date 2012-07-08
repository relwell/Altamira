<?php 

namespace Altamira\JsWriter;

abstract class JsWriterAbstract
{
    protected $chart;
    
    protected $options = array();
    
    public function __construct(\Altamira\Chart $chart)
    {
        $this->chart = $chart;
    }
    
    public function getScript()
    {
        $this->options = $this->getTypeOptions($this->getSeriesOptions($this->chart->getOptions()));
        return $this->generateScript();
    }
    
    
    public function makeJSArray($array)
    {
        $options = json_encode($array);
        return preg_replace('/"#(.*?)#"/', '$1', $options);
    }
    
    abstract protected function getSeriesOptions(array $options);
    abstract protected function getTypeOptions(array $options);
    abstract protected function generateScript();
}