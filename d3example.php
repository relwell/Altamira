<?php 
include(__DIR__ . '/autoload.php');

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

use Altamira\Chart;
use Altamira\ChartIterator;
use Altamira\Series;
use Altamira\ChartRenderer;
use Altamira\Config;
use Altamira\ChartDatum;

\Altamira\Config::setConfigFile( 'altamira-config.ini' );
\Altamira\ChartRenderer::pushRenderer( '\Altamira\ChartRenderer\SVGRenderer' );
\Altamira\ChartRenderer::pushRenderer( '\Altamira\ChartRenderer\DefaultRenderer' );
$library = 'd3';

$charts = array();

$chart = new Chart('chart1', $library);
$chart->addSeries( $chart->createSeries( ChartDatum\TwoDimensionalPointFactory::getFromXValues( array( 1, 2, 3, 4, 5 ) ), 'Series A' ) );
$charts[] = $chart;

$points = array( 'golf clubs' => 1, 'golf shoes' => 5, 'holes' => 18, 'strokes' => 32 );
$chart = new Chart('chart2', $library );
$chart->setType( 'Pie' );
$data = ChartDatum\ScalarValueFactory::getFromAssociativeArray( $points );
$chart->addSeries( $chart->createSeries( $data ) );
$charts[] = $chart;

$nested = array( array( 45, 'cool' ), array( 25, 'cold' ), array( '60', 'temperate' ), array( '75', 'warm' ), array( '90', 'hot' ) );
$chart = new Chart('chart3', $library );
$chart->setType( 'Donut' );
$chart->addSeries( $chart->createSeries( ChartDatum\ScalarValueFactory::getFromNestedArray( $nested ) ) );
$charts[] = $chart;

$chartIterator = new ChartIterator( $charts );

?>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>

<?php $chartIterator->renderLibraries()
                 ->renderCss()
                 ->renderPlugins() ?>
</head>
<body>
<?php  
while ( $chartIterator->valid() ) {
    
    echo $chartIterator->current()->getDiv();
    $chartIterator->next();
    
}
?>

<?php $chartIterator->renderScripts() ?>

</body>
</html>
