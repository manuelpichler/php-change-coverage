<?php
/**
 * PHP VCS wrapper CVS Cli resource wrapper
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
 * @subpackage CvsCliWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Resource implementation vor CVS Cli wrapper
 */
abstract class vcsCvsCliResource extends vcsResource implements vcsVersioned, vcsAuthored, vcsLogged
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
     * @return vcsLogEntry
     */
    protected function getResourceInfo()
    {
        if ( ( $this->currentVersion !== null ) &&
             ( ( $info = vcsCache::get( $this->path, $this->currentVersion, 'info' ) ) !== false ) )
        {
            return $info;
        }

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

        $this->currentVersion = $info->version;
        vcsCache::cache( $this->path, $this->currentVersion, 'info', $info );

        return $info;
    }

    /**
     * Get resource log
     *
     * Get the full log for the current resource up tu the current revision
     *
     * @return array(vcsLogEntry)
     */
    protected function getResourceLog()
    {
        if ( ( $log = vcsCache::get( $this->path, $this->currentVersion, 'log' ) ) !== false )
        {
            return $log;
        }

        $version = $this->currentVersion !== null ? $this->currentVersion : 'HEAD';

        $process = new vcsCvsCliProcess();
        $process->workingDirectory( $this->root )
                ->redirect( vcsCvsCliProcess::STDERR, vcsCvsCliProcess::STDOUT )
                ->argument( 'log' )
                ->argument( '-r:' . $version )
                ->argument( '.' . $this->path )
                ->execute();


        $log = array();

        $regexp = '((?# Get revision number )
                   revision\s+(?P<revision>[\d\.]+)(\r\n|\r|\n)
                   (?# Get commit date )
                   date:\s+(?P<date>[^;]+);\s+
                   (?# Get commit author )
                   author:\s+(?P<author>[^;]+);\s+
                   (?# Skip everything else )
                   [^\n\r]+;(\r\n|\r|\n)
                   (branches:\s+[^\n\r]+;(\r\n|\r|\n))?
                   (?# Get commit message )
                   (?P<message>[\r\n\t]*|.*)$)xs';

        // Remove closing equal characters
        $output = rtrim( substr( rtrim( $process->stdoutOutput ), 0, -77 ) );
        // Split all log entries
        $rawLogs = explode( '----------------------------', $output );
        $rawLogs = array_map( 'trim', $rawLogs );
        foreach ( $rawLogs as $rawLog )
        {
            if ( preg_match( $regexp, $rawLog, $match ) === 0 )
            {
                continue;
            }

            $date     = strtotime( $match['date'] );
            $revision = $match['revision'];
            $logEntry = new vcsLogEntry( $revision, $match['author'], $match['message'], $date );

            $log[$revision] = $logEntry;
        }

        $log  = array_reverse( $log );
        $last = end( $log );

        $this->currentVersion = $last->version;
        vcsCache::cache( $this->path, $this->currentVersion, 'log', $log );

        return $log;
    }

    /**
     * Get resource property
     *
     * Get the value of an CVS property
     *
     * @return string
     */
    protected function getResourceProperty( $property )
    {

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
        if ( version_compare( $version1, $version2, 'eq' ) === true )
        {
            return 0;
        }
        else if ( version_compare( $version1, $version2, 'gt' ) === true )
        {
            return 1;
        }

        return -1;
    }

    /**
     * @inheritdoc
     */
    public function getAuthor( $version = null )
    {
        if ( $version === null )
        {
            return $this->getResourceInfo()->author;
        }

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
}

