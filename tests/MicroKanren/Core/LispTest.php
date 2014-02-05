<?php
namespace MicroKanren\Core;

require_once __DIR__ . '/../../../src/MicroKanren/Core/Lisp.php';

class LispTest extends \PHPUnit_Framework_TestCase
{
    public function testConsWithTwoValues()
    {
        $list = cons(1, 2);
        $this->assertEquals('(1 . 2)', sprintf('%s', $list));
    }

    public function testConsWithListInCar()
    {
        $list = cons(1, cons(2, 3));
        $this->assertEquals('(1 . (2 . 3))', sprintf('%s', $list));
    }

    public function testConsWithMultipleLists()
    {
        $list = cons(1, cons(2, cons(3, nil())));
        $this->assertEquals('(1 . (2 . (3)))', sprintf('%s', $list));
    }

    public function testNil()
    {
        $list = nil();
        $this->assertEquals('()', sprintf('%s', $list));
    }

    public function testNilsAreTheSame()
    {
        $this->assertSame(nil(), nil());
    }

    public function testConsWithNil()
    {
        $list = cons(1, nil());
        $this->assertEquals('(1)', sprintf('%s', $list));
    }

    public function testConsIsAPair()
    {
        $list = cons(1, 2);
        $this->assertTrue(isPair($list));
    }

    public function testConsWithOneElementIsAPair()
    {
        $list = cons(1, nil());
        $this->assertTrue(isPair($list));
    }

    public function testNilIsNotAPair()
    {
        $list = nil();
        $this->assertFalse(isPair($list));
    }

    public function testCarOfPair()
    {
        $list = cons(1, 2);
        $this->assertEquals(1, car($list));
    }

    public function testCarOfSingleList()
    {
        $list = cons(1, nil());
        $this->assertEquals(1, car($list));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage () is not a pair
     */
    public function testCarOfNil()
    {
        $list = nil();
        car($list);
    }

    public function testCarDoesNotCopy()
    {
        $var = cons(1, 2);
        $list = cons($var, nil());

        $this->assertSame($var, car($list));
    }

    public function testCdrOfPair()
    {
        $list = cons(1, 2);
        $this->assertEquals(2, cdr($list));
    }

    public function testCdrOfList()
    {
        $list = cons(1, cons(2, 3));
        $this->assertEquals(cons(2, 3), cdr($list));
    }

    public function testCdrOfSingleList()
    {
        $list = cons(1, nil());
        $this->assertEquals(nil(), cdr($list));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage () is not a pair
     */
    public function testCdrOfNil()
    {
        $list = nil();
        cdr($list);
    }

    public function testCdrDoesNotCopy()
    {
        $var = cons(1, 2);
        $list = cons($var, $var);

        $this->assertSame($var, cdr($list));
    }


    public function testAlist()
    {
        $list = alist(1, 2, 3);

        $this->assertEquals(cons(1, cons(2, cons(3, nil()))), $list);
    }

    public function testAlistWithNoElements()
    {
        $list = alist();

        $this->assertEquals(nil(), $list);
    }

    public function testAssp()
    {
        $list = alist(cons(1, 'a'), cons(2, 'b'));
        $isEven = function ($x) { return $x % 2 === 0; };
        $isOdd = function ($x) { return $x % 2 !== 0; };

        $this->assertEquals(cons(1, 'a'), assp($isOdd, $list));
        $this->assertEquals(cons(2, 'b'), assp($isEven, $list));
    }

    public function testAsspWithNil()
    {
        $list = nil();
        $isEven = function ($x) { return $x % 2 === 0; };

        $this->assertFalse(assp($isEven, $list));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage improperly formed alist
     */
    public function testAsspWithMalformedAlist()
    {
        $list = cons(1, 2);
        $isEven = function ($x) { return $x % 2 === 0; };

        assp($isEven, $list);
    }

    public function testAsspDoesNotCopy()
    {
        $a = cons(1, 'a');
        $b = cons(2, 'b');
        $list = alist($a, $b);
        $isEven = function ($x) { return $x % 2 === 0; };
        $isOdd = function ($x) { return $x % 2 !== 0; };

        $this->assertSame($a, assp($isOdd, $list));
        $this->assertSame($b, assp($isEven, $list));
    }

    public function testIsNull()
    {
        $this->assertTrue(isNull(nil()));
        $this->assertFalse(isNull(1));
        $this->assertFalse(isNull(cons(1, 2)));
    }

    public function testLengthOfNil()
    {
        $this->assertEquals(0, length(nil()));
    }

    public function testLengthOfList()
    {
        $this->assertEquals(3, length(alist(1, 2, 3)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is not a proper list
     */
    public function testLengthOfNonList()
    {
        length(4);
    }

    public function testMapOverNil()
    {
        $isOdd = function ($x) { return $x % 2 !== 0; };

        $this->assertEquals(nil(), map($isOdd, nil()));
    }

    public function testMapOverList()
    {
        $isOdd = function ($x) { return $x % 2 !== 0; };

        $this->assertEquals(alist(true, false, true), map($isOdd, alist(1, 2, 3)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is not a proper list
     */
    public function testMapOverNonList()
    {
        $isOdd = function ($x) { return $x % 2 !== 0; };

        map($isOdd, 4);
    }
}
