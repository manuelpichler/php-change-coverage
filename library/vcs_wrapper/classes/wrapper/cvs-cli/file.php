<?php
/**
 * PHP VCS wrapper CVS Cli file wrapper
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
 * @version $Revision: 1859 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * File implementation vor CVS Cli wrapper
 *
 * @package VCSWrapper
 * @subpackage CvsCliWrapper
 * @version $Revision: 1859 $
 */
class vcsCvsCliFile extends vcsCvsCliResource implements vcsFile, vcsBlameable, vcsFetchable, vcsDiffable
{
    /**
     * Get file contents
     * 
     * Get the contents of the current file.
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

        $versions = $this->getVersions();
        if ( !in_array( $version, $versions, true ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        if ( ( $blame = vcsCache::get( $this->path, $version, 'blame' ) ) !== false )
        {
            return $blame;
        }

        // Refetch the basic blamermation, and cache it.
        $process = new vcsCvsCliProcess();
        $process
            ->workingDirectory( $this->root )
            ->redirect( vcsCvsCliProcess::STDERR, vcsCvsCliProcess::STDOUT )
            ->argument( 'annotate' )
            ->argument( '-r' )
            ->argument( $version )
            ->argument( '.' . $this->path )
            ->execute();


        $output   = $process->stdoutOutput;
        $contents = trim( substr( $output, strpos( $output, '***************' ) + 15 ) );
        $contents = preg_split( '(\r\n|\r|\n)', $contents );

        $blame = array();
        foreach ( $contents as $line )
        {
            $regexp = '((?P<revision>[0-9\.]+)\s+\(
                       (?P<author>.*)\s+
                       (?P<date>\S+)\):\s+
                       (?P<content>.*)$)x';

            preg_match( $regexp, $line, $match );

            $blame[] = new vcsBlameStruct(
                trim( $match['content'] ),
                trim( $match['revision'] ),
                trim( $match['author'] ),
                strtotime( $match['date'] )
            );
        }

        vcsCache::cache( $this->path, $version, 'blame', $blame );

        return $blame;
    }

    /**
     * Get content for version
     *
     * Get the contents of the current resource in the specified version.
     *
     * @param string $version 
     * @return string
     */
    public function getVersionedContent( $version )
    {
        $versions = $this->getVersions();
        if ( !in_array( $version, $versions, true ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        if ( ( $content = vcsCache::get( $this->path, $version, 'content' ) ) === false )
        {
            // Refetch the basic content information, and cache it.
            $process = new vcsCvsCliProcess();
            $process
                ->workingDirectory( $this->root )
                ->redirect( vcsCvsCliProcess::STDERR, vcsCvsCliProcess::STDOUT )
                ->argument( 'update' )
                ->argument( '-p' )
                ->argument( '-r' )
                ->argument( $version )
                ->argument( '.' . $this->path )
                ->execute();

            $output  = $process->stdoutOutput;
            $content = ltrim( substr( $output, strpos( $output, '***************' ) + 15 ) );
            vcsCache::cache( $this->path, $version, 'content', $content );
        }

        return $content;
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
        $current = ( $current === null ) ? $this->getVersionString() : $current;

        if ( ( $diff = vcsCache::get( $this->path, $version, 'diff' ) ) !== false )
        {
            return $diff;
        }

        // Refetch the basic content information, and cache it.
        $process = new vcsCvsCliProcess();
        // WTF: Why is there a non zero exit code?
        $process->nonZeroExitCodeException = false;
        // Configure process instance
        $process
            ->workingDirectory( $this->root )
            ->redirect( vcsCvsCliProcess::STDERR, vcsCvsCliProcess::STDOUT )
            ->argument( 'diff' )
            ->argument( '-u' )
            ->argument( '-r' )
            ->argument( $version )
            ->argument( '-r' )
            ->argument( $current )
            ->argument( '.' . $this->path )
            ->execute();

        $parser = new vcsUnifiedDiffParser();
        $diff   = $parser->parseString( $process->stdoutOutput );
        vcsCache::cache( $this->path, $version, 'diff', $diff );

        return $diff;
    }
}

