<?php

namespace Altamira;

use Altamira\JsWriter\JsWriterAbstract;
use Altamira\ChartDatum\ChartDatumAbstract;

class Series
{
    /**
     * Counter used when a series title is not provided.
     * @var int
     */
	static protected $count = 0;
	
	/**
	 * An array of ChartDatumAbstract children
	 * @var array
	 */
	protected $data = array();
	
	protected $useLabels = false;

	protected $jsWriter;
	
	protected $title;
	protected $labels= array();
	protected $files = array();

	/**
	 * Constructor method
	 * @param array            $data an array of ChartDatumAbstract instances
	 * @param string           $title the desired title of the series (used to label a series)
	 * @param JsWriterAbstract $jsWriter the jswriter, dependency-injected for rendering
	 * @throws \UnexpectedValueException
	 */
	public function __construct($data, $title = null, JsWriterAbstract $jsWriter)
	{
		self::$count++;

		$tagcount = 0;
		foreach($data as $datum) {
            if (! $datum instanceof ChartDatumAbstract ) {
                throw new \UnexpectedValueException( "The data array must consist of instances inheriting from ChartDatumAbstract" );
            }
            $datum->setJsWriter($jsWriter)
                  ->setSeries($this);
		}
		$this->data = $data;

		if(isset($title)) {
			$this->title = $title;
		} else {
			$this->title = 'Series ' . self::$count;
		}

		$this->jsWriter = $jsWriter;
		$this->jsWriter->initializeSeries($this);
	}

	public function getFiles()
	{
		return $this->files;
	}

	public function setShadow($opts = array('use'=>true, 
                                            'angle'=>45, 
                                            'offset'=>1.25, 
                                            'depth'=>3, 
                                            'alpha'=>0.1))
	{
	    if ( $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Shadowable ) {
	        $this->jsWriter->setShadow($this->getTitle(), $opts);
	    }
	    
		return $this;
	}

	public function setFill($opts = array('use' => true, 
                                                   'stroke' => false, 
                                                   'color' => null, 
                                                   'alpha' => null
                                                  ))
	{
        if ( $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Fillable ) {
    	    $this->jsWriter->setFill($this->getTitle(), $opts);
        }
	    
		return $this;
	}

	public function getData()
	{
	    return $this->data;
	}

	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	public function useLabels( $labels = array() )
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Labelable) {
    		$this->useLabels = true;
    		$this->jsWriter->useSeriesLabels($this, $labels);
	    }

		return $this;
	}

	public function setLabelSetting($name, $value)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Labelable) {
    		$this->jsWriter->setSeriesLabelSetting($this, $name, $value);
	    }
		
		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setOption($name, $value)
	{
		$this->jsWriter->setSeriesOption($this, $name, $value);

		return $this;
	}
	
	public function getOption($option)
	{
	    return $this->jsWriter->getSeriesOption($this->getTitle(), $option);
	}

	public function getOptions()
	{
        return $this->jsWriter->getOptionsForSeries($this->getTitle());
	}
	
	public function setLineWidth($val)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesLineWidth($this, $val);
	    }
	    return $this;
	}
	
	public function showLine($bool = true)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
    	    $this->jsWriter->setSeriesShowLine($this, $bool);
	    }
	    return $this;
	}
	
	public function showMarker($bool = true)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesShowMarker($this, $bool);
	    }
	    return $this;
	}
	
	public function setMarkerStyle($value)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesMarkerStyle($this, $value);
	    }
	    return $this;
	}
	
	public function setMarkerSize($value)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesMarkerSize($this, $value);
	    }
	    return $this;
	}
	
	public function setType($type)
	{
	    $this->jsWriter->setType($type, $this);
	    
	    return $this;
	}
}
