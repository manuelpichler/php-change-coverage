<?php
/**
 * PHP VCS wrapper logged interface
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
 * @subpackage Core
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Interface for resources with a log available.
 *
 * This interface should be implemented by resources which are versioned in
 * the version control system. It allows access to the current version of a
 * resource and also to contents in later versions of a resource.
 */
interface vcsLogged extends vcsVersioned
{
    /**
     * Get full revision log
     *
     * Return the full revision log for the given resource. The revision log
     * should be returned as an array of vcsLogEntry objects.
     *
     * @return array
     */
    public function getLog();

    /**
     * Get revision log entry
     *
     * Get the revision log entry for the spcified version.
     * 
     * @param string $version
     * @return vcsLogEntry
     */
    public function getLogEntry( $version );
}

