<?php
namespace MicroKanren\Core;

require_once __DIR__ . '/Core/Cons.php';
require_once __DIR__ . '/Core/Variable.php';

function cons($car, $cdr)
{
    return new Cons($car, $cdr);
}

function nil()
{
    return Cons::nil();
}

function isPair($obj)
{
    return $obj instanceof Cons && !$obj->isNil();
}

function isNull($obj)
{
    return $obj instanceof Cons && $obj->isNil();
}

function car($alist)
{
    if (!isPair($alist)) {
        throw new \InvalidArgumentException('() is not a pair');
    }

    return $alist->car;
}

function cdr($alist)
{
    if (!isPair($alist)) {
        throw new \InvalidArgumentException('() is not a pair');
    }

    return $alist->cdr;
}

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

function isEqv($obj1, $obj2)
{
    return $obj1 === $obj2;
}

function variable($c)
{
    return new Variable($c);
}

function isVariable($x)
{
    return $x instanceof Variable;
}

function isVariableEquals($x1, $x2)
{
    return $x1 == $x2;
}

function mzero()
{
    return nil();
}

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

function extS($x, $v, $s)
{
    return cons(cons($x, $v), $s);
}

function unit($sC)
{
    return cons($sC, mzero());
}

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
    } elseif (isVariableEquals($u, $v)) {
        return $s;
    }
}

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

function callFresh($f)
{
    return function ($sC) use ($f) {
        $c = cdr($sC);
        $x = $f(variable($c));
        return $x(cons(car($sC), $c + 1));
    };
}

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

function disj($g1, $g2)
{
    return function ($sC) use ($g1, $g2) {
        return mplus($g1($sC), $g2($sC));
    };
}

function conj($g1, $g2)
{
    return function ($sC) use ($g1, $g2) {
        return bind($g1($sC), $g2);
    };
}

function emptyState()
{
    return cons(mzero(), 0);
}

function pull($d)
{
    if (is_callable($d)) {
        return pull($d());
    } else {
        return $d;
    }
}

function takeAll($d)
{
    $d = pull($d);

    if (isNull($d)) {
        return nil();
    } else {
        return cons(car($d), takeAll(cdr($d)));
    }
}

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

function reifyName($n)
{
    return "_.{$n}";
}

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
