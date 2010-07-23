<?php
class Foo
{
    public $x = 42;

    public function bar()
    {
        if ( $this->x > 42 )
        { 
            $this->baz( $this->x ); 
        }
        return $this->x;
    }

    public function baz( $y )
    {
        return $y * 23;
    }
}
