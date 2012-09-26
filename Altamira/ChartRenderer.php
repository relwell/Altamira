<?php 

namespace Malwarebytes\AltamiraBundle\Altamira;
use Malwarebytes\AltamiraBundle\Altamira\ChartRenderer\RendererInterface;

use Malwarebytes\AltamiraBundle\Altamira\ChartRenderer;

class ChartRenderer
{
    protected static $rendererChain = array();
    
    public static function render( Chart $chart, array $styleOptions = array() )
    {
        if ( empty(self::$rendererChain) ) {
            self::pushRenderer( '\Malwarebytes\AltamiraBundle\Altamira\ChartRenderer\DefaultRenderer' );
        } 
            
        $outputString = '';

        for ( $i = count(self::$rendererChain)-1; $i >= 0; $i-- ) {
            $renderer = self::$rendererChain[$i];
            $outputString .= call_user_func_array(array($renderer, 'preRender'), array( $chart, $styleOptions ));
        }
        
        for ( $i = 0; $i < count(self::$rendererChain); $i++ ) {
            $renderer = self::$rendererChain[$i];
            $outputString .= call_user_func_array(array($renderer, 'postRender'), array( $chart, $styleOptions ));
        }
        
        return $outputString;
    }
    
    public static function pushRenderer( $renderer )
    {
        if (! (($renderer instanceOf ChartRenderer\RendererAbstract ) || is_subclass_of($renderer, 'Malwarebytes\AltamiraBundle\Altamira\ChartRenderer\RendererAbstract') )) {
            throw new \UnexpectedValueException( "Renderer must be instance of or string name of a class implementing RendererInterface" );
        }
        
        array_push( self::$rendererChain, $renderer );
        
        return self;
    }
    
    public static function unshiftRenderer( $renderer )
    {
        if (! $renderer instanceOf ChartRenderer\RendererAbstract ) {
            throw new \UnexpectedValueException( "Renderer must be instance of or string name of a class implementing RendererInterface" );
        }
        
        array_unshift( self::$rendererChain, $renderer );
        
        return self;
    }
    
    public function reset()
    {
        self::$rendererChain = array();
        
        return self;
    }
    
}
