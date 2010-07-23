<?php
/**
 * PHP VCS wrapper CVS-Cli based repository wrapper
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
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Handler for CVS repositories
 */
class vcsCvsCliCheckout extends vcsCvsCliDirectory implements vcsCheckout
{
    /**
     * Construct checkout with the given root path.
     *
     * Construct the checkout with the given root path, which will be used to
     * store the repository contents.
     *
     * @param string $root
     * @return void
     */
    public function __construct( $root )
    {
        parent::__construct( $root, '/' );
    }

    /**
     * Initializes fresh checkout
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
        $count = substr_count( $url, '#' );
        if ( $count === 1 )
        {
            $revision = null;
            list( $repoUrl, $module ) = explode( '#', $url );
        }
        else if ( $count === 2 )
        {
            list( $repoUrl, $module, $revision ) = explode( '#', $url );
        }
        else
        {
            throw new vcsInvalidRepositoryUrlException( $url, 'cvs' );
        }

        $process = new vcsCvsCliProcess();
        $process->argument( '-d' )
                ->argument( $repoUrl )
                ->argument( 'checkout' )
                ->argument( '-P' )
                ->argument( '-r' )
                ->argument( $revision )
                ->argument( '-d' )
                ->argument( $this->root )
                ->argument( $module )
                ->execute();
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
        if ( $version === null )
        {
            $version = 'HEAD';
        }

        $process = new vcsCvsCliProcess();
        $process->workingDirectory( $this->root )
                ->redirect( vcsCvsCliProcess::STDERR, vcsCvsCliProcess::STDOUT )
                ->argument( 'update' )
                ->argument( '-Rd' )
                ->argument( '-r' )
                ->argument( $version )
                ->execute();

        return ( preg_match( '#[\n\r]U #', $process->stdoutOutput ) > 0 );
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
                return new vcsCvsCliDirectory( $this->root, $path );

            default:
                return new vcsCvsCliFile( $this->root, $path );
        }
    }
}

