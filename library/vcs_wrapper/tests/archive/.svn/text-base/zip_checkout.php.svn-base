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
class vcsZipArchiveCheckoutTests extends vcsTestCase
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

    public function testInitializeInvalidCheckout()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );

        try
        {
            $repository->initialize( 'file:///hopefully/not/existing/svn/repo' );
            $this->fail( 'Expected vcsNoSuchFileException.' );
        } catch ( vcsNoSuchFileException $e )
        { /* Expected */ }

    }

    public function testInitializeInvalidArchive()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );

        try
        {
            $repository->initialize( __FILE__ );
            $this->fail( 'Expected vcsInvalidZipArchiveException.' );
        } catch ( vcsInvalidZipArchiveException $e )
        { /* Expected */ }

    }

    public function testInitializeCheckout()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );

        $this->assertTrue(
            file_exists( $this->tempDir . '/file' ),
            'Expected file "/file" in checkout.'
        );
    }

    public function testUpdateCheckout()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );

        $this->assertFalse( $repository->update(), "There are never updates available for archive checkouts." );

        $this->assertTrue(
            file_exists( $this->tempDir . '/file' ),
            'Expected file "/file" in checkout.'
        );
    }

    public function testIterateCheckoutContents()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );

        $files = array();
        foreach ( $repository as $file )
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

    public function testGetCheckout()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );

        $this->assertSame(
            $repository->get(),
            $repository
        );

        $this->assertSame(
            $repository->get( '/' ),
            $repository
        );
    }

    public function testGetInvalid()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );

        try
        {
            $repository->get( '/../' );
            $this->fail( 'Expected vcsFileNotFoundException.' );
        }
        catch ( vcsFileNotFoundException $e )
        { /* Expected */ }
    }

    public function testGetDirectory()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );

        $this->assertEquals(
            $repository->get( '/dir1' ),
            new vcsArchiveDirectory( $this->tempDir, '/dir1' )
        );
    }

    public function testGetFile()
    {
        $repository = new vcsZipArchiveCheckout( $this->tempDir );
        $repository->initialize( realpath( dirname( __FILE__ ) . '/../data/archive.zip' ) );

        $this->assertEquals(
            $repository->get( '/file' ),
            new vcsArchiveFile( $this->tempDir, '/file' )
        );
    }
}

