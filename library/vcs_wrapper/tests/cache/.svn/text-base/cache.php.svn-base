<?php
/**
 * Basic test cases for framework
 *
 * @version $Revision$
 * @license GPLv3
 */

class vcsTestCacheableObject implements arbitCacheable
{
    public $foo = null;
    public function __construct( $foo )
    {
        $this->foo = $foo;
    }
    public static function __set_state( array $properties )
    {
        return new vcsTestCacheableObject( reset( $properties ) );
    }
}

/**
 * Tests for the SQLite cache meta data handler
 */
class vcsCacheTests extends vcsTestCase
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

    public function testCacheNotInitialized()
    {
        try {
            vcsCache::get( '/foo', 1, 'data' );
            $this->fail( 'Expected vcsCacheNotInitializedException.' );
        } catch ( vcsCacheNotInitializedException $e )
        { /* Expected */ }
    }

    public function testValueNotInCache()
    {
        vcsCache::initialize( $this->tempDir, 100, .8 );
        $this->assertFalse(
            vcsCache::get( '/foo', 1, 'data' ),
            'Expected false, because item should not be in cache.'
        );
    }

    public function testCacheScalarValues()
    {
        $values = array( 1, .1, 'foo', true );
        vcsCache::initialize( $this->tempDir, 100, .8 );

        foreach ( $values as $nr => $value )
        {
            vcsCache::cache( '/foo', (string) $nr, 'data', $value );
        }

        foreach ( $values as $nr => $value )
        {
            $this->assertSame(
                $value,
                vcsCache::get( '/foo', $nr, 'data' ),
                'Wrong item returned from cache'
            );
        }
    }

    public function testCacheArray()
    {
        $values = array( 1, .1, 'foo', true );
        vcsCache::initialize( $this->tempDir, 100, .8 );
        vcsCache::cache( '/foo', '1', 'data', $values );

        $this->assertSame(
            $values,
            vcsCache::get( '/foo', '1', 'data' ),
            'Wrong item returned from cache'
        );
    }

    public function testInvalidCacheItem()
    {
        vcsCache::initialize( $this->tempDir, 100, .8 );

        try {
            vcsCache::cache( '/foo', '1', 'data', $this );
            $this->fail( 'Expected vcsNotCacheableException.' );
        }
        catch ( vcsNotCacheableException $e )
        { /* Expected */ }
    }

    public function testCacheCacheableObject()
    {
        vcsCache::initialize( $this->tempDir, 100, .8 );
        vcsCache::cache( '/foo', '1', 'data', $object = new vcsTestCacheableObject( 'foo' ) );

        $this->assertEquals(
            $object,
            vcsCache::get( '/foo', '1', 'data' ),
            'Wrong item returned from cache'
        );
    }

    public function testPurgeOldCacheEntries()
    {
        $values = array( 1, .1, 'foo', true );
        vcsCache::initialize( $this->tempDir, 50, .8 );

        foreach ( $values as $nr => $value )
        {
            vcsCache::cache( '/foo', (string) $nr, 'data', $value );
        }
        vcsCache::forceCleanup();

        $this->assertFalse(
            vcsCache::get( '/foo', 0, 'data' ),
            'Item 0 is not expected to be in the cache anymore.'
        );
        $this->assertFalse(
            vcsCache::get( '/foo', 1, 'data' ),
            'Item 1 is not expected to be in the cache anymore.'
        );
        $this->assertFalse(
            vcsCache::get( '/foo', 2, 'data' ),
            'Item 2 is not expected to be in the cache anymore.'
        );
        $this->assertTrue(
            vcsCache::get( '/foo', 3, 'data' ),
            'Item 3 is still expected to be in the cache.'
        );
    }
}

