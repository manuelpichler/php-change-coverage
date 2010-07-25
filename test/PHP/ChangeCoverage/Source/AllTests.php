<?php

require_once 'PHPUnit/Framework.php';

require_once dirname( __FILE__ ) . '/FileUnitTest.php';
require_once dirname( __FILE__ ) . '/LineUnitTest.php';

class PHP_ChangeCoverage_Source_AllTests extends PHPUnit_Framework_TestSuite
{
    /**
     * Constructs a new test suite instance.
     */
    public function __construct()
    {
        $this->setName( 'PHP::ChangeCoverage::Source::AllTests' );

        PHPUnit_Util_Filter::addDirectoryToWhitelist(
            realpath( dirname( __FILE__ ) . '/../../../../source/' )
        );

        $this->addTestSuite( 'PHP_ChangeCoverage_Source_FileUnitTest' );
        $this->addTestSuite( 'PHP_ChangeCoverage_Source_LineUnitTest' );
    }

    public static function suite()
    {
        return new PHP_ChangeCoverage_Source_AllTests();
    }
}