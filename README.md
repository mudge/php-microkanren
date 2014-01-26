# PHPµKanren

A PHP implementation of [Jason Hemann and Daniel P. Friedman's
µKanren](http://webyrd.net/scheme-2013/papers/HemannMuKanren2013.pdf).

## Usage

```php
$f = MicroKanren::callFresh(function ($q) {
  return MicroKanren::eq($q, 5);
});
$f(MicroKanren::emptyState());

/* =>
 * Array
 * (
 *     [0] => Array
 *         (
 *             [0] => Array
 *                 (
 *                     [0] => Array
 *                         (
 *                             [0] => Array
 *                                 (
 *                                     [0] => 0
 *                                 )
 * 
 *                             [1] => 5
 *                         )
 * 
 *                     [1] => 
 *                 )
 * 
 *             [1] => 1
 *         )
 * 
 *     [1] => 
 * )
 */
```

See [the test
suite](https://github.com/mudge/PHPMicroKanren/blob/master/tests/MicroKanrenTest.php)
for more examples of usage.

## References

* [Justin S. Leitgeb's microKanren in Ruby](https://github.com/jsl/ruby_ukanren);
* [Scott Vokes' Lua port of microKanren](https://github.com/silentbicycle/lua-ukanren);
* [Jason Hemann and Daniel P. Friedman's reference Scheme implementation](https://github.com/jasonhemann/microKanren).

## License

Copyright © 2014 Paul Mucur.

Distributed under the MIT License.
