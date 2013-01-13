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
$library = 'd3';

$charts = array();

$chart = new Chart('chart1', $library);

$charts[] = $chart;

$chartIterator = new ChartIterator( $charts );

?>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

<!-- enable this if you want to display the charts on IE -->
<!--<script type="text/javascript" src="js/excanvas.js"></script>-->

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
