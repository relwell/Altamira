<?php

namespace Altamira\Type;

class Bubble extends TypeAbstract
{

	protected $pluginFiles = array('jqplot.bubbleRenderer.min.js');
	protected $renderer = '$.jqplot.BubbleRenderer';

	protected $allowedRendererOptions = array(	'autoscalePointsFactor',
							'autoscaleMultiplier',
							'autoscaleBubbles',
							'highlightMouseDown',
							'varyBubbleColors',
							'bubbleAlpha',
							'highlightAlpha');

}

?>