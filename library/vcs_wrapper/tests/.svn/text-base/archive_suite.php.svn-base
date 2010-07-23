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
require 'archive/zip_checkout.php';
require 'archive/directory.php';
require 'archive/file.php';

/**
* Test suite for vcs
*/
class vcsArchiveTestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Basic constructor for test suite
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'Archive wrapper suite' );

        $this->addTest( vcsZipArchiveCheckoutTests::suite() );
        $this->addTest( vcsArchiveDirectoryTests::suite() );
        $this->addTest( vcsArchiveFileTests::suite() );
    }

    /**
     * Return test suite
     * 
     * @return prpTestSuite
     */
    public static function suite()
    {
        return new vcsArchiveTestSuite( __CLASS__ );
    }
}

