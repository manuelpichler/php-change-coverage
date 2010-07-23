<?php
/**
 * PHP VCS wrapper SQLite metadata cache metadata handler
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
 * SQLite Cache metadata handler.
 */
class vcsCacheSqliteMetaData extends vcsCacheMetaData
{
    /**
     * Database connection
     * 
     * @var SQLite3
     */
    protected $db;

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
        if ( !file_exists( $dbFile = ( $this->root . '/metadata.db' ) ) )
        {
            // Create cache directory, if not yet existing
            if ( !is_dir( $this->root ) )
            {
                mkdir( $this->root, 0750, true );
            }

            // Create the table with appropriate indexes, if the table does not
            // yet exist
            $db = new SQLite3( $dbFile );
            $db->query( 'CREATE TABLE metadata (
                path TEXT PRIMARY KEY,
                size NUMERIC,
                accessed NUMERIC
            )' );
            $db->query( 'CREATE INDEX size ON metadata ( size )' );
            $db->query( 'CREATE INDEX accessed ON metadata ( accessed )' );
        }

        $this->db = new SQLite3( $dbFile );
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

        // Insert new cached item into meta data storage
        $query = $this->db->prepare( 'REPLACE INTO metadata ( path, size, accessed ) VALUES ( :path, :size, :accessed )' );
        $query->bindValue( ':path',     $path );
        $query->bindValue( ':size',     $size );
        $query->bindValue( ':accessed', $time );
        $query->execute();
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

        // Insert new cached item into meta data storage
        $query = $this->db->prepare( 'UPDATE metadata SET accessed = :accessed WHERE path = :path' );
        $query->bindValue( ':path',     $path );
        $query->bindValue( ':accessed', $time );
        $query->execute();
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
        // Check if overall cache size exceeds cache limit
        $result = $this->db->query( 'SELECT SUM( size ) as size FROM metadata' );
        $cacheSize = $result->fetchArray( SQLITE3_NUM );
        $cacheSize = $cacheSize[0];
        $result->finalize();

        if ( $cacheSize <= $size )
        {
            // Cache size does not exceed cache value, so we can exit
            // immediately.
            return false;
        }

        // Otherwise clear cache values, until we pass the lower size border
        $maxSize = $size * $rate;
        $result  = $this->db->query( 'SELECT path, size FROM metadata ORDER BY accessed ASC' );
        $removed = array();
        do {
            $row = $result->fetchArray( SQLITE3_ASSOC );
            $cacheSize -= $row['size'];
            unlink( $this->root . ( $removed[] = $row['path'] ) );
        } while ( $cacheSize > $maxSize );
        $result->finalize();

        // Remove entries from database
        foreach ( $removed as $nr => $value )
        {
            $removed[$nr] = "'" . $this->db->escapeString( $value ) . "'";
        }
        $this->db->query( 'DELETE FROM metadata WHERE path IN ( ' . implode( ', ', $removed ) . ' )' );
    }
}

