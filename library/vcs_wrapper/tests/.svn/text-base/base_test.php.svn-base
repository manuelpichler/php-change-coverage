<?php
/**
 * Base test cache for cache tests
 *
 * @version $Revision$
 * @license GPLv3
 */

/**
 * Base test case for cache tests, handling the creation and removel of
 * temporary test directories.
 */
class vcsTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Temporary directory for cache contents
     * 
     * @var string
     */
    protected $tempDir;

    /**
     * List of all temporary directories created
     * 
     * @var array
     */
    protected $directories = array();

    /**
     * Create a unique temporary directory for cache contents.
     * 
     * @return void
     */
    public function setUp()
    {
        $this->tempDir = $this->createTempDir();
    }

    /**
     * Create a temporary directory
     *
     * Create a temporary writeable directory, which will be removed again at
     * the end of the test. The directory name is returned.
     *
     * @return string
     */
    protected function createTempDir()
    {
        do {
            $path = dirname( __FILE__ ) . '/tmp/cache_' . substr( md5( microtime() ), 0, 8 );
        } while ( is_dir( $path ) || file_exists( $path ) );

        mkdir( $this->directories[] = $path, 0777, true );
        return $path;
    }

    /**
    * Recursively copy a file or directory.
    *
    * Recursively copy a file or directory in $source to the given
    * destination. If a depth is given, the operation will stop, if the given
    * recursion depth is reached. A depth of -1 means no limit, while a depth
    * of 0 means, that only the current file or directory will be copied,
    * without any recursion.
    *
    * You may optionally define modes used to create files and directories.
    *
    * @throws ezcBaseFileNotFoundException
    *      If the $sourceDir directory is not a directory or does not exist.
    * @throws ezcBaseFilePermissionException
    *      If the $sourceDir directory could not be opened for reading, or the
    *      destination is not writeable.
    *
    * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
    * @license http://ez.no/licenses/new_bsd New BSD License
    * @param string $source
    * @param string $destination
    * @param int $depth
    * @param int $dirMode
    * @param int $fileMode
    * @return void
    */
    static public function copyRecursive( $source, $destination, $depth = -1, $dirMode = 0775, $fileMode = 0664 )
    {
        // Check if source file exists at all.
        if ( !is_file( $source ) && !is_dir( $source ) )
        {
            throw new ezcBaseFileNotFoundException( $source );
        }

        // Destination file should NOT exist
        if ( is_file( $destination ) || is_dir( $destination ) )
        {
            throw new ezcBaseFilePermissionException( $destination, ezcBaseFileException::WRITE );
        }

        // Skip non readable files in source directory
        if ( !is_readable( $source ) )
        {
            return;
        }

        // Copy
        if ( is_dir( $source ) )
        {
            mkdir( $destination );
            // To ignore umask, umask() should not be changed with
            // multithreaded servers...
            chmod( $destination, $dirMode );
        }
        elseif ( is_file( $source ) )
        {
            copy( $source, $destination );
            chmod( $destination, $fileMode );
        }

        if ( ( $depth === 0 ) ||
            ( !is_dir( $source ) ) )
        {
            // Do not recurse (any more)
            return;
        }

        // Recurse
        $dh = opendir( $source );
        while ( ( $file = readdir( $dh ) ) !== false )
        {
            if ( ( $file === '.' ) ||
                ( $file === '..' ) )
            {
                continue;
            }

            self::copyRecursive(
                $source . '/' . $file,
                $destination . '/' . $file,
                $depth - 1, $dirMode, $fileMode
            );
        }
    }

    /**
     * Remove directory
     *
     * Delete the given directory and all of its contents recusively.
     * 
     * @param string $dir 
     * @return void
     */
    protected function removeRecursively( $dir )
    {
        $directory = dir( $dir );
        while ( ( $path = $directory->read() ) !== false )
        {
            if ( ( $path === '.' ) ||
                 ( $path === '..' ) )
            {
                continue;
            }
            $path = $dir . '/' . $path;

            if ( is_dir( $path ) )
            {
                $this->removeRecursively( $path );
            }
            else
            {
                unlink( $path );
            }
        }

        rmdir( $dir );
    }

    /**
     * Remove the temporary cache directory if the test has failed.
     * 
     * @return void
     */
    public function tearDown()
    {
        if ( !$this->hasFailed() )
        {
            foreach ( $this->directories as $dir )
            {
                if ( is_dir( $dir ) )
                {
                    $this->removeRecursively( $dir );
                }
            }
        }
    }
}

