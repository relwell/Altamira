<?php

namespace Altamira\JsWriter\Ability;

interface Legendable
{
    public function setLegend($on = true, $location = 'ne', $x = 0, $y = 0);
}