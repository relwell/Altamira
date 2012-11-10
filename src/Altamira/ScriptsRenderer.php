<?php

namespace Altamira;

/**
 * Used for rendering inline JavaScript
 */
class ScriptsRenderer extends \ArrayIterator
{
    /**
     * Renders the JS stored herein. Passing true to $withScript wraps it in its own <script> tags.
     * @param bool $withScript
     * @return \Altamira\ScriptsRenderer provides for fluent interface
     */
    public function render( $withScript = false )
    {
        
        if ( $withScript ) {
            echo "<script type='text/javascript'>\n";
        }
        
        echo $this->current();
        
        if ( $withScript ) {
            echo "\n</script>\n";
        }
        
        return $this;
        
    }
    
}