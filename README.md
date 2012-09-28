About
===================


A PHP abstraction library for JavaScript charting built as a Symfony 2 service bundle.


Getting Started
===================

You need a working Symfony2 framework installed and setup. From your main symfony2 directory, run:



``` bash
$ composer require friendsofsymfony/rest-bundle
```


Enable the bundle:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
     $bundlles = array (
         // ...
         new Malwarebytes\AltamiraBundle\MalwarebytesAltamiraBundle(),
     );
}
```

If you would like to see example code, enable the example controller:

``` yml
# app/config/routing.yml

altamira_example:
    resource: "@MalwarebytesAltamiraBundle/Resources/config/routing.yml"
    prefix:   /chart_demo
