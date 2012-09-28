<?php

namespace Malwarebytes\AltamiraBundle\Altamira\Type\JqPlot;

class Pie extends \Malwarebytes\AltamiraBundle\Altamira\Type\TypeAbstract
{
	protected $allowedRendererOptions = array();

	public function getUseTags()
	{
		return true;
	}
}

?>
