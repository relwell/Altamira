About
===================


A PHP abstraction library for JavaScript charting built as a Symfony 2 service bundle.


The following libraries are currently bundled with this Symfony bundle, feel free to update
the libraries yourself - they reside in Resources/public/js.

* Jquery 1.6.4
* Flot 0.7
* jqPlot 1.0.0 revision 1095


Getting Started
===================

You need a working Symfony2 framework installed and setup. From your main symfony2 directory, run:



``` bash
$ composer require malwarebytes/altamirabundle
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

Lastly, install the assets:

```bash
$ app/console assets:install
```

If you would like to see example code, enable the example controller:

``` yml
# app/config/routing.yml

altamira_example:
    resource: "@MalwarebytesAltamiraBundle/Resources/config/routing.yml"
    prefix:   /chart_demo/example
```

Developing
===================

Refer to the sample controller on examples on how to use the code.
