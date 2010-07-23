<?php
/**
 * PHP VCS wrapper Bzr Cli resource wrapper
 *
 * This file is part of vcs-wrapper.
 *
 * vcs-wrapper is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * vcs-wrapper is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with vcs-wrapper; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package VCSWrapper
 * @subpackage BzrCliWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Resource implementation vor Bzr Cli wrapper
 *
 * @package VCSWrapper
 * @version $Revision$
 **/
abstract class vcsBzrCliResource extends vcsResource implements vcsVersioned, vcsAuthored, vcsLogged, vcsDiffable
{
    /**
     * Current version of the given resource
     * 
     * @var string
     */
    protected $currentVersion = null;

    /**
     * Returns the latest information about this resource
     *
     * Get the base information, like version, author, etc for the current
     * resource in the current version.
     *
     * @return vcsLogEntry
     */
    protected function getResourceInfo() 
    {
        if ($this->currentVersion !== null) {
            $info = vcsCache::get( $this->path, $this->currentVersion, 'info' );
        }
        if ($this->currentVersion === null || $info === false) {
            $log = $this->getResourceLog();

            // Fecth for specified version, if set
            $info = $this->currentVersion !== null ? $log[$this->currentVersion] : end( $log );
            vcsCache::cache( $this->path, $this->currentVersion = (string) $info->version, 'info', $info );
        }

        return $info;
    }

    /**
     * Returns the complete log for this resource.
     *
     * @return array
     */
    protected function getResourceLog() 
    {
        $log = vcsCache::get( $this->path, $this->currentVersion, 'log' );
        if ($log === false) {
            // Refetch the basic logrmation, and cache it.
            $process = new vcsBzrCliProcess();
            $process->workingDirectory( $this->root );

            // Execute log command
            $process->argument( 'xmllog' );
            $process->argument( '--forward' ); // why reverse it when we can get them in order anyway?
            $process->argument( '-q' ); // quiet, don't show extra status stuff
            // Fetch for specified version, if set
            if ( $this->currentVersion !== null ) {
                $process->argument( '-r ' . $this->currentVersion );
            }

            $process->argument( '.' . $this->path );
            
            $process->execute();

            // Parse commit log
            $xmlDoc = new SimpleXMLElement($process->stdoutOutput);

            $lineCount  = count( $xmlDoc->log );
            $log        = array();
            $lastCommit = null;
            foreach( $xmlDoc->log AS $entry ) {
                $revno = $entry->revno;
                $author = $entry->committer;
                $date = strtotime($entry->timestamp);
                $desc = $entry->message;
                
                $newEntry = new vcsLogEntry( $revno, $author, $desc, $date );
                //print_r($newEntry);
                $log[(string)$revno] = $newEntry;
            }
            $last = end( $log );

            $this->currentVersion = (string) $last->version;
            // Cache extracted data
            vcsCache::cache( $this->path, $this->currentVersion, 'log', $log );
        }

        return $log;
    }

    /**
     * Returns a property of this resource.
     *
     * This method is not implemented because mercurial has no properties.
     *
     * @param string $property
     * @return string
     */
    protected function getResourceProperty( $property ) 
    {
        $property; // stupid, but surpresses phpcs warnings...
        return '';
    }

    /**
     * Returns the current version.
     *
     * @return string
     */
    public function getVersionString() 
    {
        $info = $this->getResourceInfo();
        return $info->version;
    }

    /**
     * Returns all version for this resource.
     *
     * @return array
     */
    public function getVersions() 
    {
        $versions = array();
        $log = $this->getResourceLog();

        foreach ( $log as $entry )
        {
            $versions[] = (string) $entry->version;
        }

        return $versions;
    }

    /**
     * Compares two versions.
     *
     * Returns -1 if the first version is lower than the second, 0 if they are 
     * equal, and 1 if the second is lower.
     *
     * @param string $version1
     * @param string $version2
     * @return integer 
     */
    public function compareVersions( $version1, $version2 ) 
    {
        $versions = $this->getVersions();
        $key1 = array_search( $version1, $versions );
        $key2 = array_search( $version2, $versions );

        if ($key1 === false || $key2 === false)
        {
            return 0;
        }

        return $key1 - $key2;
    }

    /**
     * Returns the author for the given resource.
     *
     * @param string $version
     * @return string
     */
    public function getAuthor( $version = null ) 
    {
        $version = $version === null ? $this->getVersionString() : $version;
        $log = $this->getResourceLog();

        if ( !isset( $log[$version] ) ) {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        return $log[$version]->author;
    }

    /**
     * Returns the resource log for this resource.
     *
     * @return string
     */
    public function getLog() 
    {
        return $this->getResourceLog();
    }

    /**
     * Returns the log entry for he given version.
     *
     * @param string $version
     * @return string
     */
    public function getLogEntry( $version ) 
    {
        $log = $this->getResourceLog();

        if ( !isset( $log[$version] ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        return $log[$version];
    }
    /**
     * Returns the diff between two different versions.
     *
     * @param string $version
     * @param string $current
     * @return vcsDiff
     */
    public function getDiff( $version, $current = null )
    {
        if ( !in_array( $version, $this->getVersions(), true ) ) {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        $diff = vcsCache::get( $this->path, $version, 'diff' );
        if ($diff === false) {
            // Refetch the basic content information, and cache it.
            $process = new vcsBzrCliProcess();
            $process->workingDirectory( $this->root );
            $process->argument( 'diff' );
            if ($current !== null) {
                $process->argument( '-r' . $version . ".." . $current );
            } else {
                $process->argument( "-r" . $version );
            }
            $process->argument( '.' . $this->path );
            try {
                $process->execute();
            } catch ( pbsSystemProcessNonZeroExitCodeException $e ) {
                print_r($e);
                print_r($process);
            }

            // Parse resulting unified diff
            $parser = new vcsUnifiedDiffParser();
            $diff   = $parser->parseString( $process->stdoutOutput );
            vcsCache::cache( $this->path, $version, 'diff', $diff );
        }

        return $diff;
    }
}

