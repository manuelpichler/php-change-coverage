<?php
/**
 * vcs main test suite
 *
 * @version $Revision: 969 $
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
require 'hg-cli/checkout.php';
require 'hg-cli/directory.php';
require 'hg-cli/file.php';

/**
* @group mercurial
* Test suite for vcs
*/
class vcsHgCliTestSuite extends PHPUnit_Framework_TestSuite
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

        $this->addTest( vcsHgCliCheckoutTests::suite() );
        $this->addTest( vcsHgCliDirectoryTests::suite() );
        $this->addTest( vcsHgCliFileTests::suite() );
    }

    /**
     * Return test suite
     * 
     * @return prpTestSuite
     */
    public static function suite()
    {
        return new vcsHgCliTestSuite( __CLASS__ );
    }
}

