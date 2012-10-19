<?php

namespace Altamira\ChartDatum;

use Altamira\JsWriter\JsWriterAbstract;

/**
 * Data abstraction class for points.
 * @author relwell
 *
 */
abstract class ChartDatumAbstract implements \ArrayAccess
{
    /**
     * Contains all information about 
     * @var array
     */
    protected $datumData;
    
    /**
     * Used to determine how we render certain values
     */
    protected $jsWriter;
    
    /**
     * Constructor method
     * @param array       $dimensions
     * @param string|null $label
     * @throw \InvalidArgumentException
     */
    abstract public function __construct( array $dimensions, $label = null );
    
    /**
     * Used for rendering into json string
     * @param  boolean $useLabel whether to add label to the array
     * @return array
     */
    abstract public function toArray( $useLabel = false );
    
    /**
     * Set the label for this datum
     * @param string $label
     * @return \Altamira\ChartDatumAbstract
     */
    public function setLabel( $label )
    {
        $this['label'] = $label;
        return $this;
    }
    
    /**
     * Sets the JsWriter relative to the datum
     * @param JsWriterAbstract $jsWriter
     * @return \Altamira\ChartDatum\ChartDatumAbstract
     */
    public function setJsWriter( JsWriterAbstract $jsWriter )
    {
        $this->jsWriter = $jsWriter;
        return $this;
    }
    
    /**
     * Convenience method for accessing label the same way we set it. 
     * @return string
     */
    public function getLabel()
    {
        return $this['label'];
    }
    
	/* (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists ($offset)
    {
        return isset($this->datumData[$offset]);
    }

	/* (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet ($offset)
    {
        return isset($this->datumData[$offset]) ? $this->datumData[$offset] : false;
    }

	/* (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet ($offset, $value)
    {
        $this->datumData[$offset] = $value;
    }

	/* (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset ($offset)
    {
        unset($this->datumData[$offset]);
    }

}