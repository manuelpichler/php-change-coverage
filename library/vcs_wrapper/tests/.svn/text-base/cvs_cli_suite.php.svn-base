<?php
/**
 * vcs main test suite
 *
 * @version $Revision$
 * @license LGPLv3
 */

/*
 * Set file whitelist for phpunit
 */
if ( !defined( 'VCS_TEST' ) )
{
    $files = include ( $base = dirname(  __FILE__ ) . '/../src/classes/' ) . 'autoload.php';
    foreach ( $files as $class => $file )
    {
        require_once $base . $file;
    }

    require 'base_test.php';
}

/**
 * Load cvs wrapper test cases
 */
require 'cvs-cli/checkout.php';
require 'cvs-cli/directory.php';
require 'cvs-cli/file.php';

/**
* Test suite for vcs CVS cli wrapper
*/
class vcsCvsCliTestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Basic constructor for test suite
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'CVS cli wrapper suite' );

        $this->addTest( vcsCvsCliCheckoutTests::suite() );
        $this->addTest( vcsCvsCliDirectoryTests::suite() );
        $this->addTest( vcsCvsCliFileTests::suite() );
    }

    /**
     * Return test suite
     *
     * @return prpTestSuite
     */
    public static function suite()
    {
        return new vcsCvsCliTestSuite( __CLASS__ );
    }
}

