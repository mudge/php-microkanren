<?php
namespace MicroKanren\Core;

require_once __DIR__ . '/../../src/MicroKanren/Core.php';
require_once __DIR__ . '/TestPrograms.php';

class CoreTest extends \PHPUnit_Framework_TestCase
{
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
}
