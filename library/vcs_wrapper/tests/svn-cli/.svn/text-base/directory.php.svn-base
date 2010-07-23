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
class vcsSvnCliDirectoryTests extends vcsTestCase
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
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );

        $dir = new vcsSvnCliDirectory( $this->tempDir, '/' );

        $files = array();
        foreach ( $dir as $file )
        {
            $files[] = (string) $file;
        }
        sort( $files );

        $this->assertEquals(
            array(
                '/binary',
                '/dir1/',
                '/dir2/',
                '/file'
            ),
            $files
        );
    }

    public function testRecursiveIterator()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );

        $dir      = new vcsSvnCliDirectory( $this->tempDir, '/' );
        $iterator = new RecursiveIteratorIterator( $dir, RecursiveIteratorIterator::SELF_FIRST );

        $files = array();
        foreach ( $iterator as $file )
        {
            $files[] = (string) $file;
        }
        sort( $files );

        $this->assertEquals(
            array(
                '/binary',
                '/dir1/',
                '/dir1/file',
                '/dir2/',
                '/file'
            ),
            $files
        );
    }

    public function testIterateSubDirContents()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );

        $dir = new vcsSvnCliDirectory( $this->tempDir, '/dir1/' );

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

    public function testGetDirectoryDiff()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $dir = new vcsSvnCliDirectory( $this->tempDir, '/dir1/' );

        $diff = $dir->getDiff( 2 );

        $this->assertEquals(
            '/dir1/file',
            $diff[0]->from
        );
        $this->assertEquals(
            '/dir1/file',
            $diff[0]->to
        );
        $this->assertEquals(
            array(
                new vcsDiffChunk(
                    0, 1, 1, 1,
                    array(
                        new vcsDiffLine( 1, 'Some test contents' ),
                    )
                ),
            ),
            $diff[0]->chunks
        );
    }
}

