<?php
namespace MicroKanren;

require_once __DIR__ . '/../../src/MicroKanren/Core.php';
require_once __DIR__ . '/TestPrograms.php';

use MicroKanren as U;

class CoreTest extends \PHPUnit_Framework_TestCase
{
    public function testConsWithTwoValues()
    {
        $list = U\cons(1, 2);
        $this->assertEquals('(1 . 2)', sprintf('%s', $list));
    }

    public function testConsWithListInCar()
    {
        $list = U\cons(1, U\cons(2, 3));
        $this->assertEquals('(1 . (2 . 3))', sprintf('%s', $list));
    }

    public function testConsWithMultipleLists()
    {
        $list = U\cons(1, U\cons(2, U\cons(3, U\nil())));
        $this->assertEquals('(1 . (2 . (3)))', sprintf('%s', $list));
    }

    public function testNil()
    {
        $list = U\nil();
        $this->assertEquals('()', sprintf('%s', $list));
    }

    public function testConsWithNil()
    {
        $list = U\cons(1, U\nil());
        $this->assertEquals('(1)', sprintf('%s', $list));
    }

    public function testConsIsAPair()
    {
        $list = U\cons(1, 2);
        $this->assertTrue(U\isPair($list));
    }

    public function testConsWithOneElementIsAPair()
    {
        $list = U\cons(1, U\nil());
        $this->assertTrue(U\isPair($list));
    }

    public function testNilIsNotAPair()
    {
        $list = U\nil();
        $this->assertFalse(U\isPair($list));
    }

    public function testCarOfPair()
    {
        $list = U\cons(1, 2);
        $this->assertEquals(1, U\car($list));
    }

    public function testCarOfSingleList()
    {
        $list = U\cons(1, U\nil());
        $this->assertEquals(1, U\car($list));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage () is not a pair
     */
    public function testCarOfNil()
    {
        $list = U\nil();
        U\car($list);
    }

    public function testCdrOfPair()
    {
        $list = U\cons(1, 2);
        $this->assertEquals(2, U\cdr($list));
    }

    public function testCdrOfList()
    {
        $list = U\cons(1, U\cons(2, 3));
        $this->assertEquals(U\cons(2, 3), U\cdr($list));
    }

    public function testCdrOfSingleList()
    {
        $list = U\cons(1, U\nil());
        $this->assertEquals(U\nil(), U\cdr($list));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage () is not a pair
     */
    public function testCdrOfNil()
    {
        $list = U\nil();
        U\cdr($list);
    }

    public function testAlist()
    {
        $list = U\alist(1, 2, 3);

        $this->assertEquals(U\cons(1, U\cons(2, U\cons(3, U\nil()))), $list);
    }

    public function testAlistWithNoElements()
    {
        $list = U\alist();

        $this->assertEquals(U\nil(), $list);
    }

    public function testAssp()
    {
        $list = U\alist(U\cons(1, 'a'), U\cons(2, 'b'));
        $isEven = function ($x) { return $x % 2 === 0; };
        $isOdd = function ($x) { return $x % 2 !== 0; };

        $this->assertEquals(U\cons(1, 'a'), U\assp($isOdd, $list));
        $this->assertEquals(U\cons(2, 'b'), U\assp($isEven, $list));
    }

    public function testAsspWithNil()
    {
        $list = U\nil();
        $isEven = function ($x) { return $x % 2 === 0; };

        $this->assertFalse(U\assp($isEven, $list));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage improperly formed alist
     */
    public function testAsspWithMalformedAlist()
    {
        $list = U\cons(1, 2);
        $isEven = function ($x) { return $x % 2 === 0; };

        U\assp($isEven, $list);
    }

    public function testIsEqv()
    {
        $list = U\cons(1, 2);
        $list2 = U\cons(1, 2);

        $this->assertTrue(U\isEqv($list, $list));
        $this->assertFalse(U\isEqv($list, $list2));
    }

    public function testVariable()
    {
        $var = U\variable(1);

        $this->assertEquals('#(1)', sprintf('%s', $var));
    }

    public function testIsVariable()
    {
        $var = U\variable(1);
        $list = U\cons(1, 2);

        $this->assertTrue(U\isVariable($var));
        $this->assertFalse(U\isVariable($list));
    }

    public function testIsVariableEquals()
    {
        $var = U\variable(1);
        $var2 = U\variable(1);
        $var3 = U\variable(2);

        $this->assertTrue(U\isVariableEquals($var, $var2));
        $this->assertFalse(U\isVariableEquals($var2, $var3));
    }

    public function testMzero()
    {
        $this->assertEquals(U\nil(), U\mzero());
    }

    public function testIsNull()
    {
        $this->assertTrue(U\isNull(U\nil()));
        $this->assertFalse(U\isNull(1));
        $this->assertFalse(U\isNull(U\cons(1, 2)));
    }

    public function testSecondSetT1()
    {
        $x = U\callFresh(function ($q) { return U\eq($q, 5); });
        $result = $x(U\emptyState());

        $this->assertEquals(
            '(((#(0) . 5)) . 1)',
            sprintf('%s', U\car($result))
        );
    }

    public function testSecondSetT2()
    {
        $x = U\callFresh(function ($q) { return U\eq($q, 5); });
        $result = $x(U\emptyState());

        $this->assertEquals('()', sprintf('%s', U\cdr($result)));
    }

    public function testSecondSetT3()
    {
        $x = aAndB();
        $result = $x(U\emptyState());

        $this->assertEquals(
            '(((#(1) . 5) . ((#(0) . 7))) . 2)',
            sprintf('%s', U\car($result))
        );
    }

    public function testSecondSetT3Take()
    {
        $x = aAndB();
        $result = $x(U\emptyState());

        $this->assertEquals(
            '((((#(1) . 5) . ((#(0) . 7))) . 2))',
            sprintf('%s', U\take(1, $result))
        );
    }

    public function testSecondSetT4()
    {
        $x = aAndB();
        $result = $x(U\emptyState());

        $this->assertEquals(
            '(((#(1) . 6) . ((#(0) . 7))) . 2)',
            sprintf('%s', U\car(U\cdr($result)))
        );
    }

    public function testSecondSetT5()
    {
        $x = aAndB();
        $result = $x(U\emptyState());

        $this->assertEquals('()', sprintf('%s', U\cdr(U\cdr($result))));
    }

    public function testWhoCares()
    {
        $x = U\callFresh(function ($q) {
            return fives($q);
        });
        $result = $x(U\emptyState());

        $this->assertEquals(
            '((((#(0) . 5)) . 1))',
            sprintf('%s', U\take(1, $result))
        );
    }

    public function testTake2AAndBStream()
    {
        $f = aAndB();
        $d = $f(U\emptyState());

        $this->assertEquals(
            '((((#(1) . 5) . ((#(0) . 7))) . 2) . ((((#(1) . 6) . ((#(0) . 7))) . 2)))',
            sprintf('%s', U\take(2, $d))
        );
    }

    public function testTakeAllAAndBStream()
    {
        $f = aAndB();
        $d = $f(U\emptyState());

        $this->assertEquals(
            '((((#(1) . 5) . ((#(0) . 7))) . 2) . ((((#(1) . 6) . ((#(0) . 7))) . 2)))',
            sprintf('%s', U\takeAll($d))
        );
    }

    public function testGroundAppendo()
    {
        $g = groundAppendo();
        $h = $g(U\emptyState());
        $result = $h();

        $this->assertEquals(
            '(((#(2) . (b)) . ((#(1)) . ((#(0) . a)))) . 3)',
            sprintf('%s', U\car($result))
        );
    }

    public function testGroundAppendo2()
    {
        $g = groundAppendo();
        $h = $g(U\emptyState());
        $result = $h();

        $this->assertEquals(
            '(((#(2) . (b)) . ((#(1)) . ((#(0) . a)))) . 3)',
            sprintf('%s', U\car($result))
        );
    }

    public function testAppendo()
    {
        $g = callAppendo();
        $h = $g(U\emptyState());

        $this->assertEquals(
            '((((#(0) . (#(1) . (#(2) . (#(3))))) . ((#(2) . #(3)) . ((#(1))))) . 4) . ((((#(0) . (#(1) . (#(2) . (#(3))))) . ((#(2) . #(6)) . ((#(5)) . ((#(3) . (#(4) . #(6))) . ((#(1) . (#(4) . #(5)))))))) . 7)))',
            sprintf('%s', U\take(2, $h))
        );
    }

    public function testAppendo2()
    {
        $g = callAppendo2();
        $h = $g(U\emptyState());

        $this->assertEquals(
            '((((#(0) . (#(1) . (#(2) . (#(3))))) . ((#(2) . #(3)) . ((#(1))))) . 4) . ((((#(0) . (#(1) . (#(2) . (#(3))))) . ((#(3) . (#(4) . #(6))) . ((#(2) . #(6)) . ((#(5)) . ((#(1) . (#(4) . #(5)))))))) . 7)))',
            sprintf('%s', U\take(2, $h))
        );
    }

    public function testReifyFirstAcrossAppendo()
    {
        $f = callAppendo();
        $g = $f(U\emptyState());
        $h = U\take(2, $g);
        $result = U\map('MicroKanren\reifyFirst', $h);

        $this->assertEquals(
            '((() . (_.0 . (_.0))) . (((_.0) . (_.1 . ((_.0 . _.1))))))',
            sprintf('%s', $result)
        );
    }

    public function testReifyFirstAcrossAppendo2()
    {
        $f = callAppendo2();
        $g = $f(U\emptyState());
        $h = U\take(2, $g);
        $result = U\map('MicroKanren\reifyFirst', $h);

        $this->assertEquals(
            '((() . (_.0 . (_.0))) . (((_.0) . (_.1 . ((_.0 . _.1))))))',
            sprintf('%s', $result)
        );
    }

    public function testManyNonAns()
    {
        $g = manyNonAns();
        $h = $g(U\emptyState());

        $this->assertEquals(
            '((((#(0) . 3)) . 1))',
            sprintf('%s', U\take(1, $h))
        );
    }

    public function testLengthOfNil()
    {
        $this->assertEquals(0, U\length(U\nil()));
    }

    public function testLengthOfList()
    {
        $this->assertEquals(3, U\length(U\alist(1, 2, 3)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is not a proper list
     */
    public function testLengthOfNonList()
    {
        U\length(4);
    }

    public function testMapOverNil()
    {
        $isOdd = function ($x) { return $x % 2 !== 0; };

        $this->assertEquals(U\nil(), U\map($isOdd, U\nil()));
    }

    public function testMapOverList()
    {
        $isOdd = function ($x) { return $x % 2 !== 0; };

        $this->assertEquals(U\alist(true, false, true), U\map($isOdd, U\alist(1, 2, 3)));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage is not a proper list
     */
    public function testMapOverNonList()
    {
        $isOdd = function ($x) { return $x % 2 !== 0; };

        U\map($isOdd, 4);
    }
}
