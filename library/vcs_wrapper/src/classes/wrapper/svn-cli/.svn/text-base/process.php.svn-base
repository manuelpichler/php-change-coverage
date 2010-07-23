<?php
/**
 * PHP VCS wrapper SVN system process class
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
 * Svn executable wrapper for system process class
 */
class vcsSvnCliProcess extends pbsSystemProcess
{
    /**
     * Class constructor taking the executable
     * 
     * @param string $executable Executable to create system process for;
     * @return void
     */
    public function __construct( $executable = 'env' )
    {
        parent::__construct( $executable );

        $this->nonZeroExitCodeException = true;
        $this->argument( 'svn' )->argument( '--non-interactive' );
    }
}

