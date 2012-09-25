<?php 

namespace Malwarebytes\Altamira\ChartRenderer;

abstract class RendererAbstract
{
    abstract public static function preRender( \Malwarebytes\Altamira\Chart $chart, array $styleOptions = array() );
    
    abstract public static function postRender( \Malwarebytes\Altamira\Chart $chart, array $styleOptions = array() );
    
    public static function renderStyle( array $styleOptions = array() ) 
    {
        return '';
    }
    
    public static function renderData( \Malwarebytes\Altamira\Chart $chart ) 
    {
        return '';
    }
}
