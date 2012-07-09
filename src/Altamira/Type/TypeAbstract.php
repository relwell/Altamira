<?php

namespace Altamira\Type;

abstract class TypeAbstract
{
	protected $pluginFiles = array();
	protected $renderer;
	protected $options;

	protected $allowedRendererOptions = array();
	
	public function __construct($library = 'jqPlot')
	{
	    $config = \parse_ini_file(__DIR__.'/TypeConfig.ini', true);
	    if (! isset($config[strtolower($library)]) ) {
	        throw new \Exception('This chart type is not supported in this library.');
	    }
	    
	    $libConfig = $config[strtolower($library)];

	    $class = end(explode('\\', strtolower(get_class($this))));
	    
	    foreach ( preg_grep("/$class\./i", array_keys($libConfig)) as $key ) {
	        $attribute = preg_replace("/{$class}\./i", '', $key);
	        $this->{$attribute} = $libConfig[$key];
	    }
	}

	public function getFiles()
	{
		return $this->pluginFiles;
	}

	public function getRenderer()
	{
		if(isset($this->renderer)) {
			return '#' . $this->renderer . '#';
		} else {
			return null;
		}
	}

	public function getOptions()
	{
		return array();
	}

	public function getSeriesOptions()
	{
		return array();
	}

	public function getRendererOptions()
	{
		$opts = array();
		foreach($this->allowedRendererOptions as $opt) {
			if(isset($this->options[$opt]))
				$opts[$opt] = $this->options[$opt];
		}
		return $opts;
	}

	public function getUseTags()
	{
		return false;
	}

	public function setOption($name, $value)
	{
		$this->options[$name] = $value;

		return $this;
	}
}

?>