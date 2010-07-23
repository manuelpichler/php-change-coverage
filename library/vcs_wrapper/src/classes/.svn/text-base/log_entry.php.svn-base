<?php
/**
 * PHP VCS wrapper log entry
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
 * VCS wrapper abstracted log entry
 */
class vcsLogEntry extends vcsBaseStruct
{
    /**
     * Array containing the structs properties.
     * 
     * @var array
     */
    protected $properties = array(
        'version' => null,
        'author'  => null,
        'message' => null,
        'date'    => null,
    );

    /**
     * Construct struct from given values
     * 
     * @param string $version 
     * @param string $author 
     * @param string $message 
     * @param int $date 
     * @return void
     */
    public function __construct( $version = null, $author = null, $message = null, $date = null )
    {
        $this->version = (string) $version;
        $this->author  = (string) $author;
        $this->message = (string) $message;
        $this->date    = (int) $date;
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

