<?php
require_once 'PHPUnit/Framework/TestCase.php';

abstract class PHP_ChangeCoverage_AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Do not backup static test properties.
     *
     * @var boolean
     */
    protected $backupStaticAttributes = false;

    protected function setUp()
    {
        parent::setUp();

        spl_autoload_register( array( $this, 'autoload' ) );
    }

    protected function tearDown()
    {
        foreach ( spl_autoload_functions() as $function )
        {
            if ( $function === 'phpunit_autoload' )
            {
                continue;
            }
            spl_autoload_unregister( $function );
        }

        parent::tearDown();
    }

    public function autoload( $className )
    {
        if ( strpos( $className, 'PHP_ChangeCoverage_' ) === 0 )
        {
            include dirname( __FILE__ ) . '/../../../source/' . strtr( $className, '_', '/' ) . '.php';
        }
        else if ( strpos( $className, 'PHP_CodeCoverage_' ) === 0 )
        {
            include strtr( $className, '_', '/' ) . '.php';
        }
    }
}