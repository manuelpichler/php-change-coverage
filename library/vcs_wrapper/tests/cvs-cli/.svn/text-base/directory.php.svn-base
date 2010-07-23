<?php
/**
 * Basic test cases for framework
 *
 * @version $Revision$
 * @license GPLv3
 */

/**
 * Tests for the CVS Cli wrapper
 */
class vcsCvsCliDirectoryTests extends vcsTestCase
{
    /**
     * Return test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        return new PHPUnit_Framework_TestSuite( __CLASS__ );
    }

    public function setUp()
    {
        parent::setUp();

        // Create a cache, required for all VCS wrappers to store metadata
        // information
        vcsCache::initialize( $this->createTempDir() );
    }

    public function testIterateRootDirContents()
    {
        $repository = new vcsCvsCliCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $dir = new vcsCvsCliDirectory( $this->tempDir, '/' );

        $files = array();
        foreach ( $dir as $file )
        {
            // Stupid, but cvs also checks out the not versions .svn folders
            if ( strpos( (string) $file, '.svn' ) === false )
            {
                $files[] = (string) $file;
            }
        }
        sort( $files );

        $this->assertEquals(
            array(
                '/dir1/',
                '/file'
            ),
            $files
        );
    }

    public function testRecursiveIterator()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $dir      = new vcsCvsCliDirectory( $this->tempDir, '/' );
        $iterator = new RecursiveIteratorIterator( $dir, RecursiveIteratorIterator::SELF_FIRST );

        $files = array();
        foreach ( $iterator as $file )
        {
            // Stupid, but cvs also checks out the not versions .svn folders
            if ( strpos( (string) $file, '.svn' ) === false )
            {
                $files[] = (string) $file;
            }
        }
        sort( $files );

        $this->assertEquals(
            array(
                '/dir1/',
                '/dir1/file',
                '/dir1/file1',
                '/file'
            ),
            $files
        );
    }

    public function testIterateSubDirContents()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $dir = new vcsCvsCliDirectory( $this->tempDir, '/dir1/' );

        $files = array();
        foreach ( $dir as $file )
        {
            // Stupid, but cvs also checks out the not versions .svn folders
            if ( strpos( (string) $file, '.svn' ) === false )
            {
                $files[] = (string) $file;
            }
        }
        sort( $files );

        $this->assertEquals(
            array(
                '/dir1/file',
                '/dir1/file1',
            ),
            $files
        );
    }
}
