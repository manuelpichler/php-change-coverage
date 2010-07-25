<?php
class PHP_ChangeCoverage_Source_Line
{
    /**
     * The line number.
     *
     * @var integer
     */
    private $number = 0;

    /**
     * How often was this line executed.
     *
     * @var integer
     */
    private $count = 0;

    /**
     * Has someone changed this line within the specified time range.
     *
     * @var boolean
     */
    private $changed = false;

    /**
     * Constructs a new source line instance.
     *
     * @param integer $number The line number.
     * @param integer $count  How often was this line executed.
     */
    public function __construct( $number, $count )
    {
        $this->number = $number;
        $this->count  = $count;
    }

    /**
     * Returns the number of this line.
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Returns how often this line was executed.
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Flags this line as changed.
     * 
     * @return void
     */
    public function setChanged()
    {
        $this->changed = true;
    }

    public function hasChanged()
    {
        return $this->changed;
    }

    /**
     * Decrements the number of executions for this line.
     *
     * @return void
     * @todo This is not good. Decrementing should happen somewhere else in a
     *       temporary data structured.
     */
    public function decrementCount()
    {
        --$this->count;
    }
}