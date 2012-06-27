<?php

namespace Altamira\Type;

class Donut extends Pie
{
	protected $pluginFiles = array('jqplot.donutRenderer.min.js');
	protected $renderer = '$.jqplot.DonutRenderer';
}

?>