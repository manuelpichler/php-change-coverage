<?php
/**
 * PHP VCS wrapper Hg Cli file wrapper
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
 * @subpackage MercurialCliWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * File implementation vor Mercurial Cli wrapper
 *
 * @package VCSWrapper
 * @subpackage MercurialCliWrapper
 * @version $Revision$
 */
class vcsHgCliFile extends vcsHgCliResource implements vcsFile, vcsBlameable, vcsDiffable
{
    /**
     * Returns the contents of this file
     *
     * @return string
     */
    public function getContents()
    {
        return file_get_contents( $this->root . $this->path );
    }

    /**
     * Returns the mimetype for this file.
     *
     * @return string Mimetype of this file
     */
    public function getMimeType()
    {
        // If not set, fall back to application/octet-stream
        return 'application/octet-stream';
    }

    /**
     * Returns blame information for each line in file.
     *
     * @param string $version
     * @return array(vcsBlameStruct)
     */
    public function blame( $version = null )
    {
        $version = ( $version === null ) ? $this->getVersionString() : $version;

        if ( !in_array( $version, $this->getVersions(), true ) ) {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        $blame = vcsCache::get( $this->path, $version, 'blame' );
        if ( $blame === false ) {
            $shortHashCache = array();

            // Refetch the basic blamermation, and cache it.
            $process = new vcsHgCliProcess();
            $process->workingDirectory( $this->root );

            // Execute command
            $process->argument( 'blame' );
            $process->argument( '-uvdcl' );
            $process->argument( '.' . $this->path );
            $return = $process->execute();
            $contents = preg_split( '(\r\n|\r|\n)', trim( $process->stdoutOutput ) );

            // Convert returned lines into diff structures
            $blame = array();
            foreach ( $contents AS $line ) {
                if ( !$line ) {
                    continue;
                }

                $emailEndPos = strpos( $line, '>' );
                if ( !$emailEndPos ) {
                    // todo: implement better parsing for the blame line
                    throw new vcsRuntimeException( "Could not parse line: $line" );
                }
                $user = substr( $line, 0, $emailEndPos + 1 );
                $shortHash = substr( $line, $emailEndPos + 2, 12 );

                $date = substr( $line, $emailEndPos + 15, 30 );
                $line = substr( $line, $emailEndPos + 46 );

                $linePositionEnd = strpos( $line, ':' );
                $lineNumber = substr( $linePositionEnd, 0, $linePositionEnd );

                $line = trim( substr( $line, $linePositionEnd + 1 ) );

                if ( !isset( $shortHashCache[ $shortHash ] ) ) {
                    // get the long revision from the short revision number
                    $process = new vcsHgCliProcess();
                    $process->workingDirectory( $this->root );
                    $process->argument( 'id' );
                    $process->argument( '--debug' );
                    $process->argument( '-r' . $shortHash );
                    $process->execute();

                    $result = trim( $process->stdoutOutput );
                    $spacePosition = strpos( $result, ' ' );
                    // if there is a space inside the revision, we have additional tags
                    // and we really want to remove them from the revision number
                    if ( $spacePosition ) {
                        $result = substr( $result, 0, $spacePosition );
                    }

                    $shortHashCache[ $shortHash ] = $result;
                }
                // get the long revision from the cache
                $revision = $shortHashCache[ $shortHash ];

                // lets start the little work. we need the alias part of the
                // email inside the username
                $emailStart = strrpos( $user, '<' );
                $emailEnd = strrpos( $user, '>' );
                // start and end of email, now lets get it from the username
                $email = substr( $user, $emailStart + 1, $emailEnd - $emailStart );
                // alias and domain part, should be separated with @.
                // todo: we might want to check if there is really a @.
                list( $alias, $domain ) = explode( '@', $email );

                $blame[] = new vcsBlameStruct( $line, $revision, $alias, strtotime( $date ) );
            }

            vcsCache::cache( $this->path, $version, 'blame', $blame );
        }

        return $blame;
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
            $process = new vcsHgCliProcess();
            $process->workingDirectory( $this->root );
            $process->argument( 'diff' );
            if ($current !== null) {
                $process->argument( '-r' . $current );
            }
            $process->argument( '-r' . $version );
            $process->argument( '.' . $this->path );
            $process->execute();

            // Parse resulting unified diff
            $parser = new vcsUnifiedDiffParser();
            $diff   = $parser->parseString( $process->stdoutOutput );
            vcsCache::cache( $this->path, $version, 'diff', $diff );
        }

        return $diff;
    }
}

