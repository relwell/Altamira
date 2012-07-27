<?php

namespace Altamira\JsWriter\Ability;

interface Labelable
{
    
    public function useSeriesLabels( \Altamira\Series $series, array $options = array() );
    
    public function setSeriesLabelSetting( \Altamira\Series $series, $name, $value );
    
}