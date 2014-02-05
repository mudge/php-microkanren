<?php
namespace MicroKanren\Core;

require_once __DIR__ . '/../../src/MicroKanren/Mini.php';

class MiniTest extends \PHPUnit_Framework_TestCase
{
    public function testConjPlus()
    {
        $x = callFresh(function ($q) {
            return callFresh(function ($r) use ($q) {
                return callFresh(function ($s) use ($q, $r) {
                    return conjPlus(eq($q, 1), eq($r, 2), eq($s, 3));
                });
            });
        });

        $result = $x(emptyState());
        $this->assertEquals(
            '((((#(2) . 3) . ((#(1) . 2) . ((#(0) . 1)))) . 3))',
            sprintf('%s', take(1, $result))
        );
    }

    public function testDisjPlus()
    {
        $x = callFresh(function ($q) {
            return disjPlus(eq($q, 1), eq($q, 2), eq($q, 'a'));
        });

        $result = $x(emptyState());
        $this->assertEquals(
            '((((#(0) . 1)) . 1) . ((((#(0) . 2)) . 1) . ((((#(0) . a)) . 1))))',
            sprintf('%s', take(3, $result))
        );
    }

    public function testFresh()
    {
        $x = fresh(function ($q, $r, $s) {
            return conjPlus(eq($q, 1), eq($r, 2), eq($s, 3));
        });

        $result = $x(emptyState());
        $this->assertEquals(
            '((((#(2) . 3) . ((#(1) . 2) . ((#(0) . 1)))) . 3))',
            sprintf('%s', take(1, $result))
        );
    }

    public function testFreshWithOneVariable()
    {
        $x = fresh(function ($q) {
            return conjPlus(eq($q, 1));
        });

        $result = $x(emptyState());
        $this->assertEquals(
            '((((#(0) . 1)) . 1))',
            sprintf('%s', take(1, $result))
        );
    }

    public function testFreshWithNoVariables()
    {
        $x = fresh(function () {
            return conjPlus(eq(1, 1));
        });

        $result = $x(emptyState());
        $this->assertEquals(
            '((() . 0))',
            sprintf('%s', take(1, $result))
        );
    }

    public function testCallGoal()
    {
        $x = fresh(function () {
            return conjPlus(eq(1, 1));
        });

        $result = callGoal($x);
        $this->assertEquals(
            '((() . 0))',
            sprintf('%s', take(1, $result))
        );
    }

    public function testRun()
    {
        $result = run(2, function ($q, $r) {
            return disjPlus(eq($q, 2), eq($q, 3), eq($q, 4));
        });

        $this->assertEquals(
            '(2 . (3))',
            sprintf('%s', $result)
        );
    }

    public function testRunStar()
    {
        $result = runStar(function ($q, $r) {
            return disjPlus(eq($q, 2), eq($q, 3), eq($q, 4));
        });

        $this->assertEquals(
            '(2 . (3 . (4)))',
            sprintf('%s', $result)
        );
    }
}
