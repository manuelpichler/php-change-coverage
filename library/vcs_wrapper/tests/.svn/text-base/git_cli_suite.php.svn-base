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
require 'git-cli/checkout.php';
require 'git-cli/directory.php';
require 'git-cli/file.php';

/**
* Test suite for vcs
*/
class vcsGitCliTestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Basic constructor for test suite
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'GIT cli wrapper suite' );

        $this->addTest( vcsGitCliCheckoutTests::suite() );
        $this->addTest( vcsGitCliDirectoryTests::suite() );
        $this->addTest( vcsGitCliFileTests::suite() );
    }

    /**
     * Return test suite
     * 
     * @return prpTestSuite
     */
    public static function suite()
    {
        return new vcsGitCliTestSuite( __CLASS__ );
    }
}

