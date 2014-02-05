<?php

/* Copyright (c) 2014, Paul Mucur (http://mudge.name)
 *
 * Distributed under the MIT License, see LICENSE.
 */

namespace MicroKanren\Core;

require_once __DIR__ . '/Cons.php';

/**
 * Returns the number of elements in a list.
 *
 * @param Cons $alist a list
 */
function length($alist)
{
    if (isNull($alist)) {
        return 0;
    } elseif (isPair($alist)) {
        return 1 + length(cdr($alist));
    } else {
        throw new \InvalidArgumentException("{$alist} is not a proper list");
    }
}

/**
 * Returns a list resulting in invoking a function with each element of a list.
 *
 * @param callable $proc a function to invoke
 * @param Cons     $alist a list
 */
function map($proc, $alist)
{
    if (isNull($alist)) {
        return nil();
    } elseif (isPair($alist)) {
        return cons($proc(car($alist)), map($proc, cdr($alist)));
    } else {
        throw new \InvalidArgumentException("{$alist} is not a proper list");
    }
}

/**
 * Returns a cons cell with the two given values.
 *
 * @param mixed $car the value of the left-hand "car" field
 * @param mixed $cdr the value of the right hand "cdr" field
 */
function cons($car, $cdr)
{
    return new Cons($car, $cdr);
}

/**
 * Returns nil, the empty list.
 */
function nil()
{
    return Cons::nil();
}

/**
 * Returns true if the given object is a valid pair, viz. a cons cell that is
 * not nil.
 *
 * @param mixed $obj the object to be tested
 */
function isPair($obj)
{
    return $obj instanceof Cons && !$obj->isNil();
}

/**
 * Returns true if the given object is the empty list.
 *
 * @param mixed $obj the object to be tested
 */
function isNull($obj)
{
    return $obj instanceof Cons && $obj->isNil();
}

/**
 * Returns the car value of the given cons cell.
 *
 * e.g. car(cons(1, 2)) is 1
 *
 * @param Cons $alist
 */
function car($alist)
{
    if (!isPair($alist)) {
        throw new \InvalidArgumentException("{$alist} is not a pair");
    }

    return $alist->car;
}

/**
 * Returns the cdr value of the given cons cell.
 *
 * e.g. cdr(cons(1, 2)) is 2
 *
 * @param Cons $alist
 */
function cdr($alist)
{
    if (!isPair($alist)) {
        throw new \InvalidArgumentException("{$alist} is not a pair");
    }

    return $alist->cdr;
}

/**
 * Returns the first element of a list for whose car a procedure returns true
 * or false if no match is found.
 *
 * @param callable $proc  a function that takes one argument and returns a
 *                        boolean
 * @param Cons     $alist an association list of key-value pairs
 */
function assp($proc, $alist)
{
    if (isPair($alist)) {
        $car = car($alist);
        try {
            $x = car($car);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("improperly formed alist {$alist}");
        }

        if ($proc($x)) {
            return $car;
        } else {
            return assp($proc, cdr($alist));
        }
    } else {
        return false;
    }
}

/**
 * Returns a list constructed of nested cons cells from the given arguments.
 * A convenience function equivalent to Scheme's list.
 *
 * e.g. list(1, 2, 3) is the same as cons(1, cons(2, cons(3, nil())))
 *
 * @param mixed $elements the elements of the list
 */
function alist()
{
    $elements = func_get_args();
    $length = func_num_args();
    $list = nil();

    for ($i = $length - 1; $i >= 0; $i -= 1) {
        $list = cons($elements[$i], $list);
    }

    return $list;
}
