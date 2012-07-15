<?php 

namespace Altamira\JsWriter\Ability;

interface Shadowable
{
    //@todo propagation of parameters is bad -- split this out or pass an array of opts
    public function setShadow($series, $use = true, $angle = 45, $offset = 1.25, $depth = 3, $alpha = 0.1);
}