<?php
class PHP_ChangeCoverage_Report_Clover implements PHP_ChangeCoverage_Report
{
    /**
     * Simple xml representation of the clover coverage report.
     *
     * @var SimpleXMLElement
     */
    private $sxml = null;

    /**
     * Constructs a new clover coverage report instance.
     *
     * @param SimpleXMLElement $sxml Simple xml representation of the clover report.
     */
    public function __construct( SimpleXMLElement $sxml )
    {
        $this->sxml = $sxml;
    }

    public function getFiles()
    {
        return new PHP_ChangeCoverage_Report_CloverFileIterator( $this->sxml->project->file );
    }
}

class PHP_ChangeCoverage_Report_CloverFileIterator extends IteratorIterator
{
    public function current()
    {
        if ( is_object( $file = parent::current() ) )
        {
            $lines = array();
            foreach ( $file->line as $line )
            {
                if ( 'method' === (string) $line['type'] )
                {
                    continue;
                }
                $lines[] = new PHP_ChangeCoverage_Source_Line( (int) $line['num'], (int) $line['count'] );
            }
            return new PHP_ChangeCoverage_Source_File( (string) $file['name'], $lines );
        }
        return null;
    }
}