<?php

class D3Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->d3 = $this->getMockBuilder( '\Altamira\JsWriter\D3' )->disableOriginalConstructor();
        
        $this->options = new ReflectionProperty( '\Altamira\JsWriter\JsWriterAbstract', 'options' );
        $this->options->setAccessible( true );
    }
    
    /**
     * @covers \Altamira\JsWriter\D3::getType
     */
    public function testGetType()
    {
        $d3 = $this->d3->setMethods( array( 'foo' ) )->getMock();
        $this->assertInstanceOf(
                '\Altamira\Type\D3\Line',
                $d3->getType(),
                'Line should be the default type'
        );
        
        $d3->setType( 'Bar' );
        
        $this->assertInstanceOf(
                '\Altamira\Type\D3\Bar',
                $d3->getType(),
                'If a different type is set it should be accessible'
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\D3::writeData
     */
    public function testWriteData()
    {
        $d3 = $this->d3->setMethods( array( 'getNestedOptVal' ) )->getMock();
        
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )
                          ->disableOriginalConstructor()
                          ->setMethods( array( 'getSeries' ) )
                          ->getMock();
        
        $mockBubble = $this->getMockBuilder( '\Altamira\CharDatum\Bubble' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'toArray' ) )
                           ->getMock();
        
        $mockSeries = $this->getMockBuilder( '\Altamira\Series' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'getData', 'getTitle' ) )
                           ->getMock();
        
        $d3
            ->expects( $this->at( 0 ) )
            ->method ( 'getNestedOptVal' )
            ->with   ( $this->options->getValue( $d3 ), 'seriesStorage', 'series a', 'color' )
            ->will   ( $this->returnValue( '#333' ) )
        ;
        $d3
            ->expects( $this->at( 1 ) )
            ->method ( 'getNestedOptVal' )
            ->with   ( $this->options->getValue( $d3 ), 'seriesStorage', 'series b', 'color' )
            ->will   ( $this->returnValue( '#ccc' ) )
        ;
        $mockChart
            ->expects( $this->at( 0 ) )
            ->method ( 'getSeries' )
            ->will   ( $this->returnValue( array( $mockSeries, $mockSeries ) ) )
        ;
        $mockSeries
            ->expects( $this->at( 0 ) )
            ->method ( 'getTitle' )
            ->will   ( $this->returnValue( 'series a' ) )
        ;
        $mockSeries
            ->expects( $this->at( 1 ) )
            ->method ( 'getData' )
            ->will   ( $this->returnValue( array( $mockBubble ) ) )
        ;
        $mockSeries
            ->expects( $this->at( 2 ) )
            ->method ( 'getTitle' )
            ->will   ( $this->returnValue( 'series b' ) )
        ;
        $mockSeries
            ->expects( $this->at( 3 ) )
            ->method ( 'getData' )
            ->will   ( $this->returnValue( array( $mockBubble ) ) )
        ;
        $aarray = array( 'x' => 5, 'y' => 10, 'radius' => 15, 'label' => 'bubble a' );
        $mockBubble
            ->expects( $this->at( 0 ) )
            ->method ( 'toArray' )
            ->will  ( $this->returnValue( $aarray ) )
        ;
        $barray = array( 'x' => 20, 'y' => 12, 'radius' => 25, 'label' => 'bubble b' );
        $mockBubble
            ->expects( $this->at( 1 ) )
            ->method ( 'toArray' )
            ->will  ( $this->returnValue( $barray ) )
        ;
        
        $chart = new ReflectionProperty( '\Altamira\JsWriter\JsWriterAbstract', 'chart' );
        $chart->setAccessible( true );
        $chart->setValue( $d3, $mockChart );

        $writeData = new ReflectionMethod( '\Altamira\JsWriter\D3', 'writeData' );
        $writeData->setAccessible( true );
        $result = $writeData->invoke( $d3 );
        
        $aarraytransformed = $aarray;
        $aarraytransformed['size'] = $aarray['radius'];
        unset( $aarraytransformed['radius'] );
        
        $resultDecoded = json_decode( $result, true );
        $this->assertEquals(
                2,
                count( $resultDecoded )
        );
        $this->assertEquals(
                $aarraytransformed,
                $resultDecoded[0]['values'][0]
        );
        $this->assertEquals(
                'series a',
                $resultDecoded[0]['key']
        );
        $this->assertEquals(
                '#333',
                $resultDecoded[0]['color']
        );
        
    }
}
