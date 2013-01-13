<?php
/**
 * Class definition for \Altamira\JsWriter\D3
 * @author relwell
 */
namespace Altamira\JsWriter;
use Altamira\JsWriter\Ability;
/**
 * JsWriter responsible for storing options and 
 * rendering values to cause D3 to render a specific chart.
 * @namespace \Altamira\JsWriter
 * @package JsWriter
 * @author relwell
 */
class D3
    extends JsWriterAbstract
{
    /**
     * Identifies the string value of which library this jsWriter is responsible for
     * @var string
     */
    const LIBRARY = 'd3';
    
    /**
     * Used to identify the type namespace for this particualr JsWriter 
     * @var string
     */
    protected $typeNamespace = '\\Altamira\\Type\\D3\\';
    
    /** 
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getOptionsJS()
     */
    protected function getOptionsJS() 
    {
        // TODO Auto-generated method stub
    }

    /** 
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getScript()
     */
     public function getScript() 
     {
        // TODO Auto-generated method stub
     }
}