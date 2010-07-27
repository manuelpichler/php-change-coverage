<?php
/**
 * This file is part of PHP_ChangeCoverage.
 *
 * PHP Version 5
 *
 * Copyright (c) 2010, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   QualityAssurance
 * @package    PHP_ChangeCoverage
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

/**
 * Command line interface for the php change coverage tool.
 *
 * @category   QualityAssurance
 * @package    PHP_ChangeCoverage
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_ChangeCoverage_TextUI_Command
{
    /**
     * Path to the temporary clover xml log file.
     *
     * @var string
     */
    private $temporaryClover = null;

    /**
     * Path to the clover xml log file, specified by the user.
     *
     * @var string
     */
    private $coverageClover = null;

    /**
     * Path to the html report directory, specified by the user.
     *
     * @var string
     */
    private $coverageHtml = null;

    /**
     * Path to the temp- and cache-directory, used by php change coverage.
     *
     * @var string
     */
    private $tempDirectory = null;

    /**
     * Timestamp that represents the lower bound of changes.
     *
     * @var integer
     */
    private $modifiedSince = 0;

    /**
     * The PHPUnit cli tool to use.
     *
     * @var string
     */
    private $phpunitBinary = 'phpunit';

    /**
     * Should the coverage report contain unmodified lines as covered?
     *
     * @var boolean
     */
    private $unmodifiedAsCovered = false;

    /**
     * The coverage report factory to use.
     *
     * @var PHP_ChangeCoverage_Report_Factory
     */
    private $reportFactory = null;

    /**
     * Constructs a new command instance and sets some system default values.
     */
    public function  __construct()
    {
        $this->modifiedSince = time() - ( 60 * 86400 );
        $this->tempDirectory = sys_get_temp_dir() . '/php-change-coverage/';

        if ( stripos( PHP_OS, 'win' ) === 0 )
        {
            $this->phpunitBinary .= '.bat';
        }

    }

    /**
     * First runs PHPUnit and then post processes the generated coverage data
     * to calculate the change coverage.
     *
     * @param array(string) $argv The raw command line arguments.
     *
     * @return integer
     */
    public function run( array $argv )
    {
        $this->printVersionString();

        try
        {
            $this->handleArguments( $argv );

            $arguments = $this->extractPhpunitArguments( $argv );
            $arguments = array_map( 'escapeshellarg', $arguments );
        }
        catch ( InvalidArgumentException $e )
        {
            $exception = $e->getMessage();
            $arguments = array( '--help' );
        }

        $command = sprintf(
            '%s %s',
            escapeshellarg( $this->phpunitBinary ),
            join( ' ', $arguments )
        );
        
        passthru( $command, $code );

        if ( $code === 2 || in_array( '--help', $arguments ) )
        {
            $this->writeLine();
            $this->writeLine();
            $this->writeLine( 'Additional options added by PHP_ChangeCoverage' );
            $this->writeLine();
            $this->writeLine( '  --temp-dir               Temporary directory for generated runtime data.' );
            $this->writeLine( '  --phpunit-binary         Optional path to phpunit\'s binary.' );
            $this->writeLine( '  --modified-since         Cover only lines that were changed since this date.' );
            $this->writeLine( '                           This option accepts textual date expressions.' );
            $this->writeLine( '  --unmodified-as-covered  Mark all unmodified lines as covered.' );

            if ( isset( $exception ) )
            {
                $this->writeLine();
                $this->writeLine( $exception );
                return 2;
            }
        }
        else if ( file_exists( $this->temporaryClover ) )
        {
            PHP_Timer::start();

            $report = $this->createCoverageReport();
            $codeCoverage = $this->rebuildCoverageData( $report );

            $this->writeCoverageClover( $codeCoverage );
            $this->writeCoverageHtml( $codeCoverage );

            PHP_Timer::stop();
            $this->writeLine( PHP_Timer::resourceUsage() );

            unlink( $this->temporaryClover );
        }
        return $code;
    }

    /**
     * Outputs the PHP_ChangeCoverage version string.
     *
     * @return void
     */
    protected function printVersionString()
    {
        $this->writeLine( 'PHP_ChangeCoverage @package_version@ by Manuel Pichler' );
        $this->write( ' utilizes ' );
    }

    /**
     * This method extracts the command line arguments that belong to the change
     * coverage tool.
     *
     * @param array(string) $argv The raw command line arguments.
     *
     * @return void
     * @throws InvalidArgumentException When one of the passed command line
     *         values has an unexpected or incorrect format.
     */
    protected function handleArguments( array $argv )
    {
        if ( is_int( $i = array_search( '--temp-dir', $argv ) ) )
        {
            $this->tempDirectory = $this->parseTemporaryDirectory( $argv[$i + 1] );
        }
        $temporaryClover = $this->tempDirectory . '/' . uniqid( '~ccov-' ) . '.xml';


        if ( is_int( $i = array_search( '--coverage-clover', $argv ) ) )
        {
            $this->temporaryClover = $temporaryClover;
            $this->coverageClover  = $argv[$i + 1];
        }
        if ( is_int( $i = array_search( '--coverage-html', $argv ) ) )
        {
            $this->temporaryClover = $temporaryClover;
            $this->coverageHtml    = $argv[$i + 1];
        }
        if ( is_int( $i = array_search( '--modified-since', $argv ) ) )
        {
            $this->modifiedSince = $this->parseModifiedSince( $argv[$i + 1] );
        }
        if ( is_int( $i = array_search( '--phpunit-binary', $argv ) ) )
        {
            $this->phpunitBinary = $this->parsePhpunitBinary( $argv[$i + 1] );
        }
        if ( is_int( $i = array_search( '--unmodified-as-covered', $argv ) ) )
        {
            $this->unmodifiedAsCovered = true;
        }
    }

    /**
     * Parses the temporary directory that was specified by the user.
     *
     * @param string $directory Temporary directory provided by the user.
     *
     * @return string
     */
    protected function parseTemporaryDirectory( $directory )
    {
        if ( false === file_exists( $directory ) )
        {
            mkdir( $directory, 0755, true );
        }
        if ( is_dir( $directory ) )
        {
            return $directory;
        }
        throw new InvalidArgumentException( "Cannot find temp directory: '{$directory}." );
    }

    /**
     * This method parses a user specified start date and returns it's unix
     * timestamp representation. The current implementation of this method
     * only utilizes PHP's native <b>strtotime()</b> function to parse user
     * input.
     *
     * @param string $modified The user specified start date for modifications.
     *
     * @return integer
     * @throws InvalidArgumentException When the given modified expression
     *         cannot be parsed by strtotime().
     * @todo Support other formats like "3m 15d 12h".
     */
    protected function parseModifiedSince( $modified )
    {
        if ( is_int( $timestamp = strtotime( $modified ) ) )
        {
            return $timestamp;
        }
        throw new InvalidArgumentException( "Cannot parse modified since: '{$modified}'." );
    }

    /**
     * Handles a user specified phpunit cli tool.
     *
     * @param string $phpunit The user specified PHPUnit cli tool.
     *
     * @return string
     * @throws InvalidArgumentException When the given binary does not exist.
     */
    protected function parsePhpunitBinary( $phpunit )
    {
        if ( file_exists( $phpunit ) )
        {
            return $phpunit;
        }
        throw new InvalidArgumentException( "Cannot find phpunit binary: '{$phpunit}'." );
    }

    /**
     * This method extracts all those arguments that are relevant for the nested
     * phpunit process.
     *
     * @param array(string) $argv The raw arguments passed to php change coverage.
     *
     * @return array(string)
     * @todo Move this into a separate PHPUnitBinary class.
     */
    protected function extractPhpunitArguments( array $argv )
    {
        $remove = array(
            '--coverage-clover'        =>  true,
            '--coverage-html'          =>  true,
            '--temp-dir'               =>  true,
            '--modified-since'         =>  true,
            '--phpunit-binary'         =>  true,
            '--unmodified-as-covered'  =>  false,
        );

        $arguments = array();
        for ( $i = 1; $i < count( $argv ); ++$i )
        {
            if ( isset( $remove[$argv[$i]] ) )
            {
                if ( $remove[$argv[$i]] )
                {
                    ++$i;
                }
            }
            else
            {
                $arguments[$i] = $argv[$i];
            }
        }

        if ( $this->temporaryClover )
        {
            array_unshift( $arguments, '--coverage-clover', $this->temporaryClover );
        }
        return $arguments;
    }

    /**
     * Sets a coverage report factory to use.
     *
     * @param PHP_ChangeCoverage_Report_Factory $factory The coverage report factory.
     *
     * @return void
     */
    public function setReportFactory( PHP_ChangeCoverage_Report_Factory $factory )
    {
        $this->reportFactory = $factory;
    }

    /**
     * Returns the configured coverage report factory. If no factory was
     * configured, this method will create an instance of the default factory
     * implementation.
     *
     * @return PHP_ChangeCoverage_Report_Factory
     */
    protected function getReportFactory()
    {
        if ( $this->reportFactory === null )
        {
            $this->reportFactory = new PHP_ChangeCoverage_Report_Factory();
        }
        return $this->reportFactory;
    }

    /**
     * Creates a coverage report from a previously generated xml log file.
     *
     * @return PHP_ChangeCoverage_Report
     */
    protected function createCoverageReport()
    {
        return $this->getReportFactory()->createReport( $this->temporaryClover );
    }

    /**
     * This method takes a coverage report and then rebuilds the raw coverage
     * data based on the report data and the change history of the covered files.
     *
     * @param PHP_ChangeCoverage_Report $report The coverage report data.
     *
     * @return PHP_CodeCoverage
     */
    protected function rebuildCoverageData( PHP_ChangeCoverage_Report $report )
    {
        $codeCoverage = new PHP_CodeCoverage();


        $factory = new PHP_ChangeCoverage_ChangeSet_Factory();
        vcsCache::initialize( $this->tempDirectory );

        $this->writeLine();
        $this->writeLine( 'Collecting commits and meta data, this may take a moment.' );
        $this->writeLine();

        $xdebug = new PHP_ChangeCoverage_Xdebug();
        if ( $this->unmodifiedAsCovered )
        {
            $xdebug->setUnmodifiedAsCovered();
        }

        foreach ( $report->getFiles() as $file )
        {
            $changeSet = $factory->create( $file );
            $changeSet->setStartDate( $this->modifiedSince );
            
            foreach ( $xdebug->generateData( $changeSet->calculate() ) as $data )
            {
                $codeCoverage->append( $data, md5( microtime() ) );
            }
        }

        return $codeCoverage;
    }

    /**
     * This method generates a xml coverage report compatible with reports
     * generated by clover.
     *
     * @param PHP_CodeCoverage $coverage The raw coverage data.
     *
     * @return void
     */
    protected function writeCoverageClover( PHP_CodeCoverage $coverage )
    {
        if ( $this->coverageClover )
        {
            $this->writeLine( 'Writing change coverage data to XML file, this may take a moment.' );
            $this->writeLine();

            $clover = new PHP_CodeCoverage_Report_Clover();
            $clover->process( $coverage, $this->coverageClover );
        }
    }

    /**
     * This method generates a html coverage report.
     *
     * @param PHP_CodeCoverage $coverage The raw coverage data.
     *
     * @return void
     */
    protected function writeCoverageHtml( PHP_CodeCoverage $coverage )
    {
        if ( $this->coverageHtml )
        {
            $this->writeLine( 'Writing change coverage report, this may take a moment.' );
            $this->writeLine();

            $html = new PHP_CodeCoverage_Report_HTML(
                array(
                    'title'      =>  'Coverage Report for files modified since ' . date( 'Y/m/d', $this->modifiedSince ),
                    'yui'        =>  false,
                    'generator'  =>  ' post processed by PHP_ChangeCoverage'
                )
            );
            $html->process( $coverage, $this->coverageHtml );
        }
    }

    /**
     * Writes the given data string to STDOUT and appends a line feed.
     *
     * @param string $data Any data that should be send to STDOUT.
     *
     * @return void
     */
    protected function writeLine( $data = '' )
    {
        $this->write( $data . PHP_EOL );
    }

    /**
     * Writes the given data string to STDOUT.
     *
     * @param string $data Any data that should be send to STDOUT.
     *
     * @return void
     */
    protected function write( $data )
    {
        echo $data;
    }
}