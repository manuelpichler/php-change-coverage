<?php
class PHP_ChangeCoverage_ChangeSet_VcsFileStub implements vcsFile, vcsBlameable
{
    public $logs = array();

    public $blame = array();

    public function getMimeType()
    {
        throw new ErrorException( __METHOD__ . '() not used.' );
    }

    public function getContents()
    {
        throw new ErrorException( __METHOD__ . '() not used.' );
    }

    public function getAuthor( $version = null )
    {
        throw new ErrorException( __METHOD__ . '() not used.' );
    }

    public function getVersions()
    {
        throw new ErrorException( __METHOD__ . '() not used.' );
    }

    public function getVersionString()
    {
        throw new ErrorException( __METHOD__ . '() not used.' );
    }

    public function compareVersions( $version1, $version2 )
    {
        throw new ErrorException( __METHOD__ . '() not used.' );
    }

    public function getLog()
    {
        return $this->logs;
    }

    public function blame( $version = null )
    {
        return $this->blame;
    }
}