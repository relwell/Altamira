<?php 

namespace Altamira\JsWriter;

abstract class JsWriterAbstract
{
    protected $chart;
    
    protected $options = array();
    protected $files = array();
    protected $callbacks = array();
    
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
        $optionString = preg_replace('/"#(.*?)#"/', '$1', $options);
        
        foreach ( $this->callbacks as $placeHolder => $callback ) {
            $optionString = str_replace("\"{$placeHolder}\"", $callback, $optionString);
        }
        
        return $optionString;
    }
    
    protected function getCallbackPlaceholder( $callback )
    {
        $index = count($this->callbacks);
        $uid = spl_object_hash( $this );
        
        $key = sprintf('%s_%s', $uid, $index);
        
        $this->callbacks[$key] = $callback;
        
        return $key; 
    }
    
    public function getFiles()
    {
        return $this->files;
    }
    
    public function getOptionsForSeries($series)
    {
        return $this->options['series'][$series];
    }
    
    public function getSeriesOption($series, $option)
    {
        return $this->options['series'][$series][$option];
    }
    
    public function setSeriesOption($series, $name, $value)
    {
        $this->options['series'][$series][$name] = $value;
        
        return $this;
    }
    
    abstract protected function getSeriesOptions(array $options);
    abstract protected function getTypeOptions(array $options);
    abstract protected function generateScript();
}