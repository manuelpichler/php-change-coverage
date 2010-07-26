<?php

require_once 'PHPUnit/Framework.php';

require_once dirname( __FILE__ ) . '/Source/AllTests.php';

require_once dirname( __FILE__ ) . '/XdebugUnitTest.php';

class PHP_ChangeCoverage_AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * Constructs a new test suite instance.
     */
    public function __construct()
    {
        $this->setName( 'PHP::ChangeCoverage::AllTests' );

        PHPUnit_Util_Filter::addDirectoryToWhitelist(
            realpath( dirname( __FILE__ ) . '/../../../source/' )
        );

        $this->addTest( PHP_ChangeCoverage_Source_AllTests::suite() );

        $this->addTestSuite( 'PHP_ChangeCoverage_XdebugUnitTest' );
    }

    public static function suite()
    {
        return new PHP_ChangeCoverage_AllTests();
    }
}