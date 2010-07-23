<?php
/**
 * PHP VCS wrapper Git Cli file wrapper
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
 * File implementation vor Git Cli wrapper
 */
class vcsGitCliFile extends vcsGitCliResource implements vcsFile, vcsBlameable, vcsDiffable
{
    /**
     * @inheritdoc
     */
    public function getContents()
    {
        return file_get_contents( $this->root . $this->path );
    }

    /**
     * @inheritdoc
     */
    public function getMimeType()
    {
        // If not set, fall back to application/octet-stream
        return 'application/octet-stream';
    }

    /**
     * @inheritdoc
     */
    public function blame( $version = null )
    {
        $version = ( $version === null ) ? $this->getVersionString() : $version;

        if ( !in_array( $version, $this->getVersions(), true ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        if ( ( $blame = vcsCache::get( $this->path, $version, 'blame' ) ) === false )
        {
            // Refetch the basic blamermation, and cache it.
            $process = new vcsGitCliProcess();
            $process->workingDirectory( $this->root );

            // Execute command
            $return = $process->argument( 'blame' )->argument('-l')->argument( '.' . $this->path )->execute();
            $contents = preg_split( '(\r\n|\r|\n)', trim( $process->stdoutOutput ) );

            // Convert returned lines into diff structures
            $blame = array();
            foreach ( $contents as $nr => $line )
            {
                if ( preg_match( '{^\^?(?P<version>[0-9a-f]{1,40})[^(]+\((?P<author>\S*)\s+(?P<date>.*)\s+(?P<number>\d+)\) (?P<line>.*)}', $line, $match ) )
                {
                    $blame[] = new vcsBlameStruct( $match['line'], $match['version'], $match['author'], strtotime( $match['date'] ) );
                }
                else
                {
                    throw new vcsRuntimeException( "Could not parse line: $line" );
                }
            }

            vcsCache::cache( $this->path, $version, 'blame', $blame );
        }

        return $blame;
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

