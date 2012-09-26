<?php

namespace Malwarebytes\AltamiraBundle\Altamira\JsWriter\Ability;

interface Lineable
{
    public function setSeriesLineWidth( \Malwarebytes\AltamiraBundle\Altamira\Series $series, $value );
    public function setSeriesShowLine( \Malwarebytes\AltamiraBundle\Altamira\Series $series, $bool );
    
    //@todo these could be in another interface
    public function setSeriesShowMarker( \Malwarebytes\AltamiraBundle\Altamira\Series $series, $bool );   
    public function setSeriesMarkerStyle( \Malwarebytes\AltamiraBundle\Altamira\Series $series, $value ); 
    public function setSeriesMarkerSize( \Malwarebytes\AltamiraBundle\Altamira\Series $series, $value );
}
