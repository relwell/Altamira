<?php

namespace Malwarebytes\Altamira\JsWriter\Ability;

interface Labelable
{
    
    public function useSeriesLabels( \Malwarebytes\Altamira\Series $series, array $labels = array() );
    
    public function setSeriesLabelSetting( \Malwarebytes\Altamira\Series $series, $name, $value );
    
}
