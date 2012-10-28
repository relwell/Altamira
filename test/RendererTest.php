<?php

class RendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Altamira\ScriptsRenderer::__construct()
     * @covers \Altamira\ScriptsRenderer::render()
     */
    public function testScriptsRenderer()
    {
        $scriptsRenderer = new \Altamira\ScriptsRenderer( array( 'alert("hi");', 'alert("bye");' ) );
        
        $this->assertEquals(
                $scriptsRenderer,
                $scriptsRenderer->render(),
                '\Altamira\ScriptsRenderer::render() should provide a fluent interface.'
        );
        
        $scriptsRenderer->next();
        
        $this->expectOutputString(
                "alert(\"hi\");<script type='text/javascript'>\nalert(\"bye\");\n</script>\n",
                '\Altamira\ScriptsRenderer should implement ArrayIterator, and calling next() on it should provide the next script.'
                . ' Providing true as the first parameter to the render() method should cause the script to be wrapped in script tags.' 
        );
        
        $scriptsRenderer->render( true );
    }
    
    /**
     * @covers \Altamira\FilesRenderer::__construct
     * @covers \Altamira\FilesRenderer::render
     */
    public function testFilesRenderer()
    {
        $path = 'http://www.myjavascripthost.com/';
        $files = array( 'foo.js', 'bar.js' );
        
        $expectedResult = <<<ENDSCRIPT
<script type="text/javascript" src="{$path}foo.js"></script>
<script type="text/javascript" src="{$path}bar.js"></script>

ENDSCRIPT;
        
        $this->expectOutputString(
                $expectedResult,
                '\Altamira\FilesRenderer should implement ArrayIterator, and calling next() on it should provide the next script.'
                . ' Providing a path should prepend that path to each file.' 
        );
        
        $filesRenderer = new \Altamira\FilesRenderer( $files, $path );
        
        $filesRenderer->render()
                      ->next();
        $filesRenderer->render();
    }
    
    /**
     * @covers \Altamira\ChartRenderer\DefaultRenderer::preRender
     * @covers \Altamira\ChartRenderer\DefaultRenderer::postRender
     * @covers \Altamira\ChartRenderer\DefaultRenderer::renderStyle
     */
    public function testDefaultRenderer()
    {
        $mockChart = $this->getMock( '\Altamira\Chart', array( 'getLibrary', 'getName' ) );
        
        $mockChart
            ->expects( $this->any() )
            ->method ( 'getLibrary' )
            ->will   ( $this->returnValue( 'flot' ) )
        ;
        $mockChart
            ->expects( $this->any() )
            ->method ( 'getName' )
            ->will   ( $this->returnValue( 'foo' ) )
        ;
        
        $styleOptions = array( 'float' => 'left', 'border' => '1px solid #cccccc' );
        
        $expectedStyle = 'float: left; border: 1px solid #cccccc; ';
        $styleRendered = \Altamira\ChartRenderer\DefaultRenderer::renderStyle( $styleOptions );
        
        $this->assertEquals(
                $expectedStyle,
                $styleRendered,
                'DefaultRenderer::renderStyle() should transform key-value pairs into CSS inline style declarations'
        );
        
        $preRendering = \Altamira\ChartRenderer\DefaultRenderer::preRender( $mockChart, $styleOptions );
        
        $expectedPreRendering = <<<ENDDIV
<div class="flot" id="foo" style="{$expectedStyle}">
ENDDIV;
        
        $this->assertEquals(
                $expectedPreRendering,
                $preRendering,
                '\Altamira\ChartRenderer\DefaultRenderer::preRender() should create an open div with metadata based on the chart and style passed to it.'
        );

        $this->assertEquals(
                '</div>',
                \Altamira\ChartRenderer\DefaultRenderer::postRender( $mockChart, $styleOptions ),
                '\Altamira\ChartRenderer\DefaultRenderer::postRender() should return a closing div tag.'
        );
    }
    
/**
     * @covers \Altamira\ChartRenderer\TitleRenderer::preRender
     * @covers \Altamira\ChartRenderer\TitleRenderer::postRender
     * @covers \Altamira\ChartRenderer\TitleRenderer::renderStyle
     */
    public function testTitleRenderer()
    {
        $mockChart = $this->getMock( '\Altamira\Chart', array( 'getTitle' ) );
        
        $mockChart
            ->expects( $this->any() )
            ->method ( 'getTitle' )
            ->will   ( $this->returnValue( 'foo' ) )
        ;
        
        $styleOptions = array( 'float' => 'left', 'border' => '1px solid #cccccc' );
        
        $expectedStyle = '';
        $styleRendered = \Altamira\ChartRenderer\TitleRenderer::renderStyle( $styleOptions );
        
        $this->assertEquals(
                $expectedStyle,
                $styleRendered,
                'TitleRenderer::renderStyle() should always return an empty string'
        );
        
        $preRendering = \Altamira\ChartRenderer\TitleRenderer::preRender( $mockChart, $styleOptions );
        
        $expectedPreRendering = <<<ENDDIV
<div class="altamira-chart-title">
    <h3>foo</h3>

ENDDIV;
        
        $this->assertEquals(
                $expectedPreRendering,
                $preRendering,
                '\Altamira\ChartRenderer\TitleRenderer::preRender() should create an open div wrapping h3 tags with the chart title by default.'
        );
        
        $styleOptions['titleTag'] = 'h1';
        
        $expectedPreRendering = <<<ENDDIV
<div class="altamira-chart-title">
    <h1>foo</h1>

ENDDIV;

        $preRendering = \Altamira\ChartRenderer\TitleRenderer::preRender( $mockChart, $styleOptions );
        
        $this->assertEquals(
                $expectedPreRendering,
                $preRendering,
                '\Altamira\ChartRenderer\TitleRenderer::preRender() should support configurable title tags, based on style options.'
        );
        
        $this->assertEquals(
                '</div>',
                \Altamira\ChartRenderer\TitleRenderer::postRender( $mockChart, $styleOptions ),
                '\Altamira\ChartRenderer\TitleRenderer::postRender() should return a closing div tag.'
        );
    }
    
}