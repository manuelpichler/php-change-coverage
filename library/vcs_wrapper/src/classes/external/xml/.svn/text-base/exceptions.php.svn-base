<?php
/**
 * PHP VCS wrapper exceptions
 *
 * This file is part of arbit-wrapper.
 *
 * arbit-wrapper is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * arbit-wrapper is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with arbit-wrapper; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package VCSWrapper
 * @subpackage Cache
 * @version $Revision: 955 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Base exception for all exceptions inside the CVSWrapper
 */
abstract class arbitException extends Exception
{
}

/**
 * Exception thrown, when a requested file could not be found.
 */
class arbitNoSuchFileException extends arbitException
{
    /**
     * Construct exception
     *
     * @param string $file
     * @return void
     */
    public function __construct( $file )
    {
        parent::__construct( "The file '$file' could not be found." );
    }
}

/**
 * Exception thrown, when a requested file could not be found.
 */
class arbitXmlParserException extends arbitException
{
    /**
     * Human readable error names for libXML error type constants.
     *
     * @var array
     */
    protected $levels = array(
        LIBXML_ERR_WARNING => 'Warning',
        LIBXML_ERR_ERROR   => 'Error',
        LIBXML_ERR_FATAL   => 'Fatal error',
    );

    /**
     * Construct exception
     *
     * @param string $file
     * @param array $error
     * @return void
     */
    public function __construct( $file, array $errors )
    {
        foreach ( $errors as $nr => $error )
        {
            $errors[$nr] = sprintf( "%s: (%d) %s in %s +%d (%d).",
                $this->levels[$error->level],
                $error->code,
                $error->message,
                $error->file,
                $error->line,
                $error->column
            );
        }

        parent::__construct( "The XML file '$file' could not be parsed:\n - " . implode( "\n - ", $errors ) . "\n" );
    }
}

/**
 * Exception thrown, when access of some type is not allowed
 */
class arbitAccessException extends arbitException
{
    /**
     * Construct exception
     *
     * @param string $property
     * @param string $message
     * @return void
     */
    public function __construct( $property, $message )
    {
        parent::__construct( "The property '$property' cannot be accessed: $message." );
    }
}

/**
 * Exception thrown, when the assigned value is invalid
 */
class arbitValueException extends arbitException
{
    /**
     * Construct exception
     *
     * @param string $property
     * @param string $message
     * @return void
     */
    public function __construct( $property, $message )
    {
        parent::__construct( "The value for '$property' is invalid: $message." );
    }
}

