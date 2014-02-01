<?php
namespace MicroKanren;

class Cons
{
    public function __construct($car = null, $cdr = null)
    {
        $this->car = $car;
        $this->cdr = $cdr;
    }

    public function __toString()
    {
        if ($this->isNil()) {
            return '()';
        } elseif ($this->cdr instanceof self && $this->cdr->isNil()) {
            return "({$this->car})";
        } else {
            return "({$this->car} . {$this->cdr})";
        }
    }

    public function isNil()
    {
        return is_null($this->car) && is_null($this->cdr);
    }

    public static function nil()
    {
        return new Cons();
    }
}
