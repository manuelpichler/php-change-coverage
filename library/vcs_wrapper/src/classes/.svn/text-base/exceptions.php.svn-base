<?php
/**
 * PHP VCS wrapper exceptions
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
 * @subpackage Cache
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Base exception for all exceptions inside the CVSWrapper
 */
abstract class vcsException extends Exception
{
}

/**
 * Exception thrown, when a requested file could not be found.
 */
class vcsNoSuchFileException extends vcsException
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
 * Exception thrown, when something totally unexpected happens, for no
 * custom exception really makes sense, because the wrapper state cannot
 * be handled properly on application side.
 */
class vcsRuntimeException extends vcsException
{
    /**
     * Construct exception
     *
     * @param string $message
     * @return void
     */
    public function __construct( $message )
    {
        parent::__construct( "Runtime exception: $message" );
    }
}

/**
 * Exception thrown, when the cache is used, but not initialized.
 */
class vcsCacheNotInitializedException extends vcsException
{
    /**
     * Construct exception
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct( 'Cache has not been initialized.' );
    }
}

/**
 * Exception thrown when a value is passed to the cache, which is not
 * cacheable.
 */
class vcsNotCacheableException extends vcsException
{
    /**
     * Construct exception
     *
     * @param mixed $value
     * @return void
     */
    public function __construct( $value )
    {
        parent::__construct( 'Value of type ' . gettype( $value ) . ' cannot be cached. Only arrays, scalar values and objects implementing arbitCacheable are allowed.' );
    }
}

/**
 * Exception thrown when a checkout of a repository failed.
 */
class vcsCheckoutFailedException extends vcsException
{
    /**
     * Construct exception
     *
     * @param string $url
     * @return void
     */
    public function __construct( $url )
    {
        parent::__construct( "Checkout of repository at '$url' failed." );
    }
}

/**
 * Exception thrown when a version is requested from a repository, which does
 * not exist.
 */
class vcsNoSuchVersionException extends vcsException
{
    /**
     * Construct exception
     *
     * @param string $path
     * @param string $version
     * @return void
     */
    public function __construct( $path, $version )
    {
        parent::__construct( "There is no version '$version' of resource '$path'." );
    }
}

/**
 * Exception thrown when a ZIP archive could not be opened by the PHP class
 * ZipArchive, which just returns some failue code in this case.
 */
class vcsInvalidZipArchiveException extends vcsException
{
    /**
     * Failure messages for the error codes.
     *
     * @var array
     */
    protected $messages = array(
        ZipArchive::ER_OK          => 'No error.',
        ZipArchive::ER_MULTIDISK   => 'Multi-disk zip archives not supported.',
        ZipArchive::ER_RENAME      => 'Renaming temporary file failed.',
        ZipArchive::ER_CLOSE       => 'Closing zip archive failed',
        ZipArchive::ER_SEEK        => 'Seek error',
        ZipArchive::ER_READ        => 'Read error',
        ZipArchive::ER_WRITE       => 'Write error',
        ZipArchive::ER_CRC         => 'CRC error',
        ZipArchive::ER_ZIPCLOSED   => 'Containing zip archive was closed',
        ZipArchive::ER_NOENT       => 'No such file.',
        ZipArchive::ER_EXISTS      => 'File already exists',
        ZipArchive::ER_OPEN        => 'Can\'t open file',
        ZipArchive::ER_TMPOPEN     => 'Failure to create temporary file.',
        ZipArchive::ER_ZLIB        => 'Zlib error',
        ZipArchive::ER_MEMORY      => 'Memory allocation failure',
        ZipArchive::ER_CHANGED     => 'Entry has been changed',
        ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported.',
        ZipArchive::ER_EOF         => 'Premature EOF',
        ZipArchive::ER_INVAL       => 'Invalid argument',
        ZipArchive::ER_NOZIP       => 'Not a zip archive',
        ZipArchive::ER_INTERNAL    => 'Internal error',
        ZipArchive::ER_INCONS      => 'Zip archive inconsistent',
        ZipArchive::ER_REMOVE      => 'Can\'t remove file',
        ZipArchive::ER_DELETED     => 'Entry has been deleted',
    );

    /**
     * Construct exception
     *
     * @param string $file
     * @param int $code
     * @return void
     */
    public function __construct( $file, $code )
    {
        parent::__construct( "Error extracting $file: " . $this->messages[$code] );
    }
}

/**
 * Exception thrown when a checkout url could not be used to access a source
 * repository.
 */
class vcsInvalidRepositoryUrlException extends vcsException
{
    /**
     * Construct exception
     *
     * @param string $url
     * @param string $wrapper
     */
    public function __construct( $url, $wrapper )
    {
        parent::__construct( 'Invalid ' .$wrapper . ' repository url: "' . $url . '".' );
    }
}

/**
 * Exception thrown when a file or directory is requested from a
 * repository, which is not part of the repository.
 */
class vcsFileNotFoundException extends vcsException
{
    /**
     * Construct exception
     *
     * @param string $file
     */
    public function __construct( $file )
    {
        parent::__construct( "Could not locate '$file' inside the repository." );
    }
}

