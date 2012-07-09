<?php 

namespace Altamira\JsWriter;

abstract class JsWriterAbstract
{
    protected $chart;
    
    protected $options = array();
    protected $files = array();
    
    public function __construct(\Altamira\Chart $chart)
    {
        $this->chart = $chart;
    }
    
    public function getScript()
    {
        $options = array_merge($this->chart->getOptions(), $this->options);
        $this->options = $this->getTypeOptions($this->getSeriesOptions($options));
        
        return $this->generateScript();
    }
    
    
    public function makeJSArray($array)
    {
        $options = json_encode($array);
        return preg_replace('/"#(.*?)#"/', '$1', $options);
    }
    
    public function getFiles()
    {
        return $this->files;
    }
    
    abstract protected function getSeriesOptions(array $options);
    abstract protected function getTypeOptions(array $options);
    abstract protected function generateScript();
}