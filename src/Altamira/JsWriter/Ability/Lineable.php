<?php

namespace Altamira\JsWriter\Ability;

interface Lineable
{
    public function setSeriesLineWidth( $seriesTitle, $value );
    public function setSeriesShowLine( $seriesTitle, $bool );
    
    //@todo these could be in another interface
    public function setSeriesShowMarker( \Altamira\Series $series, $bool );   
    public function setSeriesMarkerStyle( \Altamira\Series $series, $value ); 
    public function setSeriesMarkerSize( \Altamira\Series $series, $value );
}