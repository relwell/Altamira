<?php

namespace Altamira\Type\Flot;

class Bar extends \Altamira\Type\TypeAbstract
{

	public function getOptions()
	{
        $opts = array();
        
        
        
		return $opts;
	}

	public function getRendererOptions()
	{
		$opts = array();
		if(isset($this->options['horizontal']) && $this->options['horizontal'])
			$opts['barDirection'] = 'horizontal';

		foreach($this->allowedOptions as $item) {
			if(isset($this->options[$item]))
				$opts[$item] = $this->options[$item];
		}

		return $opts;
	}

	public function getUseTags()
	{
		if(isset($this->options['horizontal']) && $this->options['horizontal'])
			return true;

		return false;
	}
}

?>