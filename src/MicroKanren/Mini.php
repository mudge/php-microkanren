<?php

/* Copyright (c) 2014, Paul Mucur (http://mudge.name)
 *
 * Distributed under the MIT License, see LICENSE.
 */

namespace MicroKanren\Core;

require_once __DIR__ . '/Core.php';

/**
 * Takes a goal and returns a goal as a function from a state to a stream.
 */
function zzz($g)
{
    return function ($aC) use ($g) {
        return function () use ($g, $aC) {
            return $g($aC);
        };
    };
}

/**
 * conj one or more goals without nesting.
 */
function conjPlus($g)
{
    $gs = func_get_args();

    if (func_num_args() === 1) {
        return zzz($g);
    } else {
        return conj(
            zzz($g),
            call_user_func_array('MicroKanren\Core\conjPlus', array_slice($gs, 1))
        );
    }
}

/**
 * disj one or more goals without nesting.
 */
function disjPlus($g)
{
    $gs = func_get_args();

    if (func_num_args() === 1) {
        return zzz($g);
    } else {
        return disj(
            zzz($g),
            call_user_func_array('MicroKanren\Core\disjPlus', array_slice($gs, 1))
        );
    }
}

/**
 * Introduces any number of fresh variables into scope.
 */
function fresh($f)
{
    $reflection = new \ReflectionFunction($f);
    $argCount = $reflection->getNumberOfParameters();

    if ($argCount === 0) {
        return $f();
    } else {
        return callFresh(function ($x) use ($f, $argCount) {
            return collectArgs($f, $argCount, array(), $x);
        });
    }
}

function collectArgs($f, $argCount, $args, $arg)
{
    $args[] = $arg;

    if (count($args) === $argCount) {
        return call_user_func_array($f, $args);
    } else {
        return callFresh(function ($x) use ($f, $argCount, $args) {
            return collectArgs($f, $argCount, $args, $x);
        });
    }
}

function run($n, $g)
{
    return map(
        'MicroKanren\Core\reifyFirst',
        take($n, callGoal(fresh($g)))
    );
}

function runStar($g)
{
    return map(
        'MicroKanren\Core\reifyFirst',
        takeAll(callGoal(fresh($g)))
    );
}

function callGoal($g)
{
    return $g(emptyState());
}

