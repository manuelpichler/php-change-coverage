<?php
/**
 * PHP VCS wrapper base metadata cache metadata handler
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
 * Cache metadata handler base class
 *
 * Base class for storing cache meta data, required for a basic size limited
 * LRU cache.
 */
abstract class vcsCacheMetaData
{
    /**
     * Root of cache storage
     * 
     * @var string
     */
    protected $root;

    /**
     * Construct cache from cache storage root
     * 
     * @param string $root 
     * @return void
     */
    public function __construct( $root )
    {
        $this->root = $root;

        if ( !is_dir( $root ) )
        {
            mkdir( $root, 0777, true );
        }
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
    abstract public function created( $path, $size, $time = null );

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
    abstract public function accessed( $path, $time = null );

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
    abstract public function cleanup( $size, $rate );
}

