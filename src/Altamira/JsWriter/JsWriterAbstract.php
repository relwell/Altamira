<?php 

namespace Altamira\JsWriter;

abstract class JsWriterAbstract
{
    protected $chart;
    
    public function __construct(\Altamira\Chart $chart)
    {
        $this->chart = $chart;
    }
        
    abstract public function getScript();
    
}