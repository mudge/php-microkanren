# PHPµKanren [![Build Status](https://travis-ci.org/mudge/php-microkanren.png?branch=master)](https://travis-ci.org/mudge/php-microkanren)

A PHP implementation of [Jason Hemann and Daniel P. Friedman's
µKanren](http://webyrd.net/scheme-2013/papers/HemannMuKanren2013.pdf).

## Installation

Add the following to your `composer.json`:

```javascript
{
    "require": {
        "mudge/php-microkanren": "v0.1.0"
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
that particular Lisp as closely as possible.

## Lisp Primitives

### `cons($car, $cdr)`

```php
$c = cons(1, cons(2, cons(3, nil())));
```

Return a new [cons cell](http://en.wikipedia.org/wiki/Cons) with `$car` and
`$cdr` (this is the most basic primitive for creating lists).

### `car($alist)`

```php
$c = cons(1, cons(2, nil()));
car($c);
/* => 1 */
```

Return the first element of `$alist`.

### `cdr($alist)`

```php
$c = cons(1, cons(2, nil()));
cdr($c);
/* => cons(2, nil()) */
```

Return the rest of the `$alist`.

### `nil()`

```php
$n = nil();
$n === nil();
/* => true */
```

Return the empty list. Note that all instances of nil are identical.

### `isPair($obj)`

```php
isPair(cons(1, nil())); /* => true  */
isPair(4);              /* => false */
isPair(nil());          /* => false */
```

Return true if `$obj` is a valid pair (viz. a cons cell that is not the empty
list, equivalent to Petite Scheme's `pair?`).

### `isNull($obj)`

```php
isNull(nil());          /* => true  */
isNull(cons(1, nil())); /* => false */
```

Return true is `$obj` is the empty list (equivalent to Petite Scheme's
`null?`).

### `assp($proc, $alist)`

```php
$list = alist(cons(1, 'a'), cons(2, 'b'));
$isEven = function ($x) { return $x % 2 === 0; };

assp($isEven, $list);
/* => cons(2, 'b') */
```

"Return the first element of `$alist` for whose car `$proc` returns true, or
false." &mdash; [Petite Scheme's `assp`](http://www.scheme.com/csug7/objects.html#./objects:s15)

### `alist(...)`

```php
alist(1, 2, 3);
/* => cons(1, cons(2, cons(3, nil()))) */
```

A convenience function for constructing `cons` cells, equivalent to Petite
Scheme's `list`.

### `length($alist)`

```php
length(alist(1, 2, 3));
/* => 3 */
```

Return the length of `$alist`.

### `map($proc, $alist)`

```php
$list = alist(1, 2, 3);
map(function ($x) { return $x + 1; }, $list);
/* => alist(2, 3, 4) */
```

Return a list resulting in applying `$proc` to each value in `$alist`.

## µKanren functions

### `variable($c)`

Return a new variable containing an index `$c` (equivalent to `var`).

### `isVariable($x)`

Returns true if `$x` is a variable (equivalent to `var?`).

### `isVariableEquals($x1, $x2)`

Returns true if `$x1` and `$x2` refer to the same variable (equivalent to
`var=?`).

### `mzero()`

Return the empty stream (equivalent to `mzero`).

### `walk($u, $s)`

Searches for a variable's value in the substitution. If a non-variable term is
walked, return that term.

### `extS($x, $v, $s)`

Extends the substitution with a new binding  (equivalent to `ext-s`).

### `unit($sC)`

Lifts the state into a stream whose only element is that state.

### `unify($u, $v, $s)`

Unifies two terms in a substitution.

### `eq($u, $v)`

Returns a goal that succeeds if two terms unify in the received state
(equivalent to `≡` from the paper and `==` from the reference implementation).

### `callFresh($f)`

Returns a goal given a unary function whose body is a goal (equivalent to `call/fresh`).

### `mplus($d1, $d2)`

Merges streams.

### `bind($d, $g)`

Invokes a goal on each element of a stream.

### `disj($g1, $g2)`

Returns a goal that succeeds if either of the given goals succeed.

### `conj($g1, $g2)`

Returns a goal that succeeds if both given goals succeed.

### `emptyState()`

Returns a state with an empty substitution and variable index set to 0 (equivalent to `empty-state`).

### `pull($d)`

Advances a stream until it matures.

### `takeAll($d)`

Returns all results from a stream (equivalent to `take-all`).

### `take($n, $d)`

Returns a `$n` results from a stream.

### `reifyName($n)`

Returns a string name for a given number (equivalent to `reify-name`).

### `reifyS($v, $s)`

Reifies a state's substitution with respect to a variable (equivalent to `reify-s`).

### `walkStar($v, $s)`

Equivalent to `walk*`.

### `reifyFirst($sC)`

Equivalent to `reify-1st`.

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
