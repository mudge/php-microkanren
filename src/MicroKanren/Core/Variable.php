<?php

/* Copyright (c) 2014, Paul Mucur (http://mudge.name)
 *
 * Distributed under the MIT License, see LICENSE.
 */

namespace MicroKanren\Core;

/**
 * Variable.
 *
 * A single logic variable as typically created by callFresh.
 */
class Variable
{

    /**
     * @param mixed $c the value of the variable
     */
    public function __construct($c)
    {
        $this->c = $c;
    }

    /**
     * Returns a string representing this variable.
     *
     * To be consistent with the formatting of examples in the original
     * paper, variables are surrounded by parentheses and preceded by a hash.
     *
     * e.g. #(0)
     */
    public function __toString()
    {
        return "#({$this->c})";
    }
}
