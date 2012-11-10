<?php

namespace Malwarebytes\AltamiraBundle;

use Altamira\Chart;
use Altamira\ChartIterator;
use Altamira\ChartRenderer;

class ChartFactory {
    protected $library;
    private $logger;


    function __construct($library,$logger) {
        $this->logger=$logger;
        $this->setLibrary($library);
    }


    public function setLibrary($library) {
        $this->logger->debug("Altamira library set to ".$library."!");
        $this->library=$library;

        if ($library == 'flot') {
            ChartRenderer::pushRenderer( '\Altamira\ChartRenderer\DefaultRenderer' );
            ChartRenderer::pushRenderer( '\Altamira\ChartRenderer\TitleRenderer' );
        }
    }


    public function createChart($name) {
        return new Chart($name,$this->library);
    }


    public function getChartIterator(array $charts) {
        //@todo: this supports config file, find a sensible place for it and symfonify it
        $config = new \Altamira\Config();
        return new ChartIterator($charts, $config);
    }
}
?>
