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
class vcsHgCliCheckoutTests extends vcsTestCase
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
        $repository = new vcsHgCliCheckout( $this->tempDir );

        try
        {
            $repository->initialize( 'file:///hopefully/not/existing/hg/repo' );
            $this->fail( 'Expected pbsSystemProcessNonZeroExitCodeException.' );
        } catch ( pbsSystemProcessNonZeroExitCodeException $e )
        { /* Expected */ }

    }

    public function testInitializeCheckout()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertTrue(
            file_exists( $this->tempDir . '/file' ),
            'Expected file "/file" in checkout.'
        );
    }

    public function testUpdateCheckout()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertFalse( $repository->update(), "Repository should already be on latest revision." );

        $this->assertTrue(
            file_exists( $this->tempDir . '/file' ),
            'Expected file "/file" in checkout.'
        );
    }

    public function testUpdateCheckoutWithUpdate()
    {
        $repDir = $this->createTempDir() . '/hg';
        self::copyRecursive( realpath( dirname( __FILE__ ) . '/../data/hg' ), $repDir );

        // Copy the repository to not chnage the test reference repository
        $checkin = new vcsHgCliCheckout( $this->tempDir . '/ci' );
        $checkin->initialize( 'file://' . $repDir );

        $checkout = new vcsHgCliCheckout( $this->tempDir . '/co' );
        $checkout->initialize( 'file://' . $repDir );

        // Manually execute update in repository
        file_put_contents( $this->tempDir . '/ci/another', 'Some test contents' );
        $hg = new vcsHgCliProcess();
        $hg->workingDirectory( $this->tempDir . '/ci' );
        $hg->argument( 'add' )->argument( 'another' )->execute();

        $hg = new vcsHgCliProcess();
        $hg->workingDirectory( $this->tempDir . '/ci' );
        $hg->argument( 'commit' )->argument( 'another' )->argument( '-m' )->argument( 'Test commit.' )->execute();

        $hg = new vcsHgCliProcess();
        $hg->workingDirectory( $this->tempDir . '/ci' );
        $hg->argument( 'push' )->execute();

        $this->assertTrue( $checkin->update(), "Checkin repository should have had an update available." );

        $this->assertFileNotExists( $this->tempDir . '/co/another' );
        $this->assertTrue( $checkout->update(), "Checkout repository should have had an update available." );
        $this->assertFileExists( $this->tempDir . '/co/another' );
    }

    public function testGetVersionString()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertSame(
            "b8ec741c8de1e60c5fedd98c350e3569c46ed630",
            $repository->getVersionString()
        );
    }

    public function testGetVersions()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertSame(
            array(
                "9923e3bfe735ad54d67c38351400097e25aadabd",
                "04cae3af7ea2c880d7f70fab0583476dfc31e7ae",
                "662e49b777be9ee47ab924c02ae2da863d32536a",
                "b8ec741c8de1e60c5fedd98c350e3569c46ed630",
            ),
            $repository->getVersions()
        );
    }

    public function testUpdateCheckoutToOldVersion()
    {
#        $this->markTestSkipped( 'Downgrade seems not to remove files from checkout.' );

        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );
        $this->assertTrue(
            file_exists( $this->tempDir . '/dir1/file' ),
            'Expected file "/dir1/file" in checkout.'
        );

        $repository->update( "9923e3bfe735ad54d67c38351400097e25aadabd" );

        $this->assertFalse(
            file_exists( $this->tempDir . '/dir1/file' ),
            'Expected file "/dir1/file" not in checkout.'
        );
    }

    public function testCompareVersions()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertTrue(
            $repository->compareVersions( "04cae3af7ea2c880d7f70fab0583476dfc31e7ae", "b8ec741c8de1e60c5fedd98c350e3569c46ed630" ) < 0
        );

        $this->assertTrue(
            $repository->compareVersions( "04cae3af7ea2c880d7f70fab0583476dfc31e7ae", "04cae3af7ea2c880d7f70fab0583476dfc31e7ae" ) == 0
        );

        $this->assertTrue(
            $repository->compareVersions( "662e49b777be9ee47ab924c02ae2da863d32536a", "9923e3bfe735ad54d67c38351400097e25aadabd" ) > 0
        );
    }

    public function testGetAuthor()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertEquals(
            't.tom',
            $repository->getAuthor()
        );
    }

    public function testGetLog()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertEquals(
            array(
                "9923e3bfe735ad54d67c38351400097e25aadabd" => new vcsLogEntry(
                    "9923e3bfe735ad54d67c38351400097e25aadabd", "t.tom", "- Added a first test file", 1263330480
                ),
                "04cae3af7ea2c880d7f70fab0583476dfc31e7ae" => new vcsLogEntry(
                    "04cae3af7ea2c880d7f70fab0583476dfc31e7ae", "t.tom", "- Added some test directories", 1263330600
                ),
                "662e49b777be9ee47ab924c02ae2da863d32536a" => new vcsLogEntry(
                    "662e49b777be9ee47ab924c02ae2da863d32536a", "t.tom", "- Renamed directory", 1263330600
                ),
                "b8ec741c8de1e60c5fedd98c350e3569c46ed630" => new vcsLogEntry(
                    "b8ec741c8de1e60c5fedd98c350e3569c46ed630", "t.tom", "- Modified file", 1263330660
                ),
            ),
            $repository->getLog()
        );
    }

    public function testGetLogEntry()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertEquals(
            new vcsLogEntry(
                "662e49b777be9ee47ab924c02ae2da863d32536a", "t.tom", "- Renamed directory", 1263330600
            ),
            $repository->getLogEntry( "662e49b777be9ee47ab924c02ae2da863d32536a" )
        );
    }

    public function testGetUnknownLogEntry()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        try {
            $repository->getLogEntry( "no_such_version" );
            $this->fail( 'Expected vcsNoSuchVersionException.' );
        } catch ( vcsNoSuchVersionException $e )
        { /* Expected */ }
    }

    public function testIterateCheckoutContents()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

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
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

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
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

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
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertEquals(
            $repository->get( '/dir1' ),
            new vcsHgCliDirectory( $this->tempDir, '/dir1' )
        );
    }

    public function testGetFile()
    {
        $repository = new vcsHgCliCheckout( $this->tempDir );
        $repository->initialize( 'file://' . realpath( dirname( __FILE__ ) . '/../data/hg' ) );

        $this->assertEquals(
            $repository->get( '/file' ),
            new vcsHgCliFile( $this->tempDir, '/file' )
        );
    }
}

