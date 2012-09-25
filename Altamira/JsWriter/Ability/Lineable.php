<?php

namespace Malwarebytes\Altamira\JsWriter\Ability;

interface Lineable
{
    public function setSeriesLineWidth( \Malwarebytes\Altamira\Series $series, $value );
    public function setSeriesShowLine( \Malwarebytes\Altamira\Series $series, $bool );
    
    //@todo these could be in another interface
    public function setSeriesShowMarker( \Malwarebytes\Altamira\Series $series, $bool );   
    public function setSeriesMarkerStyle( \Malwarebytes\Altamira\Series $series, $value ); 
    public function setSeriesMarkerSize( \Malwarebytes\Altamira\Series $series, $value );
}
