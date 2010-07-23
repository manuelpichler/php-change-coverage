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
require 'cache/cache.php';
require 'cache/sqlite_metadata.php';
require 'cache/filesystem_metadata.php';

/**
* Test suite for vcs
*/
class vcsCacheTestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Basic constructor for test suite
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'Cache suite' );

        $this->addTest( vcsSqliteCacheMetaDataTests::suite() );
        $this->addTest( vcsFileSystemCacheMetaDataTests::suite() );
        $this->addTest( vcsCacheTests::suite() );
    }

    /**
     * Return test suite
     * 
     * @return prpTestSuite
     */
    public static function suite()
    {
        return new vcsCacheTestSuite( __CLASS__ );
    }
}

