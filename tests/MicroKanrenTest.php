<?php
require_once __DIR__ . '/../src/MicroKanren.php';

class MicroKanrenTest extends PHPUnit_Framework_TestCase
{
    public function testVari()
    {
        $this->assertEquals(array(1), MicroKanren::vari(1));
    }

    public function testVariQ()
    {
        $this->assertTrue(MicroKanren::isVari(array(1)));
        $this->assertFalse(MicroKanren::isVari(1));
    }

    public function testVariEquals()
    {
        $this->assertTrue(MicroKanren::variEquals(array(1, 2), array(1, 2)));
        $this->assertFalse(MicroKanren::variEquals(array(1), array(2)));
    }

    public function testConsCarCdr()
    {
        $cell = MicroKanren::cons(2, MicroKanren::cons(1, null));
        $this->assertEquals(2, MicroKanren::car($cell));
        $this->assertEquals(1, MicroKanren::car(MicroKanren::cdr($cell)));
    }

    public function testAssp()
    {
        $cell = MicroKanren::cons(1, MicroKanren::cons(3, MicroKanren::cons(2, null)));
        $this->assertEquals(2, MicroKanren::assp(function ($x) { return $x === 2; }, $cell));
        $this->assertNull(MicroKanren::assp(function ($x) { return $x === 5; }, $cell));
    }

    public function testSecondSetT1()
    {
        $x = MicroKanren::callFresh(function ($q) {
            return MicroKanren::eq($q, 5);
        });
        $result = $x(MicroKanren::emptyState());
        $this->assertEquals(array(array(array(array(0), 5), array()), 1), MicroKanren::car($result));
    }

    public function testSecondSetT2()
    {
        $x = MicroKanren::callFresh(function ($q) {
            return MicroKanren::eq($q, 5);
        });
        $result = $x(MicroKanren::emptyState());
        $this->assertEquals(array(), MicroKanren::cdr($result));
    }

    public static function aAndB()
    {
        return MicroKanren::conj(
            MicroKanren::callFresh(function ($a) {
                return MicroKanren::eq($a, 7);
            }),
            MicroKanren::callFresh(function ($b) {
                return MicroKanren::disj(
                    MicroKanren::eq($b, 5),
                    MicroKanren::eq($b, 6)
                );
            })
        );
    }

    public function testSecondSetT3()
    {
        $x = self::aAndB();
        $result = $x(MicroKanren::emptyState());
        $this->assertEquals(
            array(array(array(array(1), 5), array(array(array(0), 7), array())), 2),
            MicroKanren::car($result)
        );
    }

    public function testSecondSetT3Take()
    {
        $x = self::aAndB();
        $result = $x(MicroKanren::emptyState());
        $this->assertEquals(
            array(array(array(array(array(1), 5), array(array(array(0), 7), array())), 2), array()),
            MicroKanren::take(1, $result)
        );
    }

    public function testSecondSetT4()
    {
        $x = self::aAndB();
        $result = $x(MicroKanren::emptyState());
        $this->assertEquals(
            array(array(array(array(1), 6), array(array(array(0), 7), array())), 2),
            MicroKanren::car(MicroKanren::cdr($result))
        );
    }

    public function testSecondSetT5()
    {
        $x = self::aAndB();
        $result = $x(MicroKanren::emptyState());
        $this->assertEquals(array(), MicroKanren::cdr(MicroKanren::cdr($result)));
    }

    public static function fives($x)
    {
        return MicroKanren::disj(
            MicroKanren::eq($x, 5),
            function ($aC) use ($x) {
                return function () use ($x, $aC) {
                    $f = MicroKanrenTest::fives($x);
                    return $f($aC);
                };
            }
        );
    }

    public function testWhoCares()
    { $x = MicroKanren::callFresh(function ($q) {
            return MicroKanrenTest::fives($q);
        });
        $result = $x(MicroKanren::emptyState());
        $this->assertEquals(
            array(array(array(array(array(0), 5), array()), 1), array()),
            MicroKanren::take(1, $result)
        );
    }

    public function testTake2AAndBStream()
    {
        $f = self::aAndB();
        $d = $f(MicroKanren::emptyState());
        $this->assertEquals(
            array(
                array(array(array(array(1), 5), array(array(array(0), 7), array())), 2),
                array(array(array(array(array(1), 6), array(array(array(0), 7), array())), 2), array())
            ),
            MicroKanren::take(2, $d)
        );
    }

    public function testTakeAllAAndBStream()
    {
        $f = self::aAndB();
        $d = $f(MicroKanren::emptyState());
        $this->assertEquals(
            array(
                array(array(array(array(1), 5), array(array(array(0), 7), array())), 2),
                array(array(array(array(array(1), 6), array(array(array(0), 7), array())), 2), array())
            ),
            MicroKanren::takeAll($d)
        );
    }
}
