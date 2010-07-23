<?php
/**
 * Basic test cases for framework
 *
 * @version $Revision$
 * @license GPLv3
 */

/**
 * Tests for the SQLite cache meta data handler
 */
class vcsGitCliDirectoryTests extends vcsTestCase
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
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );

        $dir = new vcsGitCliDirectory( $this->tempDir, '/' );

        $files = array();
        foreach ( $dir as $file )
        {
            $files[] = (string) $file;
        }
        sort( $files );

        $this->assertEquals(
            array(
                '/dir1/',
                '/dir2/',
                '/file'
            ),
            $files
        );
    }

    public function testRecursiveIterator()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );

        $dir      = new vcsGitCliDirectory( $this->tempDir, '/' );
        $iterator = new RecursiveIteratorIterator( $dir, RecursiveIteratorIterator::SELF_FIRST );

        $files = array();
        foreach ( $iterator as $file )
        {
            $files[] = (string) $file;
        }
        sort( $files );

        $this->assertEquals(
            array(
                '/dir1/',
                '/dir1/file',
                '/dir2/',
                '/dir2/file',
                '/file'
            ),
            $files
        );
    }

    public function testIterateSubDirContents()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );

        $dir = new vcsGitCliDirectory( $this->tempDir, '/dir1/' );

        $files = array();
        foreach ( $dir as $file )
        {
            $files[] = (string) $file;
        }

        $this->assertEquals(
            array(
                '/dir1/file'
            ),
            $files
        );
    }
}

