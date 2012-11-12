<?php

namespace Malwarebytes\AltamiraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Altamira\ChartDatum\TwoDimensionalPointFactory;

/**
 * Displays an example of all the possible charts available
 *
 */
class ExampleController extends Controller
{
    /**
     * just use the default library
     */
    public function indexAction()
    {
        return $this->sampleChartGenerator();
    }


    /**
     * specify the flot library
     */
    public function flotAction() {
        return $this->sampleChartGenerator("flot");
    }

    /**
     * specify the jqplot library
     */
    public function jqplotAction() {
        return $this->sampleChartGenerator("jqPlot");
    }



    private function sampleChartGenerator($library=null) {
        $chartsFactory=$this->get('charts_factory');
        if ( !is_null($library) ) {
            $chartsFactory->setLibrary($library);
        };
        $charts=array();

        for ($i=1; $i<=8;$i++) {
            $charts[]=$chartsFactory->createChart('chart'.$i);
        }
        
        $series1Points = TwoDimensionalPointFactory::getFromYValues( array(2, 8, 5, 3, 8, 9, 7, 8, 4, 2, 1, 6) );

        $series2Points = TwoDimensionalPointFactory::getFromYValues( array(7, 3, 7, 8, 2, 3, 1, 2, 5, 7, 8, 3) );

        $charts[0]->addSeries($charts[0]->createSeries($series1Points, 'Sales'))->
        addSeries($charts[0]->createSeries($series2Points, 'Returns'))->
        setTitle('Basic Line Chart')->
        setAxisOptions('y', 'formatString', '$%d')->
        setAxisOptions('x', 'tickInterval', 1)->
        setAxisOptions('x', 'min', 0)->
        setLegend(array('on'=>true))
        ->setAxisOptions( 'x', 'min', 0)
        ->setAxisOptions( 'x', 'max', 14)
        ->setAxisOptions( 'y', 'min', 0)
        ->setAxisOptions( 'y', 'max', 10);

        $seriesPoints = TwoDimensionalPointFactory::getFromNested( array( array('1/4/1990', 850),
                                                                  array('2/27/1991', 427),
                                                                  array('1/6/1994', 990),
                                                                  array('8/6/1994', 127),
                                                                  array('12/25/1995', 325) ) 
                                                                );
        $series = $charts[1]->createSeries($seriesPoints, 'Measured Readings');
        $series->useLabels(array('a', 'b', 'c', 'd', 'e'))->
            setLabelSetting('location', 'w')->
            setLabelSetting('xpadding', 8)->
            setLabelSetting('ypadding', 8);
        $charts[1]->addSeries($series)->setTitle('Line Chart With Highlights and Labels')->useDates()->useHighlighting();


        $seriesA = $charts[2]->createSeries( TwoDimensionalPointFactory::getFromYValues( array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10) ), 'First' );
        $seriesB = $charts[2]->createSeries( TwoDimensionalPointFactory::getFromYValues( array(1, 10, 2, 9, 3, 8, 4, 7, 5, 6) ), 'Second' );
        $seriesC = $charts[2]->createSeries( TwoDimensionalPointFactory::getFromYValues( array(10, 7, 6, 5, 3, 1, 3, 5, 6, 7) ), 'Third' );

        
        $seriesA->showMarker(false)->
            setLineWidth(8);
        $seriesB->setMarkerStyle('filledSquare')->
            showLine(false);
        $seriesC->setLineWidth(1)->
            setMarkerSize(15)->
            setMarkerStyle('diamond');
        
        $charts[2]->setTitle('Line Chart With Custom Formats And Zoom (drag to zoom, double-click to reset)')->
            addSeries($seriesA)->
            addSeries($seriesB)->
            addSeries($seriesC)->
            useZooming()
            ->setAxisOptions( 'x', 'min', 0)
            ->setAxisOptions( 'x', 'max', 12)
            ->setAxisOptions( 'y', 'min', 0)
            ->setAxisOptions( 'y', 'max', 12);
        
        $charts[3]->setTitle('Horizontal Bar Chart')->
            addSeries($charts[3]->createSeries(TwoDimensionalPointFactory::getFromXValues( array(1, 4, 8, 2, 1, 5) ), 'Runs'))->
            addSeries($charts[3]->createSeries(TwoDimensionalPointFactory::getFromXValues( array(3, 3, 5, 4, 2, 6) ), 'Walks'))->
            setType('Bar')->
            setTypeOption('horizontal', true)->
            setAxisTicks('y', array('1st Inning', '2nd Inning', '3rd Inning', '4th Inning', '5th Inning', '6th Inning'))->
            setLegend(array('on'=>true, 'location'=>'se', 'x'=>5, 'y'=>5));
        
        $manySeriesArray = array(array('Pots', 7), array('Pans', 5), array('Spoons', 2), array('Knives', 5), array('Forks', 12), );
        $nestedFactoryMethod = array( 'Altamira\ChartDatum\TwoDimensionalPointFactory', 'getFromNested' );
        
        $charts[4]->setTitle('Pie Chart')->
            addSeries($charts[4]->createManySeries($manySeriesArray, $nestedFactoryMethod, 'Utensils'))->
            setType('Pie')->
            setLegend();
        
        $chart6Many1 = array(array('Metals', 3), array('Plastics', 5), array('Wood', 2), array('Glass', 7), array('Paper', 9));
        $chart6Many2 = array(array('Metals', 4), array('Plastics', 2), array('Wood', 5), array('Glass', 4), array('Paper', 12));
        
        $charts[5]->setTitle('Donut Chart With Custom Colors And Labels')->
            setSeriesColors(array('#dd3333', '#d465f1', '#aa2211', '#3377aa', '#6699bb', '#9933aa'))->
            setType('Donut')->
            setLegend()->
            setTypeOption('sliceMargin', 3)->
            setTypeOption('showDataLabels', true);
        
            if ( $library == \Altamira\JsWriter\Flot::LIBRARY ) {
            $charts[5]->addSeries($charts[5]->createManySeries( $chart6Many1, $nestedFactoryMethod, 'Internal' ) );
            // Flot doesn't support inner and outer, but you can always use extra js to superimpose
            
            $charts[]=$chartsFactory->createChart('chart'.$i);
            
            $charts[8]
                ->addSeries( $charts[8]->createManySeries($chart6Many2, $nestedFactoryMethod, 'External' ) )
                ->setTitle('Donut Chart With Custom Colors And Labels')
                ->setSeriesColors(array('#dd3333', '#d465f1', '#aa2211', '#3377aa', '#6699bb', '#9933aa'))
                ->setType('Donut')
                ->setLegend()
                ->setTypeOption('sliceMargin', 3)
                ->setTypeOption('showDataLabels', true);
        } else {
            $charts[5]
                ->addSeries($charts[5]->createManySeries($chart6Many1, $nestedFactoryMethod, 'Internal'))
                ->addSeries($charts[5]->createManySeries($chart6Many2, $nestedFactoryMethod, 'External'));
        }
        
        $bubbleFactoryMethod = array( 'Altamira\ChartDatum\BubbleFactory', 'getBubbleDatumFromTupleSet' );
        
        $charts[6]->addSeries($charts[6]->createManySeries(
                array(  array('Screws', 4, 7, 5),
            array('Nails', 5, 3, 6),
            array('Bolts', 4, 5, 7),
            array('Nuts', 3.5, 4, 6),
            array('Washers', 3, 2, 5),
            array('Pliers', 4, 1, 5),
            array('Hammers', 4.5, 6, 6)), $bubbleFactoryMethod, 'Bubble'))->
        setTitle('Bubble Chart')->
        setType('Bubble')->
        setTypeOption('bubbleAlpha', .5)->
        setTypeOption('highlightAlpha', .7)
        ->setAxisOptions( 'x', 'min', 2)
        ->setAxisOptions( 'x', 'max', 6)
        ->setAxisOptions( 'y', 'min', -2)
        ->setAxisOptions( 'y', 'max', 10);
        
            
            
        $array1 = array(1, 4, 8, 2, 1, 5);
        $array2 = array(3, 3, 5, 4, 2, 6);
        
        $num = max(count($array1), count($array2));
        for($i = 0; $i < $num; $i++) {
            $total = $array1[$i] + $array2[$i];
            $array1[$i] = $array1[$i] / $total * 100;
            $array2[$i] = $array2[$i] / $total * 100;
        }
        
        $charts[7]->setTitle('Vertical Stack Chart')->
            addSeries($charts[7]->createSeries(TwoDimensionalPointFactory::getFromYValues( $array1 ), 'Is'))->
            addSeries($charts[7]->createSeries(TwoDimensionalPointFactory::getFromYValues( $array2 ), 'Is Not'))->
            setType('Bar')->
            setLegend(array('on'=>true, 'location'=>'se', 'x'=>5, 'y'=>5))->
            setAxisOptions('y', 'max', 100)->
            setTypeOption('stackSeries', true);
        


        $chartIterator = $chartsFactory->getChartIterator($charts);

        $altamiraJSLibraries=$chartIterator->getLibraries();
        $altamiraCSS=$chartIterator->getCSSPath();
        $altamiraJSScript=$chartIterator->getScripts();
        $altamiraPlugins=$chartIterator->getPlugins();

        while ($chartIterator->valid() ) {
            $altamiraCharts[]=$chartIterator->current()->getDiv();
            $chartIterator->next();
        }


        //print_r($charts);
        return $this->render('MalwarebytesAltamiraBundle:Default:example.html.twig', array('altamiraJSLibraries'=> $altamiraJSLibraries, 'altamiraCSS'=> $altamiraCSS, 'altamiraScripts' =>  $altamiraJSScript, 'altamiraCharts' => $altamiraCharts, 'altamiraJSPlugins' => $altamiraPlugins));
    }
}
