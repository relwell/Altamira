<?php

class ChartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Altamira\ChartIterator::__construct
     * @covers \Altamira\ChartIterator::getLibraries
     * @covers \Altamira\ChartIterator::renderCss
     * @covers \Altamira\ChartIterator::renderLibraries
     * @covers \Altamira\ChartIterator::renderPlugins
     * @covers \Altamira\ChartIterator::renderScripts
     */
    public function testChartIterator()
    {
        $mockConfig = $this->getMock( '\Altamira\Config', array( 'offsetGet' ), array( '../altamira-config.ini' ) );
        
        $junkCharts = array( 'chart1', 'chart2' );
        
        $exception = null;
        try {
            $ci = new \Altamira\ChartIterator( $junkCharts, $mockConfig );
        } catch ( Exception $e ) {
            $exception = $e;
        }
        
        $this->assertInstanceOf(
                'UnexpectedValueException',
                $exception,
                '\Altamira\ChartIterator::__construct should test that the array passed to it contains only instances of \Altamira\Chart'
        );
        
        $mockChart1 = $this->getMock( '\Altamira\Chart', array( 'getFiles', 'getScript', 'getLibrary' ), array( 'Mock Chart 1' ) );
        $mockChart2 = $this->getMock( '\Altamira\Chart', array( 'getFiles', 'getScript', 'getLibrary' ), array( 'Mock Chart 2' ) );
        
        $mockChart1
            ->expects( $this->once() )
            ->method ( 'getFiles' )
            ->will   ( $this->returnValue( array( 'file1a.js', 'file1b.js' ) ) )
        ;
        $mockChart2
            ->expects( $this->once() )
            ->method ( 'getFiles' )
            ->will   ( $this->returnValue( array( 'file2a.js', 'file2b.js' ) ) )
        ;
        $mockChart1
            ->expects( $this->once() )
            ->method ( 'getScript' )
            ->will   ( $this->returnValue( '(function(alert("hey");))();' ) );
        ;
        $mockChart2
            ->expects( $this->once() )
            ->method ( 'getScript' )
            ->will   ( $this->returnValue( '(function(alert("ho");))();' ) );
        ;
        $mockChart1
            ->expects( $this->once() )
            ->method ( 'getLibrary' )
            ->will   ( $this->returnValue( 'flot' ) )
        ;
        $mockChart2
            ->expects( $this->once() )
            ->method ( 'getLibrary' )
            ->will   ( $this->returnValue( \Altamira\JsWriter\JqPlot::LIBRARY ) )
        ;
        $cssPath = 'css/jqplot.css';
        $mockConfig
            ->expects( $this->at( 0 ) )
            ->method ( 'offsetGet' )
            ->with   ( 'js.pluginpath' )
            ->will   ( $this->returnValue( 'js/' ) )
        ;
        $mockConfig
            ->expects( $this->at( 1 ) )
            ->method ( 'offsetGet' )
            ->with   ( 'css.jqplotpath' )
            ->will   ( $this->returnValue( $cssPath ) )
        ;
        $mockConfig
            ->expects( $this->at( 2 ) )
            ->method ( 'offsetGet' )
            ->with   ( 'js.flotpath' )
            ->will   ( $this->returnValue( 'flot.js' ) )
        ;
        $mockConfig
            ->expects( $this->at( 3 ) )
            ->method ( 'offsetGet' )
            ->with   ( 'js.jqplotpath' )
            ->will   ( $this->returnValue( 'jqplot.js' ) )
        ;
        
        $mockCharts = array( $mockChart1, $mockChart2 );
        
        $chartIterator = new \Altamira\ChartIterator( $mockCharts, $mockConfig );
        
        $plugins   = new ReflectionProperty( '\Altamira\ChartIterator', 'plugins' );
        $scripts   = new ReflectionProperty( '\Altamira\ChartIterator', 'scripts' );
        $libraries = new ReflectionProperty( '\Altamira\ChartIterator', 'libraries' );
        
        $plugins->setAccessible( true );
        $scripts->setAccessible( true );
        $libraries->setAccessible( true );
        
        $this->assertInstanceOf(
                '\Altamira\FilesRenderer',
                $plugins->getValue( $chartIterator ),
                '\Altamira\ChartIterator should create an instance of \Altamira\FilesRenderer during construction'
        );
        
        $this->assertInstanceOf(
                '\Altamira\ScriptsRenderer',
                $scripts->getValue( $chartIterator ),
                '\Altamira\ChartIterator should create an instance of \Altamira\ScriptsRenderer during construction'
        );
        
        $this->assertEquals(
                array( \Altamira\JsWriter\Flot::LIBRARY   => true, 
                       \Altamira\JsWriter\JqPlot::LIBRARY => true ),
                $libraries->getValue( $chartIterator ),
                '\Altamira\ChartIterator should unique-keyed hash table of all libraries used by all charts'
        );
        
        $expectedOutputString = "<link rel='stylesheet' type='text/css' href='{$cssPath}'></link>";
        $expectedOutputString .= "<script type='text/javascript' src='flot.js'></script>";
        $expectedOutputString .= "<script type='text/javascript' src='jqplot.js'></script>";
        
        $expectedOutputString .= <<<ENDSTRING
<script type="text/javascript" src="js/file1a.js"></script>
<script type="text/javascript" src="js/file1b.js"></script>
<script type="text/javascript" src="js/file2a.js"></script>
<script type="text/javascript" src="js/file2b.js"></script>
<script type='text/javascript'>
(function(alert("hey");))();(function(alert("ho");))();
</script>

ENDSTRING;
        
        $this->expectOutputString(
                $expectedOutputString,
                '\Altamira\ChartIterator should render libraries, CSS, and plugins'
        );
        
        $chartIterator->renderCss()
                      ->renderLibraries()
                      ->renderPlugins()
                      ->renderScripts();
        
    }
    
}