<?php
namespace MicroKanren\Core;

require_once __DIR__ . '/../../src/MicroKanren/Core.php';
require_once __DIR__ . '/TestPrograms.php';

class CoreTest extends \PHPUnit_Framework_TestCase
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

    public function testVariable()
    {
        $var = variable(1);

        $this->assertEquals('#(1)', sprintf('%s', $var));
    }

    public function testIsVariable()
    {
        $var = variable(1);
        $list = cons(1, 2);

        $this->assertTrue(isVariable($var));
        $this->assertFalse(isVariable($list));
    }

    public function testIsVariableEquals()
    {
        $var = variable(1);
        $var2 = variable(1);
        $var3 = variable(2);

        $this->assertTrue(isVariableEquals($var, $var2));
        $this->assertFalse(isVariableEquals($var2, $var3));
    }

    public function testMzero()
    {
        $this->assertEquals(nil(), mzero());
    }

    public function testIsNull()
    {
        $this->assertTrue(isNull(nil()));
        $this->assertFalse(isNull(1));
        $this->assertFalse(isNull(cons(1, 2)));
    }

    public function testSecondSetT1()
    {
        $x = callFresh(function ($q) { return eq($q, 5); });
        $result = $x(emptyState());

        $this->assertEquals(
            '(((#(0) . 5)) . 1)',
            sprintf('%s', car($result))
        );
    }

    public function testSecondSetT2()
    {
        $x = callFresh(function ($q) { return eq($q, 5); });
        $result = $x(emptyState());

        $this->assertEquals('()', sprintf('%s', cdr($result)));
    }

    public function testSecondSetT3()
    {
        $x = aAndB();
        $result = $x(emptyState());

        $this->assertEquals(
            '(((#(1) . 5) . ((#(0) . 7))) . 2)',
            sprintf('%s', car($result))
        );
    }

    public function testSecondSetT3Take()
    {
        $x = aAndB();
        $result = $x(emptyState());

        $this->assertEquals(
            '((((#(1) . 5) . ((#(0) . 7))) . 2))',
            sprintf('%s', take(1, $result))
        );
    }

    public function testSecondSetT4()
    {
        $x = aAndB();
        $result = $x(emptyState());

        $this->assertEquals(
            '(((#(1) . 6) . ((#(0) . 7))) . 2)',
            sprintf('%s', car(cdr($result)))
        );
    }

    public function testSecondSetT5()
    {
        $x = aAndB();
        $result = $x(emptyState());

        $this->assertEquals('()', sprintf('%s', cdr(cdr($result))));
    }

    public function testWhoCares()
    {
        $x = callFresh(function ($q) {
            return fives($q);
        });
        $result = $x(emptyState());

        $this->assertEquals(
            '((((#(0) . 5)) . 1))',
            sprintf('%s', take(1, $result))
        );
    }

    public function testTake2AAndBStream()
    {
        $f = aAndB();
        $d = $f(emptyState());

        $this->assertEquals(
            '((((#(1) . 5) . ((#(0) . 7))) . 2) . ((((#(1) . 6) . ((#(0) . 7))) . 2)))',
            sprintf('%s', take(2, $d))
        );
    }

    public function testTakeAllAAndBStream()
    {
        $f = aAndB();
        $d = $f(emptyState());

        $this->assertEquals(
            '((((#(1) . 5) . ((#(0) . 7))) . 2) . ((((#(1) . 6) . ((#(0) . 7))) . 2)))',
            sprintf('%s', takeAll($d))
        );
    }

    public function testGroundAppendo()
    {
        $g = groundAppendo();
        $h = $g(emptyState());
        $result = $h();

        $this->assertEquals(
            '(((#(2) . (b)) . ((#(1)) . ((#(0) . a)))) . 3)',
            sprintf('%s', car($result))
        );
    }

    public function testGroundAppendo2()
    {
        $g = groundAppendo();
        $h = $g(emptyState());
        $result = $h();

        $this->assertEquals(
            '(((#(2) . (b)) . ((#(1)) . ((#(0) . a)))) . 3)',
            sprintf('%s', car($result))
        );
    }

    public function testAppendo()
    {
        $g = callAppendo();
        $h = $g(emptyState());

        $this->assertEquals(
            '((((#(0) . (#(1) . (#(2) . (#(3))))) . ((#(2) . #(3)) . ((#(1))))) . 4) . ((((#(0) . (#(1) . (#(2) . (#(3))))) . ((#(2) . #(6)) . ((#(5)) . ((#(3) . (#(4) . #(6))) . ((#(1) . (#(4) . #(5)))))))) . 7)))',
            sprintf('%s', take(2, $h))
        );
    }

    public function testAppendo2()
    {
        $g = callAppendo2();
        $h = $g(emptyState());

        $this->assertEquals(
            '((((#(0) . (#(1) . (#(2) . (#(3))))) . ((#(2) . #(3)) . ((#(1))))) . 4) . ((((#(0) . (#(1) . (#(2) . (#(3))))) . ((#(3) . (#(4) . #(6))) . ((#(2) . #(6)) . ((#(5)) . ((#(1) . (#(4) . #(5)))))))) . 7)))',
            sprintf('%s', take(2, $h))
        );
    }

    public function testReifyFirstAcrossAppendo()
    {
        $f = callAppendo();
        $g = $f(emptyState());
        $h = take(2, $g);
        $result = map('MicroKanren\Core\reifyFirst', $h);

        $this->assertEquals(
            '((() . (_.0 . (_.0))) . (((_.0) . (_.1 . ((_.0 . _.1))))))',
            sprintf('%s', $result)
        );
    }

    public function testReifyFirstAcrossAppendo2()
    {
        $f = callAppendo2();
        $g = $f(emptyState());
        $h = take(2, $g);
        $result = map('MicroKanren\Core\reifyFirst', $h);

        $this->assertEquals(
            '((() . (_.0 . (_.0))) . (((_.0) . (_.1 . ((_.0 . _.1))))))',
            sprintf('%s', $result)
        );
    }

    public function testManyNonAns()
    {
        $g = manyNonAns();
        $h = $g(emptyState());

        $this->assertEquals(
            '((((#(0) . 3)) . 1))',
            sprintf('%s', take(1, $h))
        );
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
