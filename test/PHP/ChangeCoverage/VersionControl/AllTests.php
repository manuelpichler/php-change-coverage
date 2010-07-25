<?php

require_once 'PHPUnit/Framework.php';

class PHP_ChangeCoverage_VersionControl_AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * Constructs a new test suite instance.
     */
    public function __construct()
    {
        $this->setName( 'PHP::ChangeCoverage::VersionControl::AllTests' );

        PHPUnit_Util_Filter::addDirectoryToWhitelist(
            realpath( dirname( __FILE__ ) . '/../../../../source/' )
        );
    }

    public static function suite()
    {
        return new PHP_ChangeCoverage_VersionControl_AllTests();
    }
}