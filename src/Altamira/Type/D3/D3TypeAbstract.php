<?php
/**
 * Class definition for \Altamira\Type\D3\D3TypeAbstract
 */
namespace Altamira\Type\D3;
use Altamira\Type\TypeAbstract;
/**
 * Abstract class that enforces that types are responsible for rendering in D3
 * Why is this, you may ask? Well, it's because D3 is not a chart abstraction library;
 * it is a framework for manipulating data in the document. This means that even 
 * rendering simple lines must be fully specified here in Altamira
 */
abstract class D3TypeAbstract extends TypeAbstract
{
    abstract public function write( $dataName );
}