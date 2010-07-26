<?php
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Base class for test cases of the change coverage application.
 *
 * @category  QualityAssurance
 * @package   PHP_ChangeCoverage
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
abstract class PHP_ChangeCoverage_AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Do not backup static test properties.
     *
     * @var boolean
     */
    protected $backupStaticAttributes = false;

    /**
     * Setup registers the autoload mechanism for the source under test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        spl_autoload_register( array( $this, 'autoload' ) );
    }

    /**
     * This method removes the autoloader after each test.
     *
     * @return void
     */
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

    /**
     * Autoload implementation for the test cases.
     *
     * @param string $className Name of the class with unknown source.
     *
     * @return void
     */
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