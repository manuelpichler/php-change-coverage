<?php
require_once 'PHPUnit/Framework/TestCase.php';

require_once __DIR__ . '/Code.php';

class Test extends PHPUnit_Framework_TestCase
{
    /**
     * @return void
     * @test
     */
    public function barReturnsForthyTwo()
    {
        $foo = new Foo();
        self::assertEquals( 42, $foo->bar() );
    }

    /**
     * @return void
     * @test
     */
    public function barReturnsStillForthyTwo()
    {
        $foo = new Foo();
        self::assertEquals( 42, $foo->bar() );
    }
}