<?php
/**
 * PHP VCS wrapper SVN-Cli based repository wrapper
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
 * @subpackage SvnCliWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Handler for SVN repositories
 */
class vcsSvnCliCheckout extends vcsSvnCliDirectory implements vcsCheckout
{
    /**
     * Construct repository with repository root path
     *
     * Construct the repository with the repository root path, which will be
     * used to store the repository contents.
     *
     * @param string $root
     * @return void
     */
    public function __construct( $root )
    {
        parent::__construct( $root, '/' );
    }

    /**
     * Initialize repository
     *
     * Initialize repository from the given URL. Optionally username and
     * password may be passed to the method, if required for the repository.
     *
     * @param string $url
     * @param string $user
     * @param string $password
     * @return void
     */
    public function initialize( $url, $user = null, $password = null )
    {
        $process = new vcsSvnCliProcess();

        if ( $user !== null )
        {
            $process->argument( '--username' )->argument( $user );

            if ( $password !== null )
            {
                $process->argument( '--password' )->argument( $password );
            }
        }

        $return = $process->argument( 'checkout' )->argument( $url )->argument( $this->root )->execute();

        // Cache basic revision information for checkout and update
        // currentVersion property.
        $this->getResourceInfo();
    }

    /**
     * Update repository
     *
     * Update the repository to the most current state. Method will return
     * true, if an update happened, and false if no update was available.
     *
     * Optionally a version can be specified, in which case the repository
     * won't be updated to the latest version, but to the specified one.
     *
     * @param string $version
     * @return bool
     */
    public function update( $version = null )
    {
        // Remember version before update try
        $oldVersion = $this->getVersionString();

        $process = new vcsSvnCliProcess();

        if ( $version !== null )
        {
            $process->argument( '-r' . $version );
        }

        $return = $process->argument( 'update' )->argument( $this->root )->execute();

        // Check if an update has happened
        $this->currentVersion = null;
        return ( $oldVersion !== $this->getVersionString() );
    }

    /**
     * Get checkout item
     *
     * Get an item from the checkout, specified by its local path. If no item
     * with the specified path exists an exception is thrown.
     *
     * Method either returns a vcsCheckout, a vcsDirectory or a vcsFile
     * instance, depending on the given path.
     *
     * @param string $path
     * @return mixed
     */
    public function get( $path = '/' )
    {
        $fullPath = realpath( $this->root . $path );

        if ( ( $fullPath === false ) ||
             ( strpos( $fullPath, $this->root ) !== 0 ) )
        {
            throw new vcsFileNotFoundException( $path );
        }

        switch ( true )
        {
            case ( $path === '/' ):
                return $this;

            case is_dir( $fullPath ):
                return new vcsSvnCliDirectory( $this->root, $path );

            default:
                return new vcsSvnCliFile( $this->root, $path );
        }
    }
}

