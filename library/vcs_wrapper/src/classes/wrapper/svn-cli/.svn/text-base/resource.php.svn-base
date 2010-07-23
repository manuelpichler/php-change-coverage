<?php
/**
 * PHP VCS wrapper SVN Cli resource wrapper
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
 * @subpackage SvnCliWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Resource implementation vor SVN Cli wrapper
 */
abstract class vcsSvnCliResource extends vcsResource implements vcsVersioned, vcsAuthored, vcsLogged, vcsDiffable
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
            // Refetch the basic information, and cache it.
            $process = new vcsSvnCliProcess();
            $process->argument( '--xml' );

            // Fetch for specified version, if set
            if ( $this->currentVersion !== null )
            {
                $process->argument( '-r' . $this->currentVersion );
            }

            // Execute info command
            $return = $process->argument( 'info' )->argument( $this->root . $this->path )->execute();

            $info = arbitXml::loadString( $process->stdoutOutput );
            vcsCache::cache( $this->path, $this->currentVersion = (string) $info->entry[0]->commit[0]['revision'], 'info', $info );
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
            $process = new vcsSvnCliProcess();
            $process->argument( '--xml' );

            // Fecth for specified version, if set
            if ( $this->currentVersion !== null )
            {
                $process->argument( '-r0:' . $this->currentVersion );
            }

            // Execute logr command
            $return = $process->argument( 'log' )->argument( $this->root . $this->path )->execute();

            // Transform XML into object array
            $xmlLog = arbitXml::loadString( $process->stdoutOutput );
            $log    = array();
            foreach ( $xmlLog->logentry as $entry )
            {
                $log[(string) $entry['revision']] = new vcsLogEntry(
                    $entry['revision'],
                    $entry->author,
                    $entry->msg,
                    strtotime( $entry->date )
                );
            }
            uksort( $log, array( $this, 'compareVersions' ) );
            $last = end( $log );

            // Cache extracted data
            vcsCache::cache( $this->path, $this->currentVersion = (string) $last->version, 'log', $log );
        }

        return $log;
    }

    /**
     * Get resource property
     *
     * Get the value of an SVN property
     *
     * @return string
     */
    protected function getResourceProperty( $property )
    {
        if ( ( $value = vcsCache::get( $this->path, $this->currentVersion, $property ) ) === false )
        {
            // Refetch the basic mimeTypermation, and cache it.
            $process = new vcsSvnCliProcess();

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
        if ( $this->currentVersion === null )
        {
            $this->getResourceInfo();
        }

        return $this->currentVersion;
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
        return $version1 - $version2;
    }

    /**
     * @inheritdoc
     */
    public function getAuthor( $version = null )
    {
        if ( $version === null )
        {
            $info = $this->getResourceInfo();
            return (string) $info->entry[0]->commit[0]->author;
        }

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
        $current = ( $current === null ) ? $this->getVersionString() : $current;

        if ( ( $diff = vcsCache::get( $this->path, $version, 'diff' ) ) === false )
        {
            // Refetch the basic content information, and cache it.
            $process = new vcsSvnCliProcess();
            $process->argument( '-r' . $version . ':' . $current );

            // Execute command
            $return = $process->argument( 'diff' )->argument( $this->root . $this->path )->execute();
            $parser = new vcsUnifiedDiffParser();
            $diff   = $parser->parseString( $process->stdoutOutput );
            vcsCache::cache( $this->path, $version, 'diff', $diff );
        }

        foreach ( $diff as $fileDiff )
        {
            $fileDiff->from = substr( $fileDiff->from, strlen( $this->root ) );
            $fileDiff->to   = substr( $fileDiff->to, strlen( $this->root ) );
        }

        return $diff;
    }
}

