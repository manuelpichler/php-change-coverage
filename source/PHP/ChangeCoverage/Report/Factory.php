<?php
class PHP_ChangeCoverage_Report_Factory
{
    public function createReport( $fileName )
    {
        if ( false === file_exists( $fileName ) )
        {
            throw new RuntimeException( "File '{$fileName} does not exist." );
        }

        $sxml = simplexml_load_file( $fileName );
        if ( isset( $sxml->project ) )
        {
            return new PHP_ChangeCoverage_Report_Clover( $sxml );
        }
        throw new RuntimeException( '' );
    }
}