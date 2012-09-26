<?php

namespace Malwarebytes\AltamiraBundle\Altamira;

class ScriptsRenderer extends \ArrayIterator
{
    
    public function render( $withScript = false )
    {
       echo getScript($withScript);
       return $this;
    }
    
    public function getScript($withScript = false) {
        $returnString="";
        if ( $withScript ) {
            $returnString.="<script type='text/javascript'>\n";
        }
        
        $returnString.=$this->current();
        
        if ( $withScript ) {
            $returnString.="\n</script>\n";
        }
        
        return $returnString;
    }        
        
}
?>
