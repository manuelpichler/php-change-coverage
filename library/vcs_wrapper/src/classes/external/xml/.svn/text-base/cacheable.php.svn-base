<?php
/**
 * Arbit Xml document base
 *
 * This file is part of Arbit.
 *
 * Arbit is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * Arbit is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Arbit; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package XML
 * @version $Revision: 962 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Interface indicating that an object can be cached. To be cacheable the
 * implementing class needs the to implement __set_state method.
 *
 * @package Core
 * @version $Revision: 349 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
interface arbitCacheable
{
    /**
     * Recreate struct exported by var_export()
     * 
     * @param array $properties 
     * @return arbitCacheable
     */
    public static function __set_state( array $properties );
}

