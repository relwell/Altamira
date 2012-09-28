<?php

namespace Malwarebytes\AltamiraBundle\Altamira;

class FilesRenderer extends \ArrayIterator
{
    
    public function __construct($array, $path = '')
    {
        $this->path = $path;
        parent::__construct($array);
    }
    
    public function render()
    {
        echo getScript();
        return $this;
        
    }
    
    public function getScript() {
        return <<<ENDSCRIPT
<script type="text/javascript" src="{$this->path}{$this->current()}"></script>

ENDSCRIPT;
    }


    public function getScriptPath() {
        return $this->path.$this->current();
    }
}
?>
