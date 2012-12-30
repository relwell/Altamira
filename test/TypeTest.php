<?php

class TypeTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }
    
    protected function getBar( $methods = array() )
    {
        return $this->getMockBuilder( '\Altamira\Type\JqPlot\Bar' )
                    ->disableOriginalConstructor()
                    ->setMethods( $methods )
                    ->getMock();
    }
    
    /**
     * @covers \Altamira\Type\TypeAbstract::configure
     * @covers \Altamira\Type\TypeAbstract::__construct
     * @covers \Altamira\Type\TypeAbstract::getFiles
     */
    public function testAbstractConfigure()
    {
        $mockJqPlot = $this->getMockBuilder( '\Altamira\JsWriter\JqPlot' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'getLibrary' ) )
                           ->getMock();
        
        $mockJqPlot
            ->expects    ( $this->any() )
            ->method     ( 'getLibrary' )
            ->will       ( $this->returnValue( \Altamira\JsWriter\JqPlot::LIBRARY ) )
        ;
        
        $bar = $this->getMock( '\Altamira\Type\JqPlot\Bar', array( 'foo' ), array( $mockJqPlot ) );

        $config = new ReflectionMethod( '\Altamira\Type\TypeAbstract', 'configure' );
        $config->setAccessible( true );
        $config->invoke( $bar, $mockJqPlot );
        
        $pluginFiles = new ReflectionProperty( '\Altamira\Type\TypeAbstract', 'pluginFiles' );
        $pluginFiles->setAccessible( true );
        
        $this->assertEquals(
                $pluginFiles->getValue( $bar ),
                $bar->getFiles()
        );
    }
    
}