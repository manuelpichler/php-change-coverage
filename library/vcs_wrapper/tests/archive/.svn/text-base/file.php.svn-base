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
class vcsArchiveFileTests extends vcsTestCase
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
        if ( !class_exists( 'ZipArchive' ) )
        {
            $this->markTestSkipped( 'Compile PHP with --enable-zip to get support for zip archive handling.' );
        }

        parent::setUp();

        // Create a cache, required for all VCS wrappers to store metadata
        // information
        vcsCache::initialize( $this->createTempDir() );
    }

    public function testGetFileContents()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );
        $file = new vcsArchiveFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "Some test contents\n",
            $file->getContents()
        );
    }

    public function testGetFileMimeType()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );
        $file = new vcsArchiveFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            "application/octet-stream",
            $file->getMimeType()
        );
    }

    public function testGetLocalFilePath()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );
        $file = new vcsArchiveFile( $this->tempDir, '/dir1/file' );

        $this->assertEquals(
            $this->tempDir . '/dir1/file',
            $file->getLocalPath()
        );
    }
}

