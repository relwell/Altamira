<?php

namespace Altamira;

class Series
{
	static protected $count = 0;
	protected $data = array();
	protected $tags = array();
	protected $useTags = false;
	protected $useLabels = false;

	protected $title;
	protected $labels= array();
	protected $options = array();
	protected $files = array();
	protected $allowedOptions = array('lineWidth', 'showLine', 'showMarker', 'markerStyle', 'markerSize');

	public function __construct($data, $title = null)
	{
		self::$count++;

		$tagcount = 0;
		foreach($data as $datum) {
			if(is_array($datum) && count($datum) >= 2) {
				$this->useTags = true;
				$this->data[] = array_shift($datum);
				$this->tags[] = array_shift($datum);
			} else {
				$this->data[] = $datum;
				if(count($this->tags) > 0) {
					$this->tags[] = end($this->tags) + 1;
				} else {
					$this->tags[] = 1;
				}
			}
			$tagcount++;
		}

		if(isset($title)) {
			$this->title = $title;
		} else {
			$this->title = 'Series ' . self::$count;
		}
	}

	public function getFiles()
	{
		return $this->files;
	}

	public function setSteps($start, $step)
	{
		$num = $start;
		$this->tags = array();

		foreach($this->data as $item) {
			$this->tags[] = $num;
			$num += $step;
		}
	}

	public function setShadow($use = true, $angle = 45, $offset = 1.25, $depth = 3, $alpha = 0.1)
	{
		$this->options['shadow'] = $use;
		$this->options['shadowAngle'] = $angle;
		$this->options['shadowOffset'] = $offset;
		$this->options['shadowDepth'] = $depth;
		$this->options['shadowAlpha'] = $alpha;

		return $this;
	}

	public function setFill($use = true, $stroke = false, $color = null, $alpha = null)
	{
		$this->options['fill'] = $use;
		$this->options['fillAndStroke'] = $stroke;
		if(isset($color))
			$this->options['fillColor'] = $color;
		if(isset($alpha))
			$this->options['fillAlpha'] = $alpha;

		return $this;
	}

	public function getData($tags = false)
	{
		if($tags || $this->useTags) {
			$labels = $this->labels;
			if($this->useLabels && (count($labels) > 0)) {
				$useLabels = true;
			} else {
				$useLabels = false;
			}

			$data = array();
			$tags = $this->tags;
			foreach($this->data as $datum) {
				if(is_array($datum)) {
					$item = $datum;
					$item[] = array_shift($tags);
				} else {
					$item = array($datum, array_shift($tags));
				}
				if($useLabels) {
					if(count($labels) === 0) {
						$item[] = null;
					} else {
						$item[] = array_shift($labels);
					}
				}

				$data[] = $item;
			}
			return $data;
		} else {
			return $this->data;
		}
	}

	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	public function useLabels($labels = array())
	{
		$this->useTags = true;
		$this->useLabels = true;
		$this->options['pointLabels'] = array('show' => true, 'edgeTolerance' => 3);
		$this->labels = $labels;

		return $this;
	}

	public function setLabelSetting($name, $value)
	{
		if($name === 'location' && in_array($value, array('n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'))) {
			$this->options['pointLabels']['location'] = $value;
		} elseif(in_array($name, array('xpadding', 'ypadding', 'edgeTolerance', 'stackValue'))) {
			$this->options['pointLabels'][$name] = $value;
		}

		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setOption($name, $value)
	{
		if(in_array($name, $this->allowedOptions))
			$this->options[$name] = $value;

		return $this;
	}

	public function getOptions()
	{
		$opts = $this->options;

		if(isset($this->useLabels) && $this->useLabels)
			$this->options['pointLabels']['show'] = true;

		$markerOptions = array();
		if(isset($this->options['markerStyle'])) {
			$markerOptions['style'] = $this->options['markerStyle'];
			unset($opts['markerStyle']);
		}
		if(isset($this->options['markerSize'])) {
			$markerOptions['size'] = $this->options['markerSize'];
			unset($opts['markerSize']);
		}

		if(count($markerOptions) != 0)
			$opts['markerOptions'] = $markerOptions;

		return $opts;
	}
}
