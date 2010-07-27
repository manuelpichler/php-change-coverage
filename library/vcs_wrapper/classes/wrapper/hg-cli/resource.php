<?php
/**
 * PHP VCS wrapper Hg Cli resource wrapper
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
 * @subpackage HgCliWrapper
 * @version $Revision: 1859 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Resource implementation vor Hg Cli wrapper
 *
 * @package VCSWrapper
 * @version $Revision: 1859 $
 **/
abstract class vcsHgCliResource extends vcsResource implements vcsVersioned, vcsAuthored, vcsLogged
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
        if ( $this->currentVersion !== null )
        {
            $info = vcsCache::get( $this->path, $this->currentVersion, 'info' );
        }

        if ( $this->currentVersion === null ||
             $info === false )
        {
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
        if ( $log === false )
        {
            // Refetch the basic logrmation, and cache it.
            $process = new vcsHgCliProcess();
            $process->workingDirectory( $this->root );

            // Fetch for specified version, if set
            if ( $this->currentVersion !== null )
            {
                $process->argument( '-r ' . $this->currentVersion );
            }

            // Execute log command
            $process->argument( 'log' );
            $process->argument( '--template' )->argument( '{node}\t{author|email}\t{date|isodate}\t{desc|urlescape}\n' );
            $process->argument( new pbsPathArgument( '.' . $this->path ) );
            $process->execute();

            // Parse commit log
            $lines = explode( "\n", $process->stdoutOutput );
            if ( !$lines )
            {
                return array();
            }

            $lineCount  = count( $lines );
            $log        = array();
            $lastCommit = null;
            foreach( $lines as $line )
            {
                if ( !$line )
                {
                    continue;
                }

                list( $node, $author, $date, $desc ) = explode( "\t", $line, 4 );
                
                $atPosition = strpos( $author, '@' );
                if ( $atPosition )
                {
                    $author = substr( $author, 0, $atPosition );
                }
                
                $log[$node] = new vcsLogEntry( $node, $author, urldecode( $desc ), strtotime( $date ) );
            }
            $log = array_reverse( $log );
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
     * Get version string
     *
     * Return a string representing the current version of the file or
     * directory.
     * 
     * @return string
     */
    public function getVersionString() 
    {
        $info = $this->getResourceInfo();
        return $info->version;
    }

    /**
     * Get available versions
     *
     * Get all available versions for the current resource. This method
     * returns an array with all version strings.
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
     * Compare two version strings
     *
     * If $version1 is lower then $version2, an integer < 0, will be returned.
     * In case $version1 is bigger / later then $version2 an integer > 0 will
     * be returned. In case both versions are equal 0 will be returned.
     *
     * @param string $version1 
     * @param string $version2 
     * @return int
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
     * Get author 
     *
     * Return author information for the resource. Optionally the $version
     * parameter may be passed to the method to specify a version the author
     * information should be returned for.
     *
     * @param mixed $version 
     * @return string
     */
    public function getAuthor( $version = null ) 
    {
        $version = $version === null ? $this->getVersionString() : $version;
        $log = $this->getResourceLog();

        if ( !isset( $log[$version] ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        return $log[$version]->author;
    }

    /**
     * Get full revision log
     *
     * Return the full revision log for the given resource. The revision log
     * should be returned as an array of vcsLogEntry objects.
     *
     * @return array
     */
    public function getLog() 
    {
        return $this->getResourceLog();
    }

    /**
     * Get revision log entry
     *
     * Get the revision log entry for the spcified version.
     * 
     * @param string $version
     * @return vcsLogEntry
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
}

