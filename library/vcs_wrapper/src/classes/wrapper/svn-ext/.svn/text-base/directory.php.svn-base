<?php
/**
 * PHP VCS wrapper SVN Ext directory wrapper
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
 * @subpackage SvnExtWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Directory implementation vor SVN Ext wrapper
 */
class vcsSvnExtDirectory extends vcsSvnExtResource implements vcsDirectory
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
     * directly as vcsSvnExtResource objects.
     * 
     * @return array(vcsSvnExtResource)
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
                 ( $path === '..' ) ||
                 ( $path === '.svn' ) )
                 // Also mid svn:ignore here?
            {
                continue;
            }
    
            $this->resources[] = ( is_dir( $this->root . $this->path . $path ) ?
                new vcsSvnExtDirectory( $this->root, $this->path . $path . '/' ) :
                new vcsSvnExtFile( $this->root, $this->path . $path )
            );
        }
        $contents->close();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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

