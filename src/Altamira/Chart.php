<?php

namespace Altamira;

use Altamira\JsWriter\JqPlot;

class Chart
{
	protected $name;
	protected $handler;

	protected $useTags = false;
	protected $types = array();
	protected $options = array(	'seriesDefaults' => array('pointLabels' => array('show' => false)),
					'highlighter' => array('show' => false),
					'cursor' => array('showTooltip' => false, 'show' => false)
				  );
	protected $series = array();
	protected $labels = array();
	protected $files = array();
	
	protected $library;

	protected $typeNamespace = '\\Altamira\\Type\\';

	public function __construct($name = null, $library = 'jqPlot')
	{
		if(isset($name))
			$this->name = $name;
		
		$this->library = $library;

		$this->jsWriter = $this->getJsWriter();
		
		return $this;
	}
	
	public function getName()
	{
	    return $this->name;
	}

	public function setTitle($title)
	{
		$this->options['title'] = $title;

		return $this;
	}
	
	public function getUseTags()
	{
	    return $this->useTags;
	}

	public function useHighlighting($size = 7.5)
	{
	    $this->jsWriter->useHighlighting($size);

		return $this;
	}

	public function useZooming()
	{
	    $this->jsWriter->useZooming();
	    
		return $this;
	}

	public function useCursor()
	{
        $this->jsWriter->useCursor();
	}

	public function useDates($axis = 'x')
	{
		$this->jsWriter->useDates($axis);

		return $this;
	}

	public function setAxisTicks($axis, $ticks)
	{
		if(strtolower($axis) === 'x') {
			$this->options['axes']['xaxis']['ticks'] = $ticks;
		} elseif(strtolower($axis) === 'y') {
			$this->options['axes']['yaxis']['ticks'] = $ticks;
		}

		return $this;
	}

	public function setAxisOptions($axis, $name, $value)
	{
		if(strtolower($axis) === 'x' || strtolower($axis) === 'y') {
			$axis = strtolower($axis) . 'axis';

			if (in_array($name, array('min', 'max', 'numberTicks', 'tickInterval', 'numberTicks'))) {
				$this->options['axes'][$axis][$name] = $value;
			} elseif(in_array($name, array('showGridline', 'formatString'))) {
				$this->options['axes'][$axis]['tickOptions'][$name] = $value;
			}
		}

		return $this;
	}

	public function setSeriesColors($colors)
	{
		$this->options['seriesColors'] = $colors;

		return $this;
	}

	public function setAxisLabel($axis, $label)
	{
		if(strtolower($axis) === 'x') {
			$this->options['axes']['xaxis']['label'] = $label;
		} elseif(strtolower($axis) === 'y') {
			$this->options['axes']['yaxis']['label'] = $label;
		}

		return $this;
	}

	public function setType($type, $series = null)
	{
		if(isset($series) && isset($this->series[$series])) {
			$series = $this->series[$series];
			$title = $series->getTitle();
		} else {
			$title = 'default';
		}

		$className =  $this->typeNamespace . ucwords($type);
		if(class_exists($className))
			$this->types[$title] = new $className($this->library);

		return $this;
	}

	public function setTypeOption($name, $option, $series = null)
	{
		if(isset($series)) {
			$title = $series;
		} else {
			$title = 'default';
		}

		if(isset($this->types[$title]))
			$this->types[$title]->setOption($name, $option);

		return $this;
	}

	public function useTags($use = true)
	{
		$this->useTags = $use;

		return $this;
	}

	public function setLegend($on = true, $location = 'ne', $x = 0, $y = 0)
	{
		if(!$on) {
			unset($this->options['legend']);
		} else {
			$legend = array();
			$legend['show'] = true;
			if($location == 'outside' || $location == 'outsideGrid') {
				$legend['placement'] = $location;
			} else {
				$legend['location'] = $location;
			}
			if($x != 0)
				$legend['xoffset'] = $x;
			if($y != 0)
				$legend['yoffset'] = $y;
			$this->options['legend'] = $legend;
		}

		return $this;
	}

	public function setGrid($on = true, $color = null, $background = null)
	{
		$this->options['grid']['drawGridLines'] = $on;
		if(isset($color))
			$this->options['grid']['gridLineColor'] = $color;
		if(isset($background))
			$this->options['grid']['background'] = $background;

		return $this;
	}

	public function addSeries(Series $series)
	{
		$this->series[$series->getTitle()] = $series;

		return $this;
	}

	public function getPluginList()
	{
		return array();

		return $this;
	}

	public function getDiv($width = 500, $height = 400)
	{
	    $styleOptions = array('width'    =>    $width.'px', 
	                          'height'   =>    $height.'px'
	                         );
	    
	    return ChartRenderer::render( $this, $styleOptions );
	}

	public function getFiles()
	{
		foreach($this->types as $type) {
			$this->files = array_merge_recursive($this->files, $type->getFiles());
		}

		foreach($this->series as $series) {
			$this->files = array_merge_recursive($this->files, $series->getFiles());
		}

		return array_merge($this->files, $this->jsWriter->getFiles());
	}

	public function getScript()
	{
	    return $this->jsWriter->getScript();
	}
	
	protected function getJsWriter()
	{
	    if ($this->jsWriter) {
	        return $this->jsWriter;
	    }
	    
	    $className = '\\Altamira\\JsWriter\\'.ucfirst($this->library);

	    if (class_exists($className)) {
	        $instance = new $className($this); 
	        return $instance;
	    }
	    
	    throw new \Exception("No JsWriter by name of {$className}");
	    
	}

	public function setLibrary($library)
	{
	    $this->library = $library;
	}
	
	public function getLibrary()
	{
	    return $this->library;
	}
	
	public function getTypes()
	{
	    return $this->types;
	}
	
	public function getSeries()
	{
	    return $this->series;
	}
	
	public function getOptions()
	{
	    return $this->options;
	}
}
