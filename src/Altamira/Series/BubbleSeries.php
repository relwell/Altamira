<?php

namespace Altamira\Series;
use Altamira\Series;

class BubbleSeries extends Series
{
	public function __construct($data, $title = null)
	{
		self::$count++;

		foreach($data as $datum) {
			if(is_array($datum) && count($datum) == 4) {
				$this->useTags = true;
				$this->data[] = array_slice($datum, 0, 3);
				$this->tags[] = array_pop($datum);
			}
		}

		if(isset($title)) {
			$this->title = $title;
		} else {
			$this->title = 'Series ' . self::$count;
		}
	}

}

?>