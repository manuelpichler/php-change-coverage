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
class vcsSvnCliFileTests extends vcsTestCase
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
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        $this->assertSame(
            "5",
            $file->getVersionString()
        );
    }

    public function testGetVersions()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        $this->assertSame(
            array( "1", "5" ),
            $file->getVersions()
        );
    }

    public function testGetAuthor()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            'kore',
            $file->getAuthor()
        );
    }

    public function testGetAuthorOldVersion()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            'kore',
            $file->getAuthor( '1' )
        );
    }

    public function testGetAuthorInvalidVersion()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        try {
            $file->getAuthor( 'invalid' );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetLog()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            array(
                1 => new vcsLogEntry(
                    '1',
                    'kore',
                    "- Added test file\n",
                    1226412609
                ),
                5 => new vcsLogEntry(
                    '5',
                    'kore',
                    "- Added another line to file\n",
                    1226595170
                ),
            ),
            $file->getLog()
        );
    }

    public function testGetLogEntry()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            new vcsLogEntry(
                '1',
                'kore',
                "- Added test file\n",
                1226412609
            ),
            $file->getLogEntry( "1" )
        );
    }

    public function testGetUnknownLogEntry()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        try {
            $file->getLogEntry( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetFileContents()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "Some test contents\n",
            $file->getContents()
        );
    }

    public function testGetFileMimeType()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "application/octet-stream",
            $file->getMimeType()
        );
    }

    public function testGetFileVersionedFileContents()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            "Some test file\n",
            $file->getVersionedContent( "1" )
        );
    }

    public function testGetFileContentsInvalidVersion()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        try {
            $file->getVersionedContent( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetFileBlame()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            array(
                new vcsBlameStruct(
                    'Some test file',
                    '1',
                    'kore',
                    1226412609
                ),
                new vcsBlameStruct(
                    'A second line, in a later revision',
                    '5',
                    'kore',
                    1226595170
                ),
            ),
            $file->blame()
        );
    }

    public function testGetBinaryFileBlame()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/binary' );

        $this->assertEquals(
            false,
            $file->blame()
        );
    }

    public function testGetFileBlameInvalidVersion()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        try {
            $file->blame( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetFileDiff()
    {
        $repository = new vcsSvnCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/svn' ) );
        $file = new vcsSvnCliFile( $this->tempDir, '/file' );

        $diff = $file->getDiff( 1 );
        

        $this->assertEquals(
            '/file',
            $diff[0]->from
        );
        $this->assertEquals(
            '/file',
            $diff[0]->to
        );
        $this->assertEquals(
            array(
                new vcsDiffChunk(
                    1, 1, 1, 2,
                    array(
                        new vcsDiffLine( 3, 'Some test file' ),
                        new vcsDiffLine( 1, 'A second line, in a later revision' ),
                    )
                ),
            ),
            $diff[0]->chunks
        );
    }
}

