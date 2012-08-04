<?php 

namespace Altamira\Type\Flot;

class Pie extends \Altamira\Type\TypeAbstract
{
    
    protected $options = array('series' => array('pie' => array('show' => true)));
    
    public function getOptions()
    {
        return $this->options;
    }
    
}