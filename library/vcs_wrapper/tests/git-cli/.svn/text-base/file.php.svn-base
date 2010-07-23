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
class vcsGitCliFileTests extends vcsTestCase
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

    public function testGetVersionString()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        $this->assertSame(
            "2037a8d0efd4e51a4dd84161837f8865cf7d34b1",
            $file->getVersionString()
        );
    }

    public function testGetVersions()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        $this->assertSame(
            array(
                "43fb423f4ee079af2f3cba4e07eb8b10f4476815",
                "2037a8d0efd4e51a4dd84161837f8865cf7d34b1",
            ),
            $file->getVersions()
        );
    }

    public function testGetAuthor()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            'kore',
            $file->getAuthor()
        );
    }

    public function testGetAuthorOldVersion()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            'kore',
            $file->getAuthor( '2037a8d0efd4e51a4dd84161837f8865cf7d34b1' )
        );
    }

    public function testGetAuthorInvalidVersion()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        try {
            $file->getAuthor( 'invalid' );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetLog()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            array(
                '43fb423f4ee079af2f3cba4e07eb8b10f4476815' => new vcsLogEntry(
                    "43fb423f4ee079af2f3cba4e07eb8b10f4476815", "kore", "- Added a first test file\n", 1226920616
                ),
                '2037a8d0efd4e51a4dd84161837f8865cf7d34b1' => new vcsLogEntry(
                    "2037a8d0efd4e51a4dd84161837f8865cf7d34b1", "kore", "- Modified file\n", 1226921232
                ),
            ),
            $file->getLog()
        );
    }

    public function testGetLogEntry()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            new vcsLogEntry(
                    "2037a8d0efd4e51a4dd84161837f8865cf7d34b1", "kore", "- Modified file\n", 1226921232
            ),
            $file->getLogEntry( "2037a8d0efd4e51a4dd84161837f8865cf7d34b1" )
        );
    }

    public function testGetUnknownLogEntry()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        try {
            $file->getLogEntry( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetFileContents()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "Some other test file\n",
            $file->getContents()
        );
    }

    public function testGetFileMimeType()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "application/octet-stream",
            $file->getMimeType()
        );
    }

    public function testGetFileBlame()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            array(
                new vcsBlameStruct(
                    'Some test file',
                    '43fb423f4ee079af2f3cba4e07eb8b10f447681',
                    'kore',
                    1226920616
                ),
                new vcsBlameStruct(
                    'Another line in the file',
                    '2037a8d0efd4e51a4dd84161837f8865cf7d34b1',
                    'kore',
                    1226921232
                ),
            ),
            $file->blame()
        );
    }

    public function testGetFileBlameInvalidVersion()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        try {
            $file->blame( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetFileDiff()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        $diff = $file->getDiff( "43fb423f4ee079af2f3cba4e07eb8b10f4476815" );
        
        $this->assertEquals(
            array(
                new vcsDiffChunk(
                    1, 1, 1, 2,
                    array(
                        new vcsDiffLine( 3, 'Some test file' ),
                        new vcsDiffLine( 1, 'Another line in the file' ),
                    )
                ),
            ),
            $diff[0]->chunks
        );
    }

    public function testGetFileDiffUnknownRevision()
    {
        $repository = new vcsGitCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/git' ) );
        $file = new vcsGitCliFile( $this->tempDir, '/file' );

        try {
            $diff = $file->getDiff( "1" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }
}

