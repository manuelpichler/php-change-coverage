#!/usr/bin/env php
<?php

define( 'PHP_CHANGE_COVERAGE_ROOT', dirname( __FILE__ ) . '/source' );

function __autoload( $className )
{
    if ( strpos( $className, 'PHP_ChangeCoverage' ) === 0 )
    {
        include PHP_CHANGE_COVERAGE_ROOT . '/' . strtr( $className, '_', '/') . '.php';
    }
}

$arguments = $_SERVER['argv'];
array_shift( $arguments );

if ( false === ($offset = array_search( '--coverage-clover', $arguments ) ) )
{
    $cloverFile = sys_get_temp_dir() . '/~' . uniqid( 'clover' ) . '.xml';

    array_unshift( $arguments, '--coverage-clover', $cloverFile );
}
else
{
    $cloverFile = $arguments[$offset + 1];
}


$arguments = array_map( 'escapeshellarg', $arguments );

$memory = memory_get_peak_usage();

$command = 'phpunit ' . join( ' ', $arguments );

passthru( $command, $code );

$timeRange = time() - ( 60 * 86400 );

if ( file_exists( $cloverFile ) )
{
    require_once 'PHP/CodeCoverage.php';

    $codeCoverage = new PHP_CodeCoverage();
    

    $factory = new PHP_ChangeCoverage_VcsFactory( dirname( __FILE__ ) . '/library/vcs_wrapper/src/classes' );
    vcsCache::initialize( sys_get_temp_dir() . '/cc-cache' );

    $sxml = simplexml_load_file( $cloverFile );
    if ( isset( $sxml->project->file[0] ) )
    {
        $xdebug = array();
        foreach ( $sxml->project->file as $file )
        {
            $lines = array();
            foreach ( $file->line as $line )
            {
                $lines[(int) $line['num']] = array(
                    'type'     =>  (string) $line['type'],
                    'count'    =>  (int) $line['count'],
                    'changes'  =>  0
                );
            }

            $commits = array();

            $vcsFile = $factory->create( (string) $file['name'] );
            foreach ( $vcsFile->getLog() as $log )
            {
                if ( $log->date >= $timeRange )
                {
                    $commits[] = $log->version;
                }
            }

            if ( count( $commits ) > 0 )
            {
                foreach ( $commits as $version )
                {
                    foreach ( $vcsFile->blame( $version ) as $line => $struct )
                    {
                        if ( $struct->date >= $timeRange )
                        {
                            if ( isset( $lines[$line + 1] ) )
                            {
                                ++$lines[$line + 1]['changes'];
                            }
                        }
                    }
                }

                $xdebug[(string) $file['name']] = array();
                foreach ( $lines as $idx => $line )
                {
                    $xdebug[(string) $file['name']][] = $line['count'];

                    if ( $line['changes'] === 0 )
                    {
                        $lines[$idx] = -2;
                    }
                    else
                    {
echo (string) $file['name'], ':', $idx, ' - ', $line['count'], PHP_EOL;
                        $lines[$idx] = $line['count'];
                    }
                }
            }
        }
    }

    $codeCoverage->append( $xdebug, 42 );

    require_once 'PHP/CodeCoverage/Report/Clover.php';
    require_once 'PHP/CodeCoverage/Report/HTML.php';

    $html = new PHP_CodeCoverage_Report_HTML();
    $html->process( $codeCoverage, '/tmp/coverage' );

    $clover = new PHP_CodeCoverage_Report_Clover();
    echo $clover->process( $codeCoverage );
}



echo 'Exit code: ', $code, PHP_EOL;