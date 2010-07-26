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
     * The coverage report factory to use.
     *
     * @var PHP_ChangeCoverage_Report_Factory
     */
    private $reportFactory = null;

    public function  __construct()
    {
        $this->tempDirectory = sys_get_temp_dir() . '/php-change-coverage/';
    }

    public function run( array $argv )
    {
        $this->printVersionString();

        $this->handleArguments( $argv );

        $arguments = $this->extractPhpunitArguments( $argv );
        $arguments = array_map( 'escapeshellarg', $arguments );

        $command = sprintf(
            '%s %s',
            escapeshellarg( 'phpunit' ),
            join( ' ', $arguments )
        );
        
        passthru( $command, $code );

        if ( file_exists( $this->temporaryClover ) )
        {
            PHP_Timer::start();

            $report = $this->createCoverageReport();
            $codeCoverage = $this->rebuildCoverageData( $report );

            $this->writeCoverageClover( $codeCoverage );
            $this->writeCoverageHtml( $codeCoverage );

            PHP_Timer::stop();
            echo PHP_Timer::resourceUsage(), PHP_EOL;

            unlink( $this->temporaryClover );
        }
        return $code;
    }

    protected function printVersionString()
    {
        echo 'PHP_ChangeCoverage @package_version@ by Manuel Pichler', PHP_EOL,
             ' utilizes ';
    }

    protected function handleArguments( array $argv )
    {
        $this->modifiedSince = time() - ( 60 * 86400 );

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
    }

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
        throw new RuntimeException( "Cannot find temp directory: '{$directory}." );
    }

    protected function parseModifiedSince( $modified )
    {
        if ( is_int( $timestamp = strtotime( $modified ) ) )
        {
            return $timestamp;
        }
        throw new RuntimeException( "Cannot parse modified since: {$modified}" );
    }

    protected function extractPhpunitArguments( array $argv )
    {
        $remove = array(
            '--coverage-clover',
            '--coverage-html',
            '--temp-dir',
            '--modified-since'
        );

        $arguments = array();
        for ( $i = 1; $i < count( $argv ); ++$i )
        {
            if ( in_array( $argv[$i], $remove ) )
            {
                ++$i;
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

        echo PHP_EOL, 'Collecting commits and meta data, this may take a moment.', 
             PHP_EOL,
             PHP_EOL;

        $xdebug = new PHP_ChangeCoverage_Xdebug();

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
            echo 'Writing change coverage data to XML file, this may take a moment.',
                 PHP_EOL,
                 PHP_EOL;

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
            echo 'Writing change coverage report, this may take a moment.',
                 PHP_EOL,
                 PHP_EOL;

            $html = new PHP_CodeCoverage_Report_HTML(
                array(
                    'title'      =>  'Coverage Report for files modified since ' . date( 'Y/m/d', $this->modifiedSince ),
                    'highlight'  =>  true,
                    'yui'        =>  false,
                    'generator'  =>  ' <a href="http://qafoo.com">ChangeCoverage</a>'
                )
            );
            $html->process( $coverage, $this->coverageHtml );
        }
    }
}