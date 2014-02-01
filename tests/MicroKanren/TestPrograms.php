<?php
namespace MicroKanren;
require_once __DIR__ . '/../../src/MicroKanren/Core.php';

function aAndB()
{
    return conj(
        callFresh(function ($a) {
            return eq($a, 7);
        }),
        callFresh(function ($b) {
            return disj(
                eq($b, 5),
                eq($b, 6)
            );
        })
    );
}

function fives($x)
{
    return disj(
        eq($x, 5),
        function ($aC) use ($x) {
            return function () use ($x, $aC) {
                $f = fives($x);
                return $f($aC);
            };
        }
    );
}
