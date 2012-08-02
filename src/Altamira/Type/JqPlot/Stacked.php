<?php

namespace Altamira\Type\JqPlot;

class Stacked extends \Altamira\Type\TypeAbstract
{
    
	public function getOptions()
	{
		$opts = array();
		$opts['stackSeries'] = true;
		$opts['seriesDefaults'] = array('fill' => true, 'showMarker' => false, 'shadow' => false);

		return $opts;
	}
}

?>