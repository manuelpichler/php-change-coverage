<?php
class PHP_ChangeCoverage_Autoloader
{
    private $libraryDirectory = null;

    private $autoloadMapping = array();

    public function __construct( $libraryDirectory )
    {
        $this->libraryDirectory = $libraryDirectory;
        $this->autoloadMapping  = include $libraryDirectory . '/vcs_wrapper/classes/autoload.php';
    }

    public function register()
    {
        spl_autoload_register( array( $this, 'autoload' ) );
    }

    public function autoload( $className )
    {
        if ( strpos( $className, 'PHP_' ) === 0 )
        {
            include strtr( $className, '_', '/') . '.php';
        }
        else if ( isset( $this->autoloadMapping[$className] ) )
        {
            include $this->libraryDirectory . '/vcs_wrapper/classes/' . $this->autoloadMapping[$className];
        }
    }
}