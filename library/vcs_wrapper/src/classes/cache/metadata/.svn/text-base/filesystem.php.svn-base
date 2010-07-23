<?php
/**
 * PHP VCS wrapper file system metadata cache metadata handler
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
 * file system Cache metadata handler.
 */
class vcsCacheFileSystemMetaData extends vcsCacheMetaData
{
    /**
     * Filename of file, which stores the overall cache size.
     *
     * @var string
     */
    protected $storage;

    /**
     * Construct cache from cache storage root
     *
     * @param string $root
     * @return void
     */
    public function __construct( $root )
    {
        parent::__construct( $root );

        // Check for existance of meta data storage file
        if ( !file_exists( $storage = ( $this->root . '/cacheSize' ) ) )
        {
            file_put_contents( $storage, '0' );
        }

        $this->storage = $storage;
    }

    /**
     * A cache file has been
     *
     * Method called, when a cache file has been created. The size can be used
     * to estimate the overall cache size information.
     *
     * If the cleanup() method is cheap in runtime for the cache meta data
     * handler, this method may call the cleanup on every write, or for a
     * meaningful percentage of writes. The cleanup() method will otherwise
     * also be called from outside.
     *
     * @param string $path
     * @param int $size
     * @param int $time
     * @return void
     */
    public function created( $path, $size, $time = null )
    {
        // If no time has been given, default to current time.
        $time = ( $time === null ) ? time() : $time;

        // Update acces time of file
        touch( $this->root . $path, $time );

        // Store additional file size.
        //
        // There may be edit conflicts, but those should be minor and seldom,
        // so we just ignore them. :)
        file_put_contents( $this->storage, file_get_contents( $this->storage ) + $size );
    }

    /**
     * A cache file has been accessed
     *
     * Method call, when a cache file has been read. This method ist used to
     * basically update the LRU information of cache entries.
     *
     * @param string $path
     * @param int $time
     * @return void
     */
    public function accessed( $path, $time = null )
    {
        // If no time has been given, default to current time.
        $time = ( $time === null ) ? time() : $time;

        // Update acces time of file
        touch( $this->root . $path, $time );
    }

    /**
     * Cleanup cache
     *
     * Check if the current cache size exceeds the given requested cache size.
     * If this is the case purge all cache items from the cache until the cache
     * is only filled up to $rate percentage.
     *
     * @param int $size
     * @param flaot $rate
     * @return void
     */
    public function cleanup( $size, $rate )
    {
        // Check if cache size exceeds cache limit
        if ( ( $cacheSize = (int) file_get_contents( $this->storage ) ) <= $size )
        {
            return false;
        }

        // Cache size exceeds limit, so we build a sorted list of all files in
        // the cache - may take quite some time.
        clearstatcache();
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $this->root, RecursiveDirectoryIterator::CURRENT_AS_FILEINFO )
        );

        $ctimes   = array();
        $filedata = array();
        foreach ( $iterator as $file )
        {
            if ( $file->isFile() &&
                 ( $file->getFilename() !== basename( $this->storage ) ) )
            {
                $ctimes[]   = $file->getCTime();
                $filedata[] = array( realpath( $file->getPathname() ), $file->getSize() );
            }
        }

        // Sort the file data depending on the ctimes
        array_multisort(
            $ctimes, SORT_NUMERIC, SORT_ASC,
            $filedata
        );

        // Remove files until we reached the maximum cache size
        $maxSize = $size * $rate;
        $reduced = 0;
        foreach ( $filedata as $file )
        {
            $reduced += $file[1];
            unlink( $file[0] );

            // Abort deletion loop, if we reached the lower border
            if ( ( $cacheSize - $reduced ) < $maxSize )
            {
                break;
            }
        }

        // Write new cache size to storage file
        file_put_contents( $this->storage, file_get_contents( $this->storage ) - $reduced );
    }
}

