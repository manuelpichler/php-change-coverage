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
 * @version $Revision: 1860 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * File implementation vor Mercurial Cli wrapper
 *
 * @package VCSWrapper
 * @subpackage MercurialCliWrapper
 * @version $Revision: 1860 $
 */
class vcsHgCliFile extends vcsHgCliResource implements vcsFile, vcsBlameable, vcsDiffable
{
    /**
     * Regexp to parse a mercurial blame line.
     */
    const BLAME_REGEXP = '(
        (?P<user>.*)\s+
        (?P<hash>[a-z0-9]+)\s+
        (?P<date>[a-z]{3}\s[a-z]{3}\s\d{1,2}\s\d{2}:\d{2}:\d{2}\s\d{4}\s\+\d{4}):
        (?P<line>\d+):\s+
        (?P<data>.*)
    )ix';
    
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
     * Get mime type
     * 
     * Get the mime type of the current file. If this information is not
     * available, just return 'application/octet-stream'.
     * 
     * @return string
     */
    public function getMimeType()
    {
        // If not set, fall back to application/octet-stream
        return 'application/octet-stream';
    }

    /**
     * Get blame information for resource
     *
     * The method should return author and revision information for each line,
     * describing who when last changed the current resource. The returned
     * array should look like:
        
     * <code>
     *  array(
     *      T_LINE_NUMBER => array(
     *          'author'  => T_STRING,
     *          'version' => T_STRING,
     *      ),
     *      ...
     *  );
     * </code>
     *
     * If some file in the repository has no blame information associated, like
     * binary files, the method should return false.
     *
     * Optionally a version may be specified which defines a later version of
     * the resource for which the blame information should be returned.
     *
     * @param mixed $version
     * @return mixed
     */
    public function blame( $version = null )
    {
        $version = ( $version === null ) ? $this->getVersionString() : $version;

        if ( !in_array( $version, $this->getVersions(), true ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        $blame = vcsCache::get( $this->path, $version, 'blame' );
        if ( $blame === false )
        {
            $shortHashCache = array();

            // Refetch the basic blamermation, and cache it.
            $process = new vcsHgCliProcess();
            $process->workingDirectory( $this->root );

            // Execute command
            $process->argument( 'blame' );
            $process->argument( '-uvdcl' );
            $process->argument( new pbsPathArgument( '.' . $this->path ) );
            $return = $process->execute();
            $contents = preg_split( '(\r\n|\r|\n)', trim( $process->stdoutOutput ) );

            // Convert returned lines into diff structures
            $blame = array();
            foreach ( $contents AS $line )
            {
                if ( !$line )
                {
                    continue;
                }

                if ( preg_match( self::BLAME_REGEXP, $line, $match ) === 0 )
                {
                    throw new vcsRuntimeException( "Could not parse line: $line" );
                }

                $user       = $match['user'];
                $date       = $match['date'];
                $line       = $match['data'];
                $shortHash  = $match['hash'];
                $lineNumber = $match['line'];

                if ( !isset( $shortHashCache[ $shortHash ] ) )
                {
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
                    if ( $spacePosition )
                    {
                        $result = substr( $result, 0, $spacePosition );
                    }

                    $shortHashCache[ $shortHash ] = $result;
                }
                // get the long revision from the cache
                $revision = $shortHashCache[ $shortHash ];

                // lets start the little work. we need the alias part of the
                // email inside the username
                if ( preg_match( '(<(?P<alias>\S+)@\S+\.\S+>$)', $user, $match ) )
                {
                    $alias = $match['alias'];
                }
                else
                {
                    $alias = $user;
                }

                $blame[] = new vcsBlameStruct( $line, $revision, $alias, strtotime( $date ) );
            }

            vcsCache::cache( $this->path, $version, 'blame', $blame );
        }

        return $blame;
    }

    /**
     * Get diff
     *
     * Get the diff between the current version and the given version.
     * Optionally you may specify another version then the current one as the
     * diff base as the second parameter.
     *
     * @param string $version 
     * @param string $current 
     * @return vcsResource
     */
    public function getDiff( $version, $current = null )
    {
        if ( !in_array( $version, $this->getVersions(), true ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        $diff = vcsCache::get( $this->path, $version, 'diff' );
        if ( $diff === false )
        {
            // Refetch the basic content information, and cache it.
            $process = new vcsHgCliProcess();
            $process->workingDirectory( $this->root );
            $process->argument( 'diff' );
            if ($current !== null)
            {
                $process->argument( '-r' . $current );
            }
            $process->argument( '-r' . $version );
            $process->argument( new pbsPathArgument( '.' . $this->path ) );
            $process->execute();

            // Parse resulting unified diff
            $parser = new vcsUnifiedDiffParser();
            $diff   = $parser->parseString( $process->stdoutOutput );
            vcsCache::cache( $this->path, $version, 'diff', $diff );
        }

        return $diff;
    }
}


