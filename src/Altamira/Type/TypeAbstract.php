<?php

namespace Altamira\Type;

abstract class TypeAbstract
{
	protected $pluginFiles;
	protected $renderer;
	protected $options;

	protected $allowedRendererOptions = array();

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