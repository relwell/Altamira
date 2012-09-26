<?php

namespace Malwarebytes\AltamiraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Displays an example of all the possible charts available
 *
 */
class ExampleController extends Controller
{
    public function indexAction()
    {
        $chartsFactory=$this->get('charts_factory');
        $charts=array();

        for ($i=1; $i<=8;$i++) {
            $charts[]=$chartsFactory->createChart('chart'.$i);
        }

        print_r($charts);
        return $this->render('MalwarebytesAltamiraBundle:Default:example.html.twig', array('head' => "<title>tmp</title>" , 'charts' => $charts));
    }
}
