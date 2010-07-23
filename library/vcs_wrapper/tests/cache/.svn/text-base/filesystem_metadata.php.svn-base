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
class vcsFileSystemCacheMetaDataTests extends vcsTestCase
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

    public function testStoreCreationDate()
    {
        $cacheMetaData = new vcsCacheFileSystemMetaData( $this->tempDir );

        file_put_contents( $this->tempDir . ( $path = '/foo' ), '0123456789' );
        $cacheMetaData->created( $path, 10 );
    }

    public function testReCreateCacheEntry()
    {
        $cacheMetaData = new vcsCacheFileSystemMetaData( $this->tempDir );

        file_put_contents( $this->tempDir . ( $path = '/foo' ), '0123456789' );
        $cacheMetaData->created( $path, 123 );
        $cacheMetaData->created( $path, 123 );
    }

    public function testUpdateAccessTime()
    {
        $cacheMetaData = new vcsCacheFileSystemMetaData( $this->tempDir );

        file_put_contents( $this->tempDir . ( $path = '/foo' ), '0123456789' );
        $cacheMetaData->created( $path, 10 );
        $cacheMetaData->accessed( $path );
    }

    public function testClearCache()
    {
        $cacheMetaData = new vcsCacheFileSystemMetaData( $this->tempDir );

        file_put_contents( $this->tempDir . ( $path = '/foo' ), '0123456789' );
        $cacheMetaData->created( $path, 10 );
        $cacheMetaData->accessed( $path );
        $cacheMetaData->cleanup( 0, 0. );

        $this->assertFalse(
            file_exists( $this->tempDir . $path ),
            'Cache file should have been purged'
        );
    }

    public function testClearOnlyFirstFile()
    {
        $cacheMetaData = new vcsCacheFileSystemMetaData( $this->tempDir );

        file_put_contents( $this->tempDir . ( $path1 = '/foo1' ), '0123456789' );
        $cacheMetaData->created( $path1, 10, 1 );
        sleep( 1 );
        file_put_contents( $this->tempDir . ( $path2 = '/foo2' ), '0123456789' );
        $cacheMetaData->created( $path2, 10, 2 );
        sleep( 1 );
        file_put_contents( $this->tempDir . ( $path3 = '/foo3' ), '0123456789' );
        $cacheMetaData->created( $path3, 10, 3 );

        $cacheMetaData->cleanup( 25, 1. );

        $this->assertFalse(
            file_exists( $this->tempDir . $path1 ),
            'Cache file 1 should have been purged'
        );

        $this->assertTrue(
            file_exists( $this->tempDir . $path2 ),
            'Cache file 2 should not have been purged'
        );

        $this->assertTrue(
            file_exists( $this->tempDir . $path3 ),
            'Cache file 3 should not have been purged'
        );
    }

    public function testUpdateAccessTimePurge()
    {
        $cacheMetaData = new vcsCacheFileSystemMetaData( $this->tempDir );

        file_put_contents( $this->tempDir . ( $path1 = '/foo1' ), '0123456789' );
        $cacheMetaData->created( $path1, 10, 1 );
        file_put_contents( $this->tempDir . ( $path2 = '/foo2' ), '0123456789' );
        $cacheMetaData->created( $path2, 10, 2 );
        file_put_contents( $this->tempDir . ( $path3 = '/foo3' ), '0123456789' );
        $cacheMetaData->created( $path3, 10, 3 );

        // Sleep one second to ensure a different ctime on systems, which do
        // not support chaning the ctime with touch
        sleep( 1 );
        $cacheMetaData->accessed( $path1, 4 );

        $cacheMetaData->cleanup( 25, 1. );

        $this->assertTrue(
            file_exists( $this->tempDir . $path1 ),
            'Cache file 1 should not have been purged'
        );

        $this->assertFalse(
            file_exists( $this->tempDir . $path2 ),
            'Cache file 2 should have been purged'
        );

        $this->assertTrue(
            file_exists( $this->tempDir . $path3 ),
            'Cache file 3 should not have been purged'
        );
    }
}

