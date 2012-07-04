<?php

namespace Altamira;

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

	public function useHighlighting($size = 7.5)
	{
		$this->files = array_merge_recursive(array('jqplot.highlighter.min.js'), $this->files);
		$this->options['highlighter'] = array('sizeAdjust' => $size);

		return $this;
	}

	public function useZooming()
	{
		$this->files = array_merge_recursive(array('jqplot.cursor.min.js'), $this->files);
		$this->options['cursor'] = array('zoom' => true, 'show' => true);

		return $this;
	}

	public function useCursor()
	{
		$this->files = array_merge_recursive(array('jqplot.cursor.min.js'), $this->files);
		$this->options['cursor'] = array('show' => true, 'showTooltip' => true);

		return $this;
	}

	public function useDates($axis = 'x')
	{
		$this->files = array_merge_recursive(array('jqplot.dateAxisRenderer.min.js'), $this->files);
		if(strtolower($axis) === 'x') {
			$this->options['axes']['xaxis']['renderer'] = '#$.jqplot.DateAxisRenderer#';
		} elseif(strtolower($axis) === 'y') {
			$this->options['axes']['yaxis']['renderer'] = '#$.jqplot.DateAxisRenderer#';
		}

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

		return $this->files;
	}

	public function getScript()
	{
		$output  = '$(document).ready(function(){';
		$output .= '$.jqplot.config.enablePlugins = true;';

		$num = 0;
		$vars = array();

		$useTags = false;
		if( (isset($this->types['default']) && $this->types['default']->getUseTags()) ||
			(isset($this->useTags) && $this->useTags) )
			$useTags = true;

		foreach($this->series as $series) {
			$num++;
			$data = $series->getData($useTags);

			$varname = 'plot_' . $this->name . '_' . $num;
			$vars[] = '#' . $varname . '#';
			$output .= $varname . ' = ' . $this->makeJSArray($data) . ';';
		}

		$output .= 'plot = $.jqplot("' . $this->name . '", ';
		$output .= $this->makeJSArray($vars);
		$output .= ', ';
		$output .= $this->getOptionsJS();
		$output .= ');';
		$output .= '});';

		return $output;
	}

	protected function runSeriesOptions()
	{
		if(isset($this->types['default'])) {
			$defaults = $this->options['seriesDefaults'];
			$renderer = $this->types['default']->getRenderer();
			if(isset($renderer))
				$defaults['renderer'] = $renderer;
			$defaults['rendererOptions'] = $this->types['default']->getRendererOptions();
			if(count($defaults['rendererOptions']) == 0)
				unset($defaults['rendererOptions']);
			$this->options['seriesDefaults'] = $defaults;
		}

		$seriesOptions = array();
		foreach($this->series as $series) {
			$opts = $series->getOptions();
			$title = $series->getTitle();
			if(isset($this->types[$title])) {
				$type = $this->types[$title];
				$opts['renderer'] = $type->getRenderer();
				array_merge_recursive($opts, $type->getSeriesOptions());
			}
			$opts['label'] = $title;
			$seriesOptions[] = $opts;
		}
		$this->options['series'] = $seriesOptions;
	}

	protected function runTypeOptions()
	{
		if(isset($this->types['default'])) {
			$this->options = array_merge_recursive($this->options, $this->types['default']->getOptions());
		}

		if(isset($this->options['axes'])) {
			foreach($this->options['axes'] as $axis => $contents) {
				if(isset($this->options['axes'][$axis]['renderer']) && is_array($this->options['axes'][$axis]['renderer'])) {
					$this->options['axes'][$axis]['renderer'] = $this->options['axes'][$axis]['renderer'][0];
				}
			}
		}
	}

	protected function getOptionsJS()
	{
		$this->runSeriesOptions();
		$this->runTypeOptions();
		return $this->makeJSArray($this->options);
	}

	protected function makeJSArray($array)
	{
		$options = json_encode($array);
		return preg_replace('/"#(.*?)#"/', '$1', $options);
	}
	
	public function setLibrary($library)
	{
	    $this->library = $library;
	}
}
