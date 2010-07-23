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
define( 'VCS_TEST', __FILE__ );
$files = include ( $base = dirname(  __FILE__ ) . '/../src/classes/' ) . 'autoload.php';
foreach ( $files as $class => $file )
{
    require_once $base . $file;

    if ( strpos( $file, '/external/' ) === false )
    {
        PHPUnit_Util_Filter::addFileToWhitelist( $base . $file );
    }
}

require 'base_test.php';

/**
 * Test suites
 */
require 'diff_suite.php';
require 'cache_suite.php';
require 'svn_cli_suite.php';
require 'svn_ext_suite.php';
require 'git_cli_suite.php';
require 'hg_cli_suite.php';
require 'bzr_cli_suite.php';
require 'cvs_cli_suite.php';
require 'archive_suite.php';

/**
* Test suite for vcs
*/
class vcsTestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Basic constructor for test suite
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'vcsWrapper - A PHP version control system wrapper' );

        $this->addTestSuite( vcsDiffTestSuite::suite() );
        $this->addTestSuite( vcsCacheTestSuite::suite() );
        $this->addTestSuite( vcsSvnCliTestSuite::suite() );
        $this->addTestSuite( vcsSvnExtTestSuite::suite() );
        $this->addTestSuite( vcsGitCliTestSuite::suite() );
        $this->addTestSuite( vcsHgCliTestSuite::suite() );
        $this->addTestSuite( vcsBzrCliTestSuite::suite() );
        $this->addTestSuite( vcsCvsCliTestSuite::suite() );
        $this->addTestSuite( vcsArchiveTestSuite::suite() );
    }

    /**
     * Return test suite
     * 
     * @return prpTestSuite
     */
    public static function suite()
    {
        return new vcsTestSuite( __CLASS__ );
    }
}

