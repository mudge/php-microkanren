<?php
namespace MicroKanren;

class Variable
{
    public function __construct($c)
    {
        $this->c = $c;
    }

    public function __toString()
    {
        return "#({$this->c})";
    }
}
