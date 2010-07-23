<?php
/**
 * Basic test cases for framework
 *
 * @version $Revision: 955 $
 * @license GPLv3
 */

/**
 * @group mercurial
 * Tests for the SQLite cache meta data handler
 */
class vcsHgCliFileTests extends vcsTestCase
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
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        $this->assertSame(
            "b8ec741c8de1e60c5fedd98c350e3569c46ed630",
            $file->getVersionString()
        );
    }

    public function testGetVersions()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        $this->assertSame(
            array(
                "9923e3bfe735ad54d67c38351400097e25aadabd",
                "b8ec741c8de1e60c5fedd98c350e3569c46ed630",
            ),
            $file->getVersions()
        );
    }

    public function testGetAuthor()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            't.tom',
            $file->getAuthor()
        );
    }

    public function testGetAuthorOldVersion()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            't.tom',
            $file->getAuthor( 'b8ec741c8de1e60c5fedd98c350e3569c46ed630' )
        );
    }

    public function testGetAuthorInvalidVersion()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        try {
            $file->getAuthor( 'invalid' );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetLog()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            array(
                '9923e3bfe735ad54d67c38351400097e25aadabd' => new vcsLogEntry(
                    "9923e3bfe735ad54d67c38351400097e25aadabd", "t.tom", "- Added a first test file", 1263330480
                ),
                'b8ec741c8de1e60c5fedd98c350e3569c46ed630' => new vcsLogEntry(
                    "b8ec741c8de1e60c5fedd98c350e3569c46ed630", "t.tom", "- Modified file", 1263330660
                ),
            ),
            $file->getLog()
        );
    }

    public function testGetLogEntry()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            new vcsLogEntry(
                    "b8ec741c8de1e60c5fedd98c350e3569c46ed630", "t.tom", "- Modified file", 1263330660
            ),
            $file->getLogEntry( "b8ec741c8de1e60c5fedd98c350e3569c46ed630" )
        );
    }

    public function testGetUnknownLogEntry()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        try {
            $file->getLogEntry( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetFileContents()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "Some other test file\n",
            $file->getContents()
        );
    }

    public function testGetFileMimeType()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "application/octet-stream",
            $file->getMimeType()
        );
    }

    public function testGetFileBlame()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            array(
                new vcsBlameStruct(
                    'Some test file',
                    '9923e3bfe735ad54d67c38351400097e25aadabd',
                    't.tom',
                    1263330521
                ),
                new vcsBlameStruct(
                    'Another line in the file',
                    'b8ec741c8de1e60c5fedd98c350e3569c46ed630',
                    't.tom',
                    1263330677
                ),
            ),
            $file->blame()
        );
    }

    public function testGetFileBlameInvalidVersion()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        try {
            $file->blame( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetFileDiff()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        $diff = $file->getDiff( "9923e3bfe735ad54d67c38351400097e25aadabd" );
        
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
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $file = new vcsHgCliFile( $this->tempDir, '/file' );

        try {
            $diff = $file->getDiff( "1" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }
}

