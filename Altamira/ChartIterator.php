<?php 

namespace Malwarebytes\AltamiraBundle\Altamira;

class ChartIterator extends \ArrayIterator
{
    
    protected $plugins;
    protected $scripts;
    
    public function __construct( $chartsArray )
    {
        //enforce that this is an array of charts
        $plugins = array();
        $scripts = array();
        
        foreach ($chartsArray as $item) {
            if (! $item instanceOf Chart ) {
                throw new \Exception("ChartIterator only supports an array of Chart instances.");
            }
            
            // time saver -- if it's a chart, we can use this loop to add files, too
            $plugins = array_merge($plugins, $item->getFiles());
            $scripts[] = $item->getScript();
            $this->libraries[$item->getLibrary()] = true;
        }

        // yo dawg...
        $this->plugins = new FilesRenderer($plugins, 'bundles/malwarebytesaltamira/js/plugins/');
        $this->scripts = new ScriptsRenderer($scripts);
        
        
        parent::__construct($chartsArray);        
    }
    
    
    /**
     * The following render methods are helpers that allow us to group JS easier.
     * We don't handle chart HTML this way since placement and context is a front-end concern.  
     */
    
    
    public function renderPlugins()
    {
        
        while ( $this->plugins->valid() ) {

            $this->plugins->render()
                          ->next();
            
        }
        
        return $this;
        
    }
  
    public function getPlugins() {
        $plugin=array();
        while ($this->plugins->valid() ) {
            $plugin[]=$this->plugins->getScriptPath();
            $this->plugins->next();
        }
        return $plugin;
    }
    
    public function renderScripts()
    {
        echo "<script type='text/javascript'>\n";
        while ( $this->scripts->valid() ) {
            
            $this->scripts->render()
                          ->next();
            
        }
        echo "\n</script>\n";
        
        return $this;
        
    }
    
    /* TODO: This code is excessive. Might as well just look at the last value. Methinks this is broken. -jchan */
    public function renderLibraries()
    {
        echo "<script type='text/javascript src='".getLibraries()."'></script>\n";
        return $this;
    }

    /**
     * Instead of printing, return this value
     */
    public function getLibraries() {
        foreach ($this->libraries as $library=>$junk) {
            switch($library) {
                case 'flot':
                    $libraryPath = 'bundles/malwarebytesaltamira/js/jquery.flot.js';
                    break;
                case 'jqPlot':
                default:
                    $libraryPath = 'bundles/malwarebytesaltamira/js/jquery.jqplot.js';
            }
        }
        return $libraryPath;
    }
            
               
    public function renderCss() {
        echo getCss();  
        return $this;
    }
    
    public function getCss()
    {
        foreach ($this->libraries as $library=>$junk) {
            switch($library) {
                case 'flot':
                    break;
                case 'jqPlot':
                default:
                    $cssPath = 'bundles/malwarebytesaltamira/css/jqplot.css';
            }
        
        }
        
        if (isset($cssPath)) {
            return "<link rel='stylesheet' type='text/css' href='{$cssPath}'></link>";
        }
        
        
    }


    public function getCSSPath() {
        foreach ($this->libraries as $library=>$junk) {
            switch($library) {
                case 'flot':
                    break;
                case 'jqPlot':
                default:
                    $cssPath = 'bundles/malwarebytesaltamira/css/jqplot.css';
            }
        
        }
        
        if (isset($cssPath)) {
            return ($cssPath);
        }
    }
        


    public function getJSLibraries() {
        $libraries= array( $this->getLibraries() );
        $libraries=array_merge(  $libraries ,$this->getPlugins());
        return $libraries;
    }
 
}
