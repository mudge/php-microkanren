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

function appendo($l, $s, $out)
{
    return disj(
        conj(eq(nil(), $l), eq($s, $out)),
        callFresh(function ($a) use ($l, $s, $out) {
            return callFresh(function ($d) use ($a, $l, $s, $out) {
                return conj(
                    eq(cons($a, $d), $l),
                    callFresh(function ($res) use ($a, $s, $d, $out) {
                        return conj(
                            eq(cons($a, $res), $out),
                            function ($sC) use ($d, $s, $res) {
                                return function () use ($d, $s, $res, $sC) {
                                    $f = appendo($d, $s, $res);
                                    return $f($sC);
                                };
                            }
                        );
                    })
                );
            });
        })
    );
}

function groundAppendo()
{
    return appendo(alist('a'), alist('b'), alist('a', 'b'));
}

function appendo2($l, $s, $out)
{
    return disj(
        conj(eq(nil(), $l), eq($s, $out)),
        callFresh(function ($a) use ($l, $s, $out) {
            return callFresh(function ($d) use ($a, $l, $s, $out) {
                return conj(
                    eq(cons($a, $d), $l),
                    callFresh(function ($res) use ($a, $d, $s, $out) {
                        return conj(
                            function ($sC) use ($d, $s, $res) {
                                return function () use ($d, $s, $res, $sC) {
                                    $f = appendo2($d, $s, $res);
                                    return $f($sC);
                                };
                            },
                            eq(cons($a, $res), $out)
                        );
                    })
                );
            });
        })
    );
}

function callAppendo()
{
    return callFresh(function ($q) {
        return callFresh(function ($l) use ($q) {
            return callFresh(function ($s) use ($q, $l) {
                return callFresh(function ($out) use ($q, $l, $s) {
                    return conj(
                        appendo($l, $s, $out),
                        eq(alist($l, $s, $out), $q)
                    );
                });
            });
        });
    });
}

function callAppendo2()
{
    return callFresh(function ($q) {
        return callFresh(function ($l) use ($q) {
            return callFresh(function ($s) use ($q, $l) {
                return callFresh(function ($out) use ($q, $l, $s) {
                    return conj(
                        appendo2($l, $s, $out),
                        eq(alist($l, $s, $out), $q)
                    );
                });
            });
        });
    });
}

function groundAppendo2()
{
    return appendo2(alist('a'), alist('b'), alist('a', 'b'));
}

function relo($x)
{
    return callFresh(function ($x1) use ($x) {
        return callFresh(function ($x2) use ($x, $x1) {
            return conj(
                eq($x, cons($x1, $x2)),
                disj(
                    eq($x1, $x2),
                    function ($sC) use ($x) {
                        return function () use ($x, $sC) {
                            $r = relo($x);
                            return $r($sC);
                        };
                    }
                )
            );
        });
    });
}

function manyNonAns()
{
    return callFresh(function ($x) {
        return disj(
            relo(cons(5, 6)),
            eq($x, 3)
        );
    });
}
