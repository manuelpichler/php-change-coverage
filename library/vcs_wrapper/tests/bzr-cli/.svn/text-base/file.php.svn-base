<?php
/**
 * Basic test cases for framework
 *
 * @version $Revision$
 * @license GPLv3
 */

/**
 * @group bazaar
 * Tests for the SQLite cache meta data handler
 */
class vcsBzrCliFileTests extends vcsTestCase
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
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        $this->assertSame(
            "2",
            $file->getVersionString()
        );
    }

    public function testGetVersions()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        $this->assertSame(
            array(
                "1",
                "2",
            ),
            $file->getVersions()
        );
    }

    public function testGetAuthor()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            'Richard Bateman <taxilian@gmail.com>',
            $file->getAuthor()
        );
    }

    public function testGetAuthorOldVersion()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            'richard <richard@shaoden>',
            $file->getAuthor( '1' )
        );
    }

    public function testGetAuthorInvalidVersion()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        try {
            $file->getAuthor( 'invalid' );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetLog()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        $log = $file->getLog();
        
        $this->assertEquals(
            array(
                "1" => new vcsLogEntry(
                    "1", "richard <richard@shaoden>", "Initial commit", 1276559935
                    ),
                "2" => new vcsLogEntry(
                    "2", "Richard Bateman <taxilian@gmail.com>", "Second commit", 1276563712
                    ),
            ),
            $file->getLog()
        );
    }

    public function testGetLogEntry()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            new vcsLogEntry(
                    "1", "richard <richard@shaoden>", "Initial commit", 1276559935
            ),
            $file->getLogEntry( "1" )
        );
    }

    public function testGetUnknownLogEntry()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        try {
            $file->getLogEntry( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetFileContents()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "Some other test file\n",
            $file->getContents()
        );
    }

    public function testGetFileMimeType()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "application/octet-stream",
            $file->getMimeType()
        );
    }

    public function testGetFileBlame()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        $this->assertEquals(
            array(
                new vcsBlameStruct(
                    'Some test file',
                    "1",
                    'richard@shaoden',
                    1276495200
                ),
                new vcsBlameStruct(
                    'Another line in the file',
                    "1",
                    'richard@shaoden',
                    1276495200
                ),
                new vcsBlameStruct(
                    "Added a new line",
                    "2",
                    "taxilian@gmail.com",
                    1276495200
                ),
            ),
            $file->blame()
        );
    }

    public function testGetFileBlameInvalidVersion()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        try {
            $file->blame( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testGetFileDiff()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        $diff = $file->getDiff( "1", "2" );
        
        $this->assertEquals(
            array(
                new vcsDiffChunk(
                    1, 2, 1, 3,
                    array(
                        new vcsDiffLine( 3, 'Some test file' ),
                        new vcsDiffLine( 3, "Another line in the file" ),
                        new vcsDiffLine( 1, 'Added a new line' ),
                    )
                ),
            ),
            $diff[0]->chunks
        );
    }

    public function testGetFileDiffUnknownRevision()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $file = new vcsBzrCliFile( $this->tempDir, '/file' );

        try {
            $diff = $file->getDiff( "8" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }
}

