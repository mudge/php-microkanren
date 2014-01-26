<?php
class MicroKanren
{
    public static $mzero = array();

    public static function vari($c)
    {
        return array($c);
    }

    public static function isVari($x)
    {
        return is_array($x);
    }

    public static function variEquals($x1, $x2)
    {
        return $x1 === $x2;
    }

    public static function car($x)
    {
        if (count($x) >= 1) {
            return $x[0];
        }
    }

    public static function cdr($x)
    {
        if (count($x) > 1) {
            return $x[1];
        }
    }

    public static function cons($x, $y)
    {
        return array($x, $y);
    }

    public static function assp($proc, $alist)
    {
        if ($alist) {
            $head = self::car($alist);
            if ($head) {
                if ($proc($head)) {
                    return $head;
                } else {
                    return self::assp($proc, self::cdr($alist));
                }
            }
        }
    }

    public static function walk($u, $s)
    {
        if (self::isVari($u)) {
            $pr = self::assp(function ($v) use ($u) {
                return MicroKanren::variEquals($u, $v);
            }, $s);
            if ($pr) {
                return self::walk(self::cdr($pr), $s);
            } else {
                return $u;
            }
        } else {
            return $u;
        }
    }

    public static function extS($x, $v, $s)
    {
        return self::cons(self::cons($x, $v), $s);
    }

    public static function unit($sC)
    {
        return self::cons($sC, self::$mzero);
    }

    public static function isPair($v)
    {
        return is_array($v) && count($v) === 2;
    }

    public static function unify($u, $v, $s)
    {
        $u = self::walk($u, $s);
        $v = self::walk($v, $s);
        if (self::isVari($u) && self::isVari($v) && self::variEquals($u, $v)) {
            return $s;
        } elseif (self::isVari($u)) {
            return self::extS($u, $v, $s);
        } elseif (self::isVari($v)) {
            return self::extS($v, $u, $s);
        } elseif (self::isPair($u) && self::isPair($v)) {
            $s = self::unify(self::car($u), self::car($v), $s);
            if ($s) {
                return self::unify(self::cdr($u), self::cdr($v), $s);
            }
        } elseif (self::variEquals($u, $v)) {
            return $s;
        }
    }

    public static function eq($u, $v)
    {
        return function ($sC) use ($u, $v) {
            $s = MicroKanren::unify($u, $v, MicroKanren::car($sC));
            if ($s) {
                return MicroKanren::unit(MicroKanren::cons($s, MicroKanren::cdr($sC)));
            } else {
                return MicroKanren::$mzero;
            }
        };
    }

    public static function callFresh($f)
    {
        return function ($sC) use ($f) {
            $c = MicroKanren::cdr($sC);
            $x = $f(MicroKanren::vari($c));
            return $x(MicroKanren::cons(MicroKanren::car($sC), $c + 1));
        };
    }

    public static function isNull($v)
    {
        return $v === self::$mzero;
    }

    public static function mplus($d1, $d2)
    {
        if (self::isNull($d1)) {
            return $d2;
        } elseif (is_callable($d1)) {
            return function () use ($d1, $d2) {
                return MicroKanren::mplus($d2, $d1());
            };
        } else {
            return self::cons(self::car($d1), self::mplus(self::cdr($d1), $d2));
        }
    }

    public static function bind($d, $g)
    {
        if (self::isNull($d)) {
            return self::$mzero;
        } elseif (is_callable($d)) {
            return function () use ($d, $g) {
                return MicroKanren::bind($d(), $g);
            };
        } else {
            return self::mplus($g(self::car($d)), self::bind(self::cdr($d), $g));
        }
    }

    public static function disj($g1, $g2)
    {
        return function ($sC) use ($g1, $g2) {
            return MicroKanren::mplus($g1($sC), $g2($sC));
        };
    }

    public static function conj($g1, $g2)
    {
        return function ($sC) use ($g1, $g2) {
            return MicroKanren::bind($g1($sC), $g2);
        };
    }

    public static function emptyState()
    {
        return self::cons(self::$mzero, 0);
    }
}
