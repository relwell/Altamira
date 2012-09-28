<?php

namespace Malwarebytes\AltamiraBundle\Altamira\JsWriter\Ability;

interface Labelable
{
    
    public function useSeriesLabels( \Malwarebytes\AltamiraBundle\Altamira\Series $series, array $labels = array() );
    
    public function setSeriesLabelSetting( \Malwarebytes\AltamiraBundle\Altamira\Series $series, $name, $value );
    
}
