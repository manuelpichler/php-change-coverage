<?php
/**
 * PHP VCS wrapper SVN Ext resource wrapper
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
 * @subpackage SvnExtWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Resource implementation vor SVN Ext wrapper
 */
abstract class vcsSvnExtResource extends vcsResource implements vcsVersioned, vcsAuthored, vcsLogged, vcsDiffable
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
            // Fecth for specified version, if set
            if ( $this->currentVersion !== null )
            {
                $info = svn_info( $this->root . $this->path, $this->currentVersion );
            }
            else
            {
                $info = svn_info( $this->root . $this->path );
            }

            $info = $info[0];
            vcsCache::cache( $this->path, $this->currentVersion = (string) $info['last_changed_rev'], 'info', $info );
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
            $svnLog = svn_log( $this->root . $this->path );

            $log = array();
            foreach ( $svnLog as $nr => $entry )
            {
                $log[$entry['rev']] = new vcsLogEntry(
                    $entry['rev'],
                    $entry['author'],
                    $entry['msg'],
                    strtotime( $entry['date'] )
                );
            }
            uksort( $log, array( $this, 'compareVersions' ) );
            $last = end( $log );

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
        // There currently seems no way to get the property contents inside a
        // checkout.
        return null;

        if ( ( $value = vcsCache::get( $this->path, $this->currentVersion, $property ) ) === false )
        {
            $rep   = svn_repos_open( $this->root );
            $value = svn_fs_node_prop( $rep, $this->path, 'svn:' . $property );
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
        return (string) $info['last_changed_rev'];
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
            list( $diffStream, $errors ) = svn_diff( $this->root . $this->path, $version, $this->root . $this->path, $current );
            $diffContents = '';
            while ( !feof( $diffStream ) )
            {
                $diffContents .= fread( $diffStream, 8192);
            }
            fclose( $diffStream );

            // Execute command
            $parser = new vcsUnifiedDiffParser();
            $diff   = $parser->parseString( $diffContents );
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

