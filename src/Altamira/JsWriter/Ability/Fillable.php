<?php

namespace Altamira\JsWriter\Ability;

interface Fillable
{
    //@todo reduce parameter inflation
    public function setFill($series, $use = true, $stroke = false, $color = null, $alpha = null);
}