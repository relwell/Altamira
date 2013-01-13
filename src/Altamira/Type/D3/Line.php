<?php
/**
 * Class definition for \Altamira\Type\D3\Line
 */
namespace Altamira\Type\D3;
/**
 * This is basically a reminder for now that D3 
 * is weird and needs its own method for rendering lines
 * @author relwell
 *
 */
class Line extends \Altamira\Type\D3\D3TypeAbstract
{
    public function write( $dataName )
    {
        return <<<ENDSCRIPT
var line = d3.svg.line()
    .x(function(d,i) { return x(i); })
    .y(function(d) { return -1 * y(d); });
g.append("svg:path").attr("d", line({$dataName}));
g.append("svg:line")
    .attr("x1", x(0))
    .attr("y1", -1 * y(0))
    .attr("x2", x(w))
    .attr("y2", -1 * y(0));
g.append("svg:line")
    .attr("x1", x(0))
    .attr("y1", -1 * y(0))
    .attr("x2", x(0))
    .attr("y2", -1 * y(d3.max({$dataName})));
g.selectAll(".xLabel")
    .data(x.ticks(5))
    .enter().append("svg:text")
    .attr("class", "xLabel")
    .text(String)
    .attr("x", function(d) { return x(d) })
    .attr("y", 0)
    .attr("text-anchor", "middle")
g.selectAll(".yLabel")
    .data(y.ticks(4))
    .enter().append("svg:text")
    .attr("class", "yLabel")
    .text(String)
    .attr("x", 0)
    .attr("y", function(d) { return -1 * y(d) })
    .attr("text-anchor", "right")
    .attr("dy", 4);
ENDSCRIPT;
        
    }
}