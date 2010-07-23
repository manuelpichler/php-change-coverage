<?php
/**
 * PHP VCS wrapper Git system process class
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
 * @subpackage GitCliWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Git executable wrapper for system process class
 */
class vcsGitCliProcess extends pbsSystemProcess
{
    /**
     * Static property containg information, if the version of the git CLI
     * binary version has already been verified.
     *
     * @var bool
     */
    public static $checked = false;

    /**
     * Class constructor taking the executable
     * 
     * @param string $executable Executable to create system process for;
     * @return void
     */
    public function __construct( $executable = 'env' )
    {
        parent::__construct( $executable );
        self::checkVersion();

        $this->nonZeroExitCodeException = true;
        $this->argument( 'git' )->argument( '--no-pager' );
    }

    /**
     * Verify git version
     *
     * Verify hat the version of the installed GIT binary is at least 1.6. Will
     * throw an exception, if the binary is not available or too old.
     * 
     * @return void
     */
    protected static function checkVersion()
    {
        if ( self::$checked === true )
        {
            return true;
        }

        $process = new pbsSystemProcess( 'env' );
        $process->argument( 'git' )->argument( '--version' )->execute();

        if ( !preg_match( '(\\d+(?:\.\\d+)+)', $process->stdoutOutput, $match ) )
        {
            throw new vcsRuntimeException( 'Could not determine GIT version.' );
        }

        if ( version_compare( $match[0], '1.6', '>=' ) )
        {
            return self::$checked = true;
        }

        throw new vcsRuntimeException( 'Git is required in a minimum version of 1.6.' );
    }
}

