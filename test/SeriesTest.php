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
    
    /**
     * @covers \Altamira\Series::__construct
     * @covers \Altamira\Series::getTitle
     */
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
        
        $this   ->mockJqPlotWriter
                ->expects          ( $this->any() )
                ->method           ( 'initializedSeries' );
        
        $series = new Series( $this->data, 'Foo', $this->mockJqPlotWriter );
        
        $this->assertEquals(
                $this->data,
                $series->getData(),
                'A series should return the data that has been passed to it during instantiation.'
        );
        
        $datumJsWriterProperty  = new ReflectionProperty('\Altamira\ChartDatum\ChartDatumAbstract', 'jsWriter');
        $datumSeriesProperty    = new ReflectionProperty('\Altamira\ChartDatum\ChartDatumAbstract', 'series');
        $seriesJsWriterProperty = new ReflectionProperty('\Altamira\Series', 'jsWriter');
        $datumJsWriterProperty->setAccessible( true );
        $datumSeriesProperty->setAccessible( true );
        $seriesJsWriterProperty->setAccessible( true );
        
        foreach( $series->getData() as $datum ) {
            $this->assertEquals(
                    $this->mockJqPlotWriter,
                    $datumJsWriterProperty->getValue( $datum ),
                    'A series should inject its JsWriter into each datum.'
            );
            $this->assertEquals(
                    $series,
                    $datumSeriesProperty->getValue( $datum ),
                    'A series should inject itself into each datum.'
            );
        }
        
        $this->assertEquals(
                'Foo',
                $series->getTitle(),
                'Series title should be set during construct, and accessible via Series::getTitle'
        );
        
        $this->assertEquals(
                $this->mockJqPlotWriter,
                $seriesJsWriterProperty->getValue( $series ),
                'A series should store the JsWriter passed to it in the JsWriter property.'
        );
    }
    
}