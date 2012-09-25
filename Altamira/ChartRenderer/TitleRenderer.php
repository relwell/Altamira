<?php 

namespace Malwarebytes\Altamira\ChartRenderer;

class TitleRenderer extends RendererAbstract
{
    
    public static function preRender( \Malwarebytes\Altamira\Chart $chart, array $styleOptions = array() )
    {
        $tagType = isset($styleOptions['titleTag']) ? $styleOptions['titleTag'] : 'h3';
        $title = $chart->getTitle();
        
        $output = <<<ENDDIV
<div class="altamira-chart-title">
    <{$tagType}>{$title}</{$tagType}>

ENDDIV;
        
        return $output;
    }
    
    public static function postRender( \Malwarebytes\Altamira\Chart $chart, array $styleOptions = array() )
    {
        return '</div>';
    }

}
