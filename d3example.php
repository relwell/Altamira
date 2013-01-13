<?php 
include(__DIR__ . '/autoload.php');

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

use Altamira\Chart;
use Altamira\ChartIterator;
use Altamira\Series;
use Altamira\ChartRenderer;
use Altamira\Config;
use Altamira\ChartDatum\TwoDimensionalPointFactory;

\Altamira\Config::setConfigFile( 'altamira-config.ini' );
\Altamira\ChartRenderer::pushRenderer( '\Altamira\ChartRenderer\SVGRenderer' );
\Altamira\ChartRenderer::pushRenderer( '\Altamira\ChartRenderer\DefaultRenderer' );
$library = 'd3';

$charts = array();

$chart = new Chart('chart1', $library);
$chart->addSeries( $chart->createSeries( TwoDimensionalPointFactory::getFromXValues( array( 1, 2, 3, 4, 5 ) ), 'Series A' ) )
;

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
