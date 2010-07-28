<?php
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Base class for test cases of the change coverage application.
 *
 * @category  QualityAssurance
 * @package   PHP_ChangeCoverage
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
abstract class PHP_ChangeCoverage_AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Do not backup static test properties.
     *
     * @var boolean
     */
    protected $backupStaticAttributes = false;

    /**
     * The projects base directory.
     *
     * @var string
     */
    protected $baseDir = null;

    /**
     * Setup registers the autoload mechanism for the source under test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        spl_autoload_register( array( $this, 'autoload' ) );

        $this->baseDir = dirname( __FILE__ ) . '/../../..';

        $this->deleteTempDirectory();
    }

    /**
     * This method removes the autoloader after each test.
     *
     * @return void
     */
    protected function tearDown()
    {
        foreach ( spl_autoload_functions() as $function )
        {
            if ( $function === 'phpunit_autoload' )
            {
                continue;
            }
            spl_autoload_unregister( $function );
        }

        $this->deleteTempDirectory();

        parent::tearDown();
    }

    /**
     * Autoload implementation for the test cases.
     *
     * @param string $className Name of the class with unknown source.
     *
     * @return void
     */
    public function autoload( $className )
    {
        if ( strpos( $className, 'PHP_ChangeCoverage_' ) === 0 )
        {
            include $this->baseDir . '/source/' . strtr( $className, '_', '/' ) . '.php';
        }
        else if ( strpos( $className, 'PHP_CodeCoverage_' ) === 0 )
        {
            include strtr( $className, '_', '/' ) . '.php';
        }
        else
        {
            $autoload = $this->loadAutoload();
            if ( isset( $autoload[$className] ) )
            {
                include $this->baseDir . '/library/vcs_wrapper/classes/' . $autoload[$className];
            }
        }
    }

    public function loadAutoload()
    {
        $fileName = $this->baseDir . '/library/vcs_wrapper/classes/autoload.php';
        if ( file_exists( $fileName ) )
        {
            return include $fileName;
        }
        return array();
    }

    /**
     * Creates a temporary directory for the given local name.
     *
     * @param string $directory Local directory name.
     *
     * @return string
     */
    protected function createDirectory( $directory )
    {
        if ( $directory === '.' || $directory === '/' || $directory === '' )
        {
            return $this->createTempDirectory();
        }
        $path = $this->createTempDirectory() . '/' . $directory;
        mkdir( $path, 0755, true );
        return $path;
    }

    /**
     * Creates a temporary file with the given data as content.
     *
     * @param string $file Local file name.
     * @param string $data The file contents.
     *
     * @return string
     */
    protected function createFile( $file, $data )
    {
        $path  = $this->createDirectory( dirname( $file ) );
        $path .= '/' . basename( $file );

        file_put_contents( $path , $data );

        return $path;
    }

    protected function createTempDirectory()
    {
        if ( false === file_exists( $this->getTempDirectory() ) )
        {
            mkdir( $this->getTempDirectory(), 0755, true );
        }
        return $this->getTempDirectory();
    }

    protected function deleteTempDirectory()
    {
        if ( file_exists( $this->getTempDirectory() ) )
        {
            $this->deleteDirectory( $this->getTempDirectory() );
        }
    }

    protected function deleteDirectory( $directory )
    {
        $dir = new DirectoryIterator( $directory );
        foreach ( $dir as $file )
        {
            if ( $file->isFile() )
            {
                unlink( $file->getPathname() );
            }
            else if ( $file->getFilename() !== '.' && $file->getFilename() !== '..' )
            {
                $this->deleteDirectory( $file->getPathname() );
            }
            unset( $file );
        }
        unset( $dir );
        rmdir( $directory );
    }

    protected function getTempDirectory()
    {
        return rtrim( sys_get_temp_dir(), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . '~phpunit-phpcc';
    }
}