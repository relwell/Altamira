<?php

namespace Malwarebytes\Altamira\Type\JqPlot;

class Pie extends \Malwarebytes\Altamira\Type\TypeAbstract
{
	protected $allowedRendererOptions = array();

	public function getUseTags()
	{
		return true;
	}
}

?>
