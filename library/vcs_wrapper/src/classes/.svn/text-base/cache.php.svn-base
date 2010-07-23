<?php
/**
 * PHP VCS wrapper base metadata cache handler
 *
 * This file is part of vcs-wrapper.
 *
 * vcs-wrapper is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * vcs-wrapper is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with vcs-wrapper; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package VCSWrapper
 * @subpackage Cache
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Cache handler for VCS meta data
 *
 * Basic LRU (last recently used) cache, with a storage size limitation,
 * because the amount of diskspace used for all meta data information in a big
 * repository might be very space consuming.
 *
 * The size and access information is stored in a SQLite database, if
 * available, and directly in the file system otherwise.
 *
 * The filesystem is very slow to collect the oldest files, so the cleanup will
 * not happen automatically using the file system cache meta data storage, but
 * has to be triggered manually (cron or something). With the SQLite cache
 * metadata storage this will happen automatically.
 */
class vcsCache
{
    /**
     * Cache path, used to store the actual cache contents
     *
     * @var string
     */
    protected static $path = null;

    /**
     * Cache size in bytes. A cache size lower or equal 0 is used to
     * intentionally disable the cache.
     *
     * @var int
     */
    protected static $size = null;

    /**
     * Cache cleanup rate.
     *
     * The clean up rate defines how much of the cache contents will be left on
     * the device, when the cache size limit has been exceeded. The default
     * rate of .8 with a cache size of 1 MBdefines, that everything except the
     * most recently used 800 kB will be purged.
     *
     * @var float
     */
    protected static $cleanupRate = null;

    /**
     * Cache meta data handler
     *
     * Handler to store the cache meta data, like file access time and overall
     * storage volumne.
     *
     * @var vcsCacheMetaData
     */
    protected static $metaDataHandler = null;

    /**
     * Private constructor
     *
     * The cache is only accessed statically and should be configured using the
     * static initialize method. Therefore this constructor is protected to not
     * be called from the outside.
     *
     * @return void
     */
    protected function __construct()
    {
        // Empty, just preventing from construction.
    }

    /**
     * Initialize cache
     *
     * Initialize cache with its settings. You need to provide a path to a
     * location where the cache may store its contents.
     *
     * Optionally you may pass a different cache size limit, which defaults to
     * 1MB in bytes, and a cleanup rate. The clean up rate defines how much of
     * the cache contents will be left on the device, when the cache size limit
     * has been exceeded. The default rate of .8 with a cache size of 1
     * MBdefines, that everything except the most recently used 800 kB will be
     * purged.
     *
     * @param string $path 
     * @param int $size 
     * @param float $cleanupRate 
     * @return void
     */
    public static function initialize( $path, $size = 1048576, $cleanupRate = .8 )
    {
        self::$path        = (string) $path;
        self::$size        = (int)    $size;
        self::$cleanupRate = (float)  $cleanupRate;

        // Determine meta data handler to use for caching the cache metadata.
        if ( false && extension_loaded( 'sqlite3' ) )
        {
            // SQLite metadata cache handler disabled for now, since it has 
            // lock issues.
            self::$metaDataHandler = new vcsCacheSqliteMetaData( self::$path );
        }
        else
        {
            self::$metaDataHandler = new vcsCacheFileSystemMetaData( self::$path );
        }
    }

    /**
     * Get cache file file name
     *
     * Create the file name for the cache item based on the three cache item
     * characteristica.
     * 
     * @param string $resource 
     * @param string $version 
     * @param string $key 
     * @return string
     */
    protected static function getFileName( $resource, $version, $key )
    {
        return "/$version/$resource/$key.cache";
    }

    /**
     * Get value from cache
     *
     * Get the metadata, identified by the $key from the cache, for the given
     * resource in the given version.
     *
     * This method returns false, if the item does not yet exist in the cache,
     * and the cached value otherwise.
     *
     * @param string $resource 
     * @param string $version 
     * @param string $key 
     * @return mixed
     */
    public static function get( $resource, $version, $key )
    {
        if ( self::$path === null )
        {
            throw new vcsCacheNotInitializedException();
        }

        $cacheFile = self::getFileName( $resource, $version, $key );
        if ( !is_file( self::$path . $cacheFile ) )
        {
            return false;
        }

        self::$metaDataHandler->accessed( $cacheFile );
        return include self::$path . $cacheFile;
    }

    /**
     * Cache item
     *
     * Cache the meta data, identified by the $key, for the given resource in
     * the given version. You may cache all scalar values, arrays and objects
     * which are implementing the interface arbitCacheable.
     *
     * @param string $resource 
     * @param string $version 
     * @param string $key 
     * @param mixed $value 
     * @return void
     */
    public static function cache( $resource, $version, $key, $value )
    {
        if ( self::$path === null )
        {
            throw new vcsCacheNotInitializedException();
        }

        if ( !is_scalar( $value ) &&
             !is_array( $value ) &&
             ( !$value instanceof arbitCacheable ) )
        {
            throw new vcsNotCacheableException( $value );
        }

        $cacheFile = self::getFileName( $resource, $version, $key );

        $cacheFileDir = dirname( self::$path . $cacheFile );
        if ( !is_dir( $cacheFileDir ) )
        {
            mkdir( $cacheFileDir, 0770, true );
        }

        file_put_contents( self::$path . $cacheFile, sprintf( "<?php\n\nreturn %s;\n\n", var_export( $value, true ) ) );
        self::$metaDataHandler->created( $cacheFile, filesize( self::$path . $cacheFile ) );
    }

    /**
     * Force cache cleanup
     *
     * Force a check, if the cache currently exceeds its given size limit. If
     * this is the case this method will start cleaning up the cache.
     *
     * Depending on the used meta data storage and the size of the cache this
     * operation might take some time.
     *
     * @return void
     */
    public static function forceCleanup()
    {
        if ( self::$path === null )
        {
            throw new vcsCacheNotInitializedException();
        }

        self::$metaDataHandler->cleanup( self::$size, self::$cleanupRate );
    }
}

