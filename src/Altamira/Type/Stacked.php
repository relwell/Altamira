<?php

namespace Altamira\Type;

class Stacked extends TypeAbstract
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