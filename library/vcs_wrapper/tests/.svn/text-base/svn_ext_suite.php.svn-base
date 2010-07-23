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
 * Couchdb backend tests
 */
require 'svn-ext/checkout.php';
require 'svn-ext/directory.php';
require 'svn-ext/file.php';

/**
* Test suite for vcs
*/
class vcsSvnExtTestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Basic constructor for test suite
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'SVN ext wrapper suite' );

        $this->addTest( vcsSvnExtCheckoutTests::suite() );
        $this->addTest( vcsSvnExtDirectoryTests::suite() );
        $this->addTest( vcsSvnExtFileTests::suite() );
    }

    /**
     * Return test suite
     * 
     * @return prpTestSuite
     */
    public static function suite()
    {
        return new vcsSvnExtTestSuite( __CLASS__ );
    }
}

