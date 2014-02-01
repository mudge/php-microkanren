# PHPµKanren [![Build Status](https://travis-ci.org/mudge/php-microkanren.png?branch=master)](https://travis-ci.org/mudge/php-microkanren)

A PHP implementation of [Jason Hemann and Daniel P. Friedman's
µKanren](http://webyrd.net/scheme-2013/papers/HemannMuKanren2013.pdf).

## Installation

Add the following to your `composer.json`:

```javascript
{
  "require": {
    "mudge/php-microkanren": "0.1.0"
  }
}
```

## Usage

```php
require_once 'vendor/autoload.php';

use MicroKanren\Core as U;

$f = U\callFresh(function ($q) {
  return U\eq($q, 5);
});

echo $f(U\emptyState());
/* => (((#(0) . 5)) . 1) */
```

Inside the `MicroKanren\Core` namespace, there are implementations of the core
µKanren functions as described in the original paper as well as common Lisp
primitives needed for their execution. As the reference implementation is in
[Chez Scheme](http://www.scheme.com), this implementation attempts to mimic
that particularly Lisp as closely as possible.

## Lisp Primitives

* `cons($car, $cdr)`: return a new [cons
  cell](http://en.wikipedia.org/wiki/Cons) with `$car` and `$cdr` (this is the
  most basic primitive for creating lists);
* `car($alist)`: return the first element of `$alist`;
* `cdr($alist)`: return the rest of the `$alist`;
* `nil()`: return the empty list;
* `isPair($obj)`: return true if `$obj` is a valid pair (viz. a cons cell that
  is not the empty list, equivalent to `pair?`);
* `isNull($obj)`: return true is `$obj` is the empty list (equivalent to
  `null?`);
* `assp($proc, $alist)`: [see `assp`](http://www.scheme.com/csug7/objects.html#./objects:s15);
* `alist(...)`: a convenience function for constructing `cons` cells
  equivalent to `list`, e.g.
  `alist(1, 2, 3)` is the same as `cons(1, cons(2, cons(3, nil())))`;
* `isEqv($obj1, $obj2)`: returns true if `$obj1` and `$obj2` are the same
  object (equivalent to [`eqv?`](http://www.scheme.com/csug7/objects.html#./objects:s0));
* `length($alist)`: return the length of `$alist`;
* `map($proc, $alist)`: return a list resulting in applying `$proc` to each
  value in `$alist`;

## µKanren functions

* `variable($c)`: return a new variable containing `$c` (equivalent to `var`);
* `isVariable($x)`: returns true if `$x` is a variable (equivalent to `var?`);
* `isVariableEquals($x1, $x2)`: returns true if `$x1` and `$x2` contain the
  same value (equivalent to `var=?`);
* `mzero()`: return the empty stream (equivalent to `mzero`);
* `walk($u, $s)`;
* `extS($x, $v, $s)`: equivalent to `ext-s`;
* `unit($sC)`;
* `unify($u, $v, $s)`;
* `eq($u, $v)`: equivalent to `≡` from the paper and `==` from the reference
  implementation;
* `callFresh($f)`: equivalent to `call/fresh`;
* `mplus($d1, $d2)`;
* `bind($d, $g)`;
* `disj($g1, $g2)`;
* `conj($g1, $g2)`;
* `emptyState()`: return the empty state (equivalent to `empty-state`);
* `pull($d)`;
* `takeAll($d)`: equivalent to `take-all`;
* `take($n, $d)`;
* `reifyName($n)`: equivalent to `reify-name`;
* `reifyS($v, $s)`: equivalent to `reify-s`;
* `walkStar($v, $s)`: equivalent to `walk*`;
* `reifyFirst($sC)`: equivalent to `reify-1st`.

See [the test
suite](https://github.com/mudge/php-microkanren/blob/master/tests/MicroKanren/CoreTest.php)
for more examples of usage.

## References

* [Justin S. Leitgeb's microKanren in Ruby](https://github.com/jsl/ruby_ukanren);
* [Scott Vokes' Lua port of microKanren](https://github.com/silentbicycle/lua-ukanren);
* [Jason Hemann and Daniel P. Friedman's reference Scheme implementation](https://github.com/jasonhemann/microKanren).

## License

Copyright © 2014 Paul Mucur.

Distributed under the MIT License.
