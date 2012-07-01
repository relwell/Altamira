<?php

namespace Altamira;

class FilesRenderer extends \ArrayIterator
{
    
    public function __construct($array, $path = '')
    {
        $this->path = $path;
        parent::__construct($array);
    }
    
    public function render()
    {
        //@todo refactor everything to not require js/plugins
        echo <<<ENDSCRIPT
<script type="text/javascript" src="{$this->path}{$this->current()}"></script>

ENDSCRIPT;
        
        return $this;
        
    }
    
}