<?php
/**
 * PHP VCS wrapper diff struct
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
 * Basic struct containing all diff chunks for one file
 */
class vcsDiff extends vcsBaseStruct
{
    /**
     * Array containing the structs properties.
     * 
     * @var array
     */
    protected $properties = array(
        'from'   => null,
        'to'     => null,
        'chunks' => null,
    );

    /**
     * Construct diff from properties
     * 
     * @param string $from 
     * @param string $to 
     * @param array $chunks 
     * @return void
     */
    public function __construct( $from = null, $to = null, array $chunks = array() )
    {
        $this->from   = $from;
        $this->to     = $to;
        $this->chunks = $chunks;
    }

    /**
     * Recreate struct exported by var_export()
     *
     * Recreate struct exported by var_export()
     * 
     * @ignore
     * @param array $properties 
     * @return arbitBaseStruct
     */
    public static function __set_state( array $properties, $class = __CLASS__ )
    {
        return vcsBaseStruct::__set_state( $properties, $class );
    }
}

