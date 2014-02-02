<?php

/* Copyright (c) 2014, Paul Mucur (http://mudge.name)
 *
 * Distributed under the MIT License, see LICENSE.
 */

namespace MicroKanren\Core;

/**
 * Cons cell.
 *
 * An implementation of a Lisp cons cell, used throughout the rest of the
 * code as a fundamental data structure.
 *
 * c.f. http://en.wikipedia.org/wiki/Cons
 */
class Cons
{

    /**
     * @param mixed $car the value of the left-hand "car" field
     * @param mixed $cdr the value of the right-hand "cdr" field
     */
    public function __construct($car = null, $cdr = null)
    {
        $this->car = $car;
        $this->cdr = $cdr;
    }

    /**
     * Returns a string representing this cons cell.
     *
     * The formatting of this string uses parentheses around the cell with a
     * dot to separate the car and cdr field.
     *
     * e.g. (1 . 2)
     */
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

    /**
     * Returns true if this cons cell is nil.
     */
    public function isNil()
    {
        return is_null($this->car) && is_null($this->cdr);
    }

    /**
     * Returns nil, the empty list.
     */
    public static function nil()
    {
        return new Cons();
    }
}
