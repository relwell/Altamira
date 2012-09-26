<?php

namespace Malwarebytes\AltamiraBundle\Altamira;

class ChartFactory {
    protected $library;


    function __construct($library) {
        $this->setLibrary($library);
        echo "I AM CREATED! this should be changed to debug message."; 
    }


    protected function setLibrary($library) {
        $this->library=$library;

    }


    public function createChart($name) {
        return new Chart($name,$this->library);
    }
}
?>
