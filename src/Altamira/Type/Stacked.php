<?php

namespace Altamira\Type;

class Stacked extends TypeAbstract
{

	protected $pluginFiles = array();

	public function getOptions()
	{
		$opts = array();
		$opts['stackSeries'] = true;
		$opts['seriesDefaults'] = array('fill' => true, 'showMarker' => false, 'shadow' => false);

		return $opts;
	}
}

?>