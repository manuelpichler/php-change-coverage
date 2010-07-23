<?php
/**
 * PHP VCS wrapper diff parser base
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
 * @subpackage Diff
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Abstract base class for diff parsers
 */
abstract class vcsDiffParser
{
    /**
     * Parse diff string
     *
     * Parse the diff, given as a string, into a vcsDiff objects. The different
     * diff objects are returned in an array.
     *
     * @param string $string 
     * @return array(vcsDiff)
     */
    abstract public function parseString( $string );

    /**
     * Parse diff file
     *
     * Parse the diff, given as a file name, into a vcsDiff objects. The
     * different diff objects are returned in an array.
     *
     * @param string $file 
     * @return array(vcsDiff)
     */
    public function parseFile( $file )
    {
        return $this->parseString( file_get_contents( $file ) );
    }
}

