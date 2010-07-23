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
 * Bazaar backend tests
 */
require 'bzr-cli/checkout.php';
require 'bzr-cli/directory.php';
require 'bzr-cli/file.php';

/**
* @group bazaar
* Test suite for vcs
*/
class vcsBzrCliTestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Basic constructor for test suite
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'Mercurial cli wrapper suite' );

        $this->addTest( vcsBzrCliCheckoutTests::suite() );
        $this->addTest( vcsBzrCliDirectoryTests::suite() );
        $this->addTest( vcsBzrCliFileTests::suite() );
    }

    /**
     * Return test suite
     * 
     * @return prpTestSuite
     */
    public static function suite()
    {
        return new vcsBzrCliTestSuite( __CLASS__ );
    }
}

