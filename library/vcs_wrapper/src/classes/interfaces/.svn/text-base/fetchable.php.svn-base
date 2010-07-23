<?php
/**
 * PHP VCS wrapper fetchable interface
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
 * Interface for versioned resources which can be fetched from earlier versions
 *
 * This implemented should be implemented by VCS for resources, which can be
 * fetched in earlier revisions then the current revision.
 */
interface vcsFetchable extends vcsVersioned
{
    /**
     * Get content for version
     *
     * Get the contents of the current resource in the specified version.
     *
     * @param string $version 
     * @return string
     */
    public function getVersionedContent( $version );
}

