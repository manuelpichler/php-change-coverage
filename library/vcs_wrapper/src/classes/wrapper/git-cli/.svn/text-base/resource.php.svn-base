<?php
/**
 * PHP VCS wrapper Git Cli resource wrapper
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
 * @subpackage GitCliWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Resource implementation vor Git Cli wrapper
 */
abstract class vcsGitCliResource extends vcsResource implements vcsVersioned, vcsAuthored, vcsLogged, vcsDiffable
{
    /**
     * Current version of the given resource
     *
     * @var string
     */
    protected $currentVersion = null;

    /**
     * Get resource base information
     *
     * Get the base information, like version, author, etc for the current
     * resource in the current version.
     *
     * @return arbitXml
     */
    protected function getResourceInfo()
    {
        if ( ( $this->currentVersion === null ) ||
             ( ( $info = vcsCache::get( $this->path, $this->currentVersion, 'info' ) ) === false ) )
        {
            $log = $this->getResourceLog();

            // Fecth for specified version, if set
            if ( $this->currentVersion !== null )
            {
                $info = $log[$this->currentVersion];
            }
            else
            {
                $info = end( $log );
            }

            vcsCache::cache( $this->path, $this->currentVersion = (string) $info->version, 'info', $info );
        }

        return $info;
    }

    /**
     * Get resource log
     *
     * Get the full log for the current resource up tu the current revision
     *
     * @return arbitXml
     */
    protected function getResourceLog()
    {
        if ( ( $log = vcsCache::get( $this->path, $this->currentVersion, 'log' ) ) === false )
        {
            // Refetch the basic logrmation, and cache it.
            $process = new vcsGitCliProcess();
            $process->workingDirectory( $this->root );

            // Fecth for specified version, if set
            if ( $this->currentVersion !== null )
            {
                $process->argument( '..' . $this->currentVersion );
            }

            // Execute log command
            $process->argument( 'log' )->argument( '--pretty=format:%H;%cn;%ct;%s%n%b' )->argument( '.' . $this->path )->execute();

            // Parse commit log
            $lines      = preg_split( '(\r\n|\r|\n)', $process->stdoutOutput );
            $lineCount  = count( $lines );
            $log        = array();
            $lastCommit = null;
            for ( $i = 0; $i < $lineCount; ++$i )
            {
                if ( preg_match( '(^(?P<version>[0-9a-f]{40});(?P<author>.*);(?P<date>[0-9]+);(?P<message>.*))', $lines[$i], $match ) )
                {
                    $lastCommit = $match['version'];
                    $log[$lastCommit] = new vcsLogEntry( $lastCommit, $match['author'], $match['message'], $match['date'] );
                }
                elseif ( $lastCommit !== null )
                {
                    $log[$lastCommit]->message = $log[$lastCommit]->message . "\n" . $lines[$i];
                }
            }
            $log = array_reverse( $log );
            $last = end( $log );

            // Cache extracted data
            vcsCache::cache( $this->path, $this->currentVersion = (string) $last->version, 'log', $log );
        }

        return $log;
    }

    /**
     * Get resource property
     *
     * Get the value of an Git property
     *
     * @return string
     */
    protected function getResourceProperty( $property )
    {
        if ( ( $value = vcsCache::get( $this->path, $this->currentVersion, $property ) ) === false )
        {
            // Refetch the basic mimeTypermation, and cache it.
            $process = new vcsGitCliProcess();

            // Fecth for specified version, if set
            if ( $this->currentVersion !== null )
            {
                $process->argument( '-r' . $this->currentVersion );
            }

            // Execute mimeTyper command
            $return = $process->argument( 'propget' )->argument( 'svn:' . $property )->argument( $this->root . $this->path )->execute();

            $value = trim( $process->stdoutOutput );
            vcsCache::cache( $this->path, $this->currentVersion, $property, $value );
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getVersionString()
    {
        $info = $this->getResourceInfo();
        return $info->version;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function compareVersions( $version1, $version2 )
    {
        $versions = $this->getVersions();

        if ( ( ( $key1 = array_search( $version1, $versions ) ) === false ) ||
             ( ( $key2 = array_search( $version2, $versions ) ) === false ) )
        {
            return 0;
        }

        return $key1 - $key2;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getLog()
    {
        return $this->getResourceLog();
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getDiff( $version, $current = null )
    {
        if ( !in_array( $version, $this->getVersions(), true ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        $current = ( $current === null ) ? $this->getVersionString() : $current;

        if ( ( $diff = vcsCache::get( $this->path, $version, 'diff' ) ) === false )
        {
            // Refetch the basic content information, and cache it.
            $process = new vcsGitCliProcess();
            $process->workingDirectory( $this->root );
            $process->argument( 'diff' )->argument( '--no-ext-diff' );
            $process->argument( $version . '..' . $current )->argument( '.' . $this->path )->execute();

            // Parse resulting unified diff
            $parser = new vcsUnifiedDiffParser();
            $diff   = $parser->parseString( $process->stdoutOutput );
            vcsCache::cache( $this->path, $version, 'diff', $diff );
        }

        return $diff;
    }
}

