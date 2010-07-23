<?php
/**
 * PHP VCS wrapper abstract file base class
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
 * Base class for files in the VCS wrapper.
 *
 * This class should be extended by the various wrappers to represent
 * files in the respective VCS. In the wrapper implementations this base
 * class should be extended with interfaces annotating the VCS features beside
 * basic file iteration.
 */
interface vcsFile
{
    /**
     * Get file contents
     * 
     * Get the contents of the current file.
     * 
     * @return string
     */
    public function getContents();

    /**
     * Get mime type
     * 
     * Get the mime type of the current file. If this information is not
     * available, just return 'application/octet-stream'.
     * 
     * @return string
     */
    public function getMimeType();
}

