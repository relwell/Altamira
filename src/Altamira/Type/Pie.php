<?php

namespace Altamira\Type;

class Pie extends TypeAbstract
{
	protected $pluginFiles = array('jqplot.pieRenderer.min.js');
	protected $renderer = '$.jqplot.PieRenderer';

	protected $allowedRendererOptions = array('sliceMargin', 'showDataLabels', 'dataLabelPositionFactor', 'dataLabelNudge', 'dataLabels');

	public function getUseTags()
	{
		return true;
	}
}

?>