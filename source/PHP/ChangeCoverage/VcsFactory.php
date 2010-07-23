<?php
class PHP_ChangeCoverage_VcsFactory
{
    private $libraryRoot = null;

    private $autoload = array();

    private $timeRange = 0;

    public function __construct( $lib )
    {
        $this->timeRange = time() - ( 90 * 86400 );

        $this->libraryRoot = $lib;
        $this->autoload = include $lib . '/autoload.php';

        spl_autoload_register( array( $this, 'autoload' ) );
    }

    public function create( $path )
    {
        $fileParts = array( basename( $path ) );
        $parts = explode( DIRECTORY_SEPARATOR, dirname( realpath( $path ) ) );
        do {

            $root = join( DIRECTORY_SEPARATOR, $parts ) . DIRECTORY_SEPARATOR;
            $path = DIRECTORY_SEPARATOR . join( DIRECTORY_SEPARATOR, $fileParts );

            if ( file_exists( $root . '.svn' ) )
            {
                if ( extension_loaded( 'svn' ) )
                {
                    return new vcsSvnExtFile( $root, $path );
                }
                else
                {
                    return new vcsSvnCliFile( $root, $path );
                }
            }
            else if ( file_exists( $root . '.git' ) )
            {
                return new vcsGitCliFile( $root, $path );
            }
            else if ( file_exists( $root . '.hg' ) )
            {
                return new vcsHgCliFile( $root, $path );
            }
            else if ( file_exists( $root . '.bzr' ) )
            {
                return new vcsBzrCliFile($root, $path);
            }
            else if ( file_exists( $root . 'CVS' ) )
            {
                return new vcsCvsCliFile( $root, $path );
            }
            
            if ( ( $part = array_pop( $parts ) ) === null )
            {
                throw new RuntimeException( 'No more elements found.' );
            }
            
            array_unshift( $fileParts, $part );
            
        } while ( true );
    }

    public function autoload( $className )
    {
        if ( isset( $this->autoload[$className] ) )
        {
            include $this->libraryRoot . '/' . $this->autoload[$className];
        }
    }
}