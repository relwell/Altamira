<?php

namespace Malwarebytes\Altamira\Type\JqPlot;

class Stacked extends \Malwarebytes\Altamira\Type\TypeAbstract
{
    
    protected $options = array(   'stackSeries'     => true,
                                  'seriesDefaults'  => array( 'fill'       => true, 
                                                              'showMarker' => false, 
                                                              'shadow'     => false
                                                             )
                                       );

}

?>
