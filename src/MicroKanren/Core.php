<?php

/* Copyright (c) 2014, Paul Mucur (http://mudge.name)
 *
 * Distributed under the MIT License, see LICENSE.
 */

namespace MicroKanren\Core;

require_once __DIR__ . '/Core/Lisp.php';
require_once __DIR__ . '/Core/Variable.php';

/**
 * Returns a new variable containing an index.
 *
 * @param integer $c the variable index
 */
function variable($c)
{
    return new Variable($c);
}

/**
 * Returns true if the given object is a variable.
 *
 * @param mixed $x the object to test
 */
function isVariable($x)
{
    return $x instanceof Variable;
}

/**
 * Returns true if both arguments refer to the same variable.
 *
 * @param Variable $x1 the first variable
 * @param Variable $x2 the second variable
 */
function isVariableEquals($x1, $x2)
{
    return $x1 == $x2;
}

/**
 * Returns the empty stream.
 */
function mzero()
{
    return nil();
}

/**
 * Searches for a variable's value in the substitution. If a non-variable term
 * is walked, return that term.
 *
 * @param mixed $u a variable or non-variable term
 * @param Cons  $s the substitution
 */
function walk($u, $s)
{
    if (isVariable($u)) {
        $pr = assp(
            function ($v) use ($u) {
                return isVariableEquals($u, $v);
            },
            $s
        );

        if ($pr) {
            return walk(cdr($pr), $s);
        } else {
            return $u;
        }
    } else {
        return $u;
    }
}

/**
 * Extends the substitution with a new binding.
 *
 * @param Variable $x a variable
 * @param mixed    $v an arbitrary term
 * @param Cons     $s the substitution.
 */
function extS($x, $v, $s)
{
    return cons(cons($x, $v), $s);
}

/**
 * Lifts the state into a stream whose only element is that state.
 *
 * @param Cons $sC the state
 */
function unit($sC)
{
    return cons($sC, mzero());
}

/**
 * Unifies two terms in a substitution.
 *
 * @param Variable $u a variable
 * @param mixed    $v a term
 * @param Cons     $s the substitution
 */
function unify($u, $v, $s)
{
    $u = walk($u, $s);
    $v = walk($v, $s);

    if (isVariable($u) && isVariable($v) && isVariableEquals($u, $v)) {
        return $s;
    } elseif (isVariable($u)) {
        return extS($u, $v, $s);
    } elseif (isVariable($v)) {
        return extS($v, $u, $s);
    } elseif (isPair($u) && isPair($v)) {
        $s = unify(car($u), car($v), $s);
        if ($s) {
            return unify(cdr($u), cdr($v), $s);
        }
    } elseif ($u === $v) {
        return $s;
    }
}

/**
 * Returns a goal that succeeds if two terms unify in the received state.
 *
 * @param mixed $u a term
 * @param mixed $v a term
 */
function eq($u, $v)
{
    return function ($sC) use ($u, $v) {
        $s = unify($u, $v, car($sC));
        if ($s) {
            return unit(cons($s, cdr($sC)));
        } else {
            return mzero();
        }
    };
}

/**
 * Returns a goal given a unary function whose body is a goal.
 *
 * @param callable $f a unary function whose body is a goal
 */
function callFresh($f)
{
    return function ($sC) use ($f) {
        $c = cdr($sC);
        $x = $f(variable($c));
        return $x(cons(car($sC), $c + 1));
    };
}

/**
 * Merges streams.
 *
 * @param [Cons|callable] $d1 a stream
 * @param Cons            $d2 a stream
 */
function mplus($d1, $d2)
{
    if (isNull($d1)) {
        return $d2;
    } elseif (is_callable($d1)) {
        return function () use ($d1, $d2) {
            return mplus($d2, $d1());
        };
    } else {
        return cons(car($d1), mplus(cdr($d1), $d2));
    }
}

/**
 * Invokes a goal on each element of a stream.
 *
 * @param [Cons|callable] $d a stream
 * @param callable        $g a goal
 */
function bind($d, $g)
{
    if (isNull($d)) {
        return mzero();
    } elseif (is_callable($d)) {
        return function () use ($d, $g) {
            return bind($d(), $g);
        };
    } else {
        return mplus($g(car($d)), bind(cdr($d), $g));
    }
}

/**
 * Returns a goal that succeeds if either of the given goals succeed.
 *
 * @param callable $g1 a goal
 * @param callable $g2 a goal
 */
function disj($g1, $g2)
{
    return function ($sC) use ($g1, $g2) {
        return mplus($g1($sC), $g2($sC));
    };
}

/**
 * Returns a goal that succeeds if both given goals succeed.
 *
 * @param callable $g1 a goal
 * @param callable $g2 a goal
 */
function conj($g1, $g2)
{
    return function ($sC) use ($g1, $g2) {
        return bind($g1($sC), $g2);
    };
}

/**
 * Returns a state with an empty substitution and variable index at 0.
 */
function emptyState()
{
    return cons(mzero(), 0);
}

/**
 * Advances a stream until it matures.
 *
 * @param [Cons|callable] $d a stream
 */
function pull($d)
{
    if (is_callable($d)) {
        return pull($d());
    } else {
        return $d;
    }
}

/**
 * Returns all results from a stream.
 *
 * @param [Cons|callable] $d a stream
 */
function takeAll($d)
{
    $d = pull($d);

    if (isNull($d)) {
        return nil();
    } else {
        return cons(car($d), takeAll(cdr($d)));
    }
}

/**
 * Returns a specific number of results from a stream.
 *
 * @param integer         $n the number of results
 * @param [Cons|callable] $d a stream
 */
function take($n, $d)
{
    if ($n === 0) {
        return nil();
    } else {
        $d = pull($d);

        if (isNull($d)) {
            return nil();
        } else {
            return cons(car($d), take($n - 1, cdr($d)));
        }
    }
}

/**
 * Returns a string name for a given number.
 *
 * @param integer $n the number
 */
function reifyName($n)
{
    return "_.{$n}";
}

/**
 * Reifies a state's substitution with respect to a variable.
 *
 * @param Variable $v a variable
 * @param Cons     $s a substitution
 */
function reifyS($v, $s)
{
    $v = walk($v, $s);
    if (isVariable($v)) {
        $n = reifyName(length($s));
        return cons(cons($v, $n), $s);
    } elseif (isPair($v)) {
        return reifyS(cdr($v), reifyS(car($v), $s));
    } else {
        return $s;
    }
}

function walkStar($v, $s)
{
    $v = walk($v, $s);
    if (isVariable($v)) {
        return $v;
    } elseif (isPair($v)) {
        return cons(walkStar(car($v), $s), walkStar(cdr($v), $s));
    } else {
        return $v;
    }
}

function reifyFirst($sC)
{
    $v = walkStar(variable(0), car($sC));
    return walkStar($v, reifyS($v, nil()));
}

