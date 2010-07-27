<?php
/**
 * PHP VCS wrapper diff line struct
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
 * @version $Revision: 1859 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Basic struct containing a diff line
 *
 * @package VCSWrapper
 * @subpackage Diff
 * @version $Revision: 1859 $
 */
class vcsDiffLine extends vcsBaseStruct
{
    /**
     * Array containing the structs properties.
     * 
     * @var array
     */
    protected $properties = array(
        'type'    => null,
        'content' => null,
    );

    /**
     * A line in a diff, which is new in the destination file 
     */
    const ADDED = 1;

    /**
     * A line in a diff, which has been removed in the destination file 
     */
    const REMOVED = 2;

    /**
     * A line in the diff which is equal in the source and the destination file
     */
    const UNCHANGED = 3;

    /**
     * Construct diff from properties
     * 
     * @param int $type 
     * @param string $content 
     * @return vcsDiffLine
     */
    public function __construct( $type = self::UNCHANGED, $content = null )
    {
        $this->type    = $type;
        $this->content = $content;
    }

    /**
     * Recreate struct exported by var_export()
     *
     * Recreate struct exported by var_export()
     * 
     * @ignore
     * @param array $properties 
     * @param mixed $class 
     * @return void
     */
    public static function __set_state( array $properties, $class = __CLASS__ )
    {
        return vcsBaseStruct::__set_state( $properties, $class );
    }
}

