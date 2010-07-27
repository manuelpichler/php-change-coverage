<?php
/**
 * PHP VCS wrapper archive directory wrapper
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
 * @subpackage ArchiveWrapper
 * @version $Revision: 1859 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Directory implementation for archive wrapper
 *
 * @package VCSWrapper
 * @subpackage ArchiveWrapper
 * @version $Revision: 1859 $
 */
class vcsArchiveDirectory extends vcsArchiveResource implements vcsDirectory
{
    /**
     * Array with children resources of the directory, used for the iterator.
     * 
     * @var array
     */
    protected $resources = null;

    /**
     * Initialize resources array
     * 
     * Initilaize the array containing all child elements of the current
     * directly as vcsArchiveResource objects.
     * 
     * @return array(vcsArchiveResource)
     */
    protected function initializeResouces()
    {
        $this->resources = array();

        // Build resources array, without constructing the objects yet, for
        // lazy construction of the object tree.
        $contents = dir( $this->root . $this->path );
        while ( ( $path = $contents->read() ) !== false )
        {
            if ( ( $path === '.' ) ||
                 ( $path === '..' ) )
            {
                continue;
            }
    
            $this->resources[] = ( is_dir( $this->root . $this->path . $path ) ?
                new vcsArchiveDirectory( $this->root, $this->path . $path . '/' ) :
                new vcsArchiveFile( $this->root, $this->path . $path )
            );
        }
        $contents->close();
    }

    /**
     * Return the current element
     *
     * @return mixed
     */
    public function current()
    {
        if ( $this->resources === null )
        {
            $this->initializeResouces();
        }

        return current( $this->resources );
    }

    /**
     * Move forward to next element
     *
     * @return mixed
     */
    public function next()
    {
        if ( $this->resources === null )
        {
            $this->initializeResouces();
        }

        return next( $this->resources );
    }

    /**
     * Return the key of the current element
     *
     * @return mixed
     */
    public function key()
    {
        if ( $this->resources === null )
        {
            $this->initializeResouces();
        }

        return key( $this->resources );
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        if ( $this->resources === null )
        {
            $this->initializeResouces();
        }

        return $this->current() !== false;
    }
    
    /**
     * Rewind the Iterator to the first element
     *
     * @return mixed
     */
    public function rewind()
    {
        if ( $this->resources === null )
        {
            $this->initializeResouces();
        }

        return reset( $this->resources );
    }

    /**
     * Returns an iterator for the current entry.
     *
     * @return Iterator
     */
    public function getChildren()
    {
        if ( $this->resources === null )
        {
            $this->initializeResouces();
        }

        return current( $this->resources );
    }
    
    /**
     * Returns if an iterator can be created fot the current entry.
     *
     * @return bool
     */
    public function hasChildren()
    {
        if ( $this->resources === null )
        {
            $this->initializeResouces();
        }

        return current( $this->resources ) instanceof vcsDirectory;
    }
}

