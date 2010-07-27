<?php
/**
 * PHP VCS wrapper CVS Cli directory wrapper
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
 * @subpackage CvsCliWrapper
 * @version $Revision: 1859 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Directory implementation vor CVS Cli wrapper
 *
 * @package VCSWrapper
 * @subpackage CvsCliWrapper
 * @version $Revision: 1859 $
 */
class vcsCvsCliDirectory extends vcsResource implements vcsDirectory
{
    /**
     * Array with children resources of the directory, used for the iterator.
     *
     * @var array(vcsCvsCliResource) $resources
     */
    protected $resources = null;

    /**
     * Returns the current item inside this iterator
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
     * Returns the next item of the iterator.
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
     * Returns the key for the current pointer.
     *
     * @return integer
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
     * Checks if the current item is valid.
     *
     * @return boolean
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
     * Set the internal pointer of an array to its first element.
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
     * Returns the children for this instance.
     *
     * @return vcsDirectory
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
     * Returns if this directory contains files of directories.
     *
     * @return boolean
     */
    public function hasChildren()
    {
        if ( $this->resources === null )
        {
            $this->initializeResouces();
        }

        return current( $this->resources ) instanceof vcsDirectory;
    }

    /**
     * Initialize resources array
     *
     * Initilaize the array containing all child elements of the current
     * directly as vcsCvsCliResource objects.
     *
     * @return array(vcsCvsCliResource)
     */
    protected function initializeResouces()
    {
        $this->resources = array();

        // Build resources array, without constructing the objects yet, for
        // lazy construction of the object tree.
        $directory = new DirectoryIterator( $this->root . $this->path );
        foreach ( $directory as $fileInfo )
        {
            $fileName = $fileInfo->getFilename();
            if ( ( $fileName === '.' ) ||
                 ( $fileName === '..' ) ||
                 ( $fileName === 'CVS' ) )
            {
                continue;
            }

            $resource = null;
            if ( $fileInfo->isDir() === true )
            {
                $resource = new vcsCvsCliDirectory( $this->root, $this->path . $fileName . '/' );
            }
            else
            {
                $resource = new vcsCvsCliFile( $this->root, $this->path . $fileName );
            }
            $this->resources[] = $resource;
        }
    }
}
