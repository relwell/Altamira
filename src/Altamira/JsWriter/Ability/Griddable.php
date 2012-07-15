<?php 

namespace Altamira\JsWriter\Ability;

interface Griddable
{
    public function setGrid($on = true, $color = null, $background = null);
}