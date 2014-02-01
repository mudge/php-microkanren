# PHPµKanren [![Build Status](https://travis-ci.org/mudge/php-microkanren.png?branch=master)](https://travis-ci.org/mudge/php-microkanren)

A PHP implementation of [Jason Hemann and Daniel P. Friedman's
µKanren](http://webyrd.net/scheme-2013/papers/HemannMuKanren2013.pdf).

```javascript
"mudge/php-microkanren": "dev-master"
```

## Usage

```php
use MicroKanren as U;

$f = U\callFresh(function ($q) {
  return U\eq($q, 5);
});

echo $f(U\emptyState());
/* => (((#(0) . 5)) . 1) */
```

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
