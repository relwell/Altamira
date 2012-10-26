<?

use Altamira\Series;
use Altamira\ChartDatum\TwoDimensionalPointFactory;

class SeriesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $data = array();
        for ( $i = 1; $i <= 10; $i++ )
        {
            $data[] = $i;
        } 
        
        $jsWriterMethods = array(
                'initializeSeries', 
                'setShadow', 
                'setFill', 
                'useSeriesLabels', 
                'setSeriesLabelSetting', 
                'setSeriesOption', 
                'getSeriesOption', 
                'getOptionsForSeries', 
                'setSeriesLineWidth', 
                'setSeriesShowLine', 
                'setSeriesShowMarker', 
                'setSeriesMarkerStyle', 
                'setSeriesMarkerSize', 
                'setType'
        ); 
        
        $mockChart                 = $this->getMock( '\Altamira\Chart' );
        $this->data                = TwoDimensionalPointFactory::getFromXValues( $data );
        $this->mockJqPlotWriter    = $this->getMock( '\Altamira\JsWriter\JqPlot', $jsWriterMethods, array( $mockChart ) );
        $this->mockFlotWriter      = $this->getMock( '\Altamira\JsWriter\JqPlot', $jsWriterMethods, array( $mockChart ) );
        
    }
    
    public function testConstruct()
    {
        $exception = false;
        try {
            $crapSeries = new Series( array(1, 2, 3), 'Foo', $this->mockJqPlotWriter );
        } catch (Exception $e) {
            $exception = $e;
        }
        
        $this->assertInstanceOf(
                '\UnexpectedValueException', 
                $exception,
                'A series should throw an exception if it is passed data that is not formatted into ChartDatumAbstract'
        );
        
        $series = new Series( $this->data, 'Foo', $this->mockJqPlotWriter );
        
    }
    
}