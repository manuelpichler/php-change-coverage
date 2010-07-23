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
class vcsCvsCliFileTests extends vcsTestCase
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
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/file' );
        $this->assertEquals( '1.2', $file->getVersionString() );

        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file' );
        $this->assertEquals( '1.3', $file->getVersionString() );
    }

    public function testGetVersions()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/file' );
        $this->assertSame( array( '1.1', '1.2' ), $file->getVersions()  );

        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file' );
        $this->assertSame( array( '1.1', '1.2', '1.3' ), $file->getVersions()  );
    }

    public function testCompareVersions()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );
        $file = new vcsCvsCliFile( $this->tempDir, '/file' );

        $this->assertEquals( 0, $file->compareVersions( '1.1', '1.1' ) );
        $this->assertLessThan( 0, $file->compareVersions( '1.1', '1.2' ) );
        $this->assertGreaterThan( 0, $file->compareVersions( '1.3', '1.2' ) );
    }

    public function testGetAuthor()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/file' );
        $this->assertEquals( 'manu', $file->getAuthor() );
    }

    public function testGetAuthorWithVersion()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/file' );
        $this->assertEquals( 'manu', $file->getAuthor( '1.1' ) );
    }

    public function testGetAuthorWithInvalidVersion()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/file' );
        $this->setExpectedException('vcsNoSuchVersionException');
        $file->getAuthor( '1.10' );
    }

    public function testGetLog()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );
        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            array(
                '1.1' => new vcsLogEntry(
                    '1.1',
                    'manu',
                    '- Added file in subdir',
                    1227507833
                ),
                '1.2' => new vcsLogEntry(
                    '1.2',
                    'manu',
                    '- A',
                    1227804262
                ),
                '1.3' => new vcsLogEntry(
                    '1.3',
                    'manu',
                    '- Test file modified.',
                    1227804446
                ),
            ),
            $file->getLog()
        );
    }

    public function testGetLogEntry()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/file' );
        $this->assertEquals(
            new vcsLogEntry(
                '1.2',
                'manu',
                '- Added another line to file',
                1227507961
            ),
            $file->getLogEntry( '1.2' )
        );
    }

    public function testGetUnknownLogEntry()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/file' );

        $this->setExpectedException( 'vcsNoSuchVersionException' );

        $file->getLogEntry( "no_such_version" );
    }

    public function testGetFileContents()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file1' );
        $this->assertEquals( "Another test file\n", $file->getContents() );
    }

    public function testGetFileMimeType()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file1' );
        $this->assertEquals( 'application/octet-stream', $file->getMimeType() );
    }

    public function testGetFileVersionedFileContents()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file' );
        $this->assertEquals( "Some test contents\n", $file->getVersionedContent( '1.1' ) );
    }

    public function testGetFileContentsInvalidVersion()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/file' );
        $this->setExpectedException( 'vcsNoSuchVersionException' );
        $file->getVersionedContent( 'no_such_version' );
    }

    public function testGetFileBlame()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file' );
        $this->assertEquals(
            array(
                new vcsBlameStruct(
                    'Some test contents',
                    '1.1',
                    'manu',
                    1227481200
                ),
                new vcsBlameStruct(
                    'More test contents',
                    '1.2',
                    'manu',
                    1227740400
                ),
                new vcsBlameStruct(
                    'And another test line',
                    '1.3',
                    'manu',
                    1227740400
                ),
            ),
            $file->blame()
        );
    }

    public function testGetFileBlameWithVersion()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file' );
        $this->assertEquals(
            array(
                new vcsBlameStruct(
                    'Some test contents',
                    '1.1',
                    'manu',
                    1227481200
                ),
            ),
            $file->blame( '1.1' )
        );
    }

    public function testGetFileBlameWithInvalidVersion()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file' );
        $this->setExpectedException( 'vcsNoSuchVersionException' );
        $file->blame( 'no_such_version' );
    }

    public function testGetFileDiff()
    {
        $checkout = new vcsCvsCliCheckout( $this->tempDir );
        $checkout->initialize( realpath( dirname( __FILE__ ) . '/../data/cvs' ) . '#cvs' );

        $file = new vcsCvsCliFile( $this->tempDir, '/dir1/file' );
        $diff = $file->getDiff( '1.1' );

        $this->assertEquals(
            array(
                new vcsDiffChunk(
                    1, 1, 1, 3,
                    array(
                        new vcsDiffLine( 3, 'Some test contents' ),
                        new vcsDiffLine( 1, 'More test contents' ),
                        new vcsDiffLine( 1, 'And another test line' ),
                    )
                ),
            ),
            $diff[0]->chunks
        );
    }
}
