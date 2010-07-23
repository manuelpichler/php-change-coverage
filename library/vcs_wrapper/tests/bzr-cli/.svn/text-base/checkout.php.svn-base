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
class vcsBzrCliCheckoutTests extends vcsTestCase
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

    public function testInitializeInvalidCheckout()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );

        try
        {
            $repository->initialize( 'file:///hopefully/not/existing/bzr/repo' );
            $this->fail( 'Expected pbsSystemProcessNonZeroExitCodeException.' );
        } catch ( pbsSystemProcessNonZeroExitCodeException $e )
        { /* Expected */ }

    }

    public function testInitializeCheckout()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        $this->assertTrue(
            file_exists( $this->tempDir . '/file' ),
            'Expected file "/file" in checkout.'
        );
    }

    public function testUpdateCheckout()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        $this->assertFalse( $repository->update(), "Repository should already be on latest revision." );

        $this->assertTrue(
            file_exists( $this->tempDir . '/file' ),
            'Expected file "/file" in checkout.'
        );
    }

    public function testUpdateCheckoutWithUpdate()
    {
        $repDir = $this->createTempDir() . '/bzr';
        self::copyRecursive( realpath( dirname( __FILE__ ) . '/../data/bzr' ), $repDir );

        // Copy the repository to not change the test reference repository
        $checkin = new vcsBzrCliCheckout( $this->tempDir . '/ci' );
        $checkin->initialize( 'file://' . $repDir );

        $checkout = new vcsBzrCliCheckout( $this->tempDir . '/co' );
        $checkout->initialize( 'file://' . $repDir );

        // Manually execute update in repository
        file_put_contents( $this->tempDir . '/ci/another', 'Some test contents' );
        $bzr = new vcsBzrCliProcess();
        $bzr->workingDirectory( $this->tempDir . '/ci' );
        $bzr->argument( 'add' )->argument( 'another' )->execute();

        $bzr = new vcsBzrCliProcess();
        $bzr->workingDirectory( $this->tempDir . '/ci' );
        $bzr->argument( 'commit' )->argument( 'another' )->argument( '-m' )->argument( 'Test commit.' )->execute();

        $this->assertTrue( $checkin->update(), "Checkin repository should have had an update available." );

        $this->assertFileNotExists( $this->tempDir . '/co/another' );
        $this->assertTrue( $checkout->update(), "Checkout repository should have had an update available." );
        $this->assertFileExists( $this->tempDir . '/co/another' );
    }

    public function testGetVersionString()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        $this->assertSame(
            "2",
            $repository->getVersionString()
        );
    }

    public function testGetVersions()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        $this->assertSame(
            array(
                "1",
                "2",
            ),
            $repository->getVersions()
        );
    }

    public function testUpdateCheckoutToOldVersion()
    {
#        $this->markTestSkipped( 'Downgrade seems not to remove files from checkout.' );

        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );
        $this->assertTrue(
            file_exists( $this->tempDir . '/dir1/file' ),
            'Expected file "/dir1/file" in checkout.'
        );

        $repository->update( "0" );

        $this->assertFalse(
            file_exists( $this->tempDir . '/dir1/file' ),
            'Expected file "/dir1/file" not in checkout.'
        );
    }

    public function testGetAuthor()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        $this->assertEquals(
            'Richard Bateman <taxilian@gmail.com>',
            $repository->getAuthor()
        );
    }

    public function testGetLog()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        $this->assertEquals(
            array(
                "1" => new vcsLogEntry(
                    "1", "richard <richard@shaoden>", "Initial commit", 1276559935
                    ),
                "2" => new vcsLogEntry(
                    "2", "Richard Bateman <taxilian@gmail.com>", "Second commit", 1276563712
                    ),
            ),
            $repository->getLog()
        );
    }

    public function testGetLogEntry()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        $this->assertEquals(
            new vcsLogEntry(
                "1", "richard <richard@shaoden>", "Initial commit", 1276559935
            ),
            $repository->getLogEntry( "1" )
        );
    }

    public function testGetUnknownLogEntry()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        try {
            $repository->getLogEntry( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testIterateCheckoutContents()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

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
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

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
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

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
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        $this->assertEquals(
            $repository->get( '/dir1' ),
            new vcsBzrCliDirectory( $this->tempDir, '/dir1' )
        );
    }

    public function testGetFile()
    {
        $repository = new vcsBzrCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/bzr' ) );

        $this->assertEquals(
            $repository->get( '/file' ),
            new vcsBzrCliFile( $this->tempDir, '/file' )
        );
    }
}

