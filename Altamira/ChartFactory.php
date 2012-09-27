<?php

namespace Malwarebytes\AltamiraBundle\Altamira;

class ChartFactory {
    protected $library;


    function __construct($library) {
        $this->setLibrary($library);
        echo "I AM CREATED with library ".$library."! this should be changed to debug message."; 

        if ($library == 'flot') {
            ChartRenderer::pushRenderer( 'Malwarebytes\AltamiraBundle\Altamira\ChartRenderer\DefaultRenderer' );
            ChartRenderer::pushRenderer( 'Malwarebytes\AltamiraBundle\Altamira\ChartRenderer\TitleRenderer' );
        }
    }


    protected function setLibrary($library) {
        $this->library=$library;

    }


    public function createChart($name) {
        return new Chart($name,$this->library);
    }


    public function getChartIterator(array $charts) {
        return new ChartIterator($charts);
    }
}
?>
