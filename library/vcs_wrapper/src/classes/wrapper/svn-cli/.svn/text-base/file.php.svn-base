<?php
/**
 * PHP VCS wrapper SVN Cli file wrapper
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
 * File implementation vor SVN Cli wrapper
 */
class vcsSvnCliFile extends vcsSvnCliResource implements vcsFile, vcsBlameable, vcsFetchable
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
        $mimeType = $this->getResourceProperty( 'mime-type' );

        if ( !empty( $mimeType ) )
        {
            return $mimeType;
        }

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
            $process = new vcsSvnCliProcess();
            $process->argument( '--xml' );

            // Execute command
            $return = $process->argument( 'blame' )->argument( $this->root . $this->path )->execute();
            $xml = arbitXml::loadString( $process->stdoutOutput );

            // Check if blame information si available. Is absent fro binary
            // files.
            if ( !$xml->target )
            {
                return false;
            }

            $blame = array();
            $contents = preg_split( '(\r\n|\r|\n)', $this->getVersionedContent( $version ) );
            foreach ( $xml->target[0]->entry as $nr => $entry )
            {
                $blame[] = new vcsBlameStruct(
                    $contents[$nr],
                    $entry->commit[0]['revision'],
                    $entry->commit[0]->author,
                    strtotime( $entry->commit[0]->date )
                );
            }

            vcsCache::cache( $this->path, $version, 'blame', $blame );
        }

        return $blame;
    }

    /**
     * @inheritdoc
     */
    public function getVersionedContent( $version )
    {
        if ( !in_array( $version, $this->getVersions(), true ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        if ( ( $content = vcsCache::get( $this->path, $version, 'content' ) ) === false )
        {
            // Refetch the basic content information, and cache it.
            $process = new vcsSvnCliProcess();
            $process->argument( '-r' . $version );

            // Execute command
            $return = $process->argument( 'cat' )->argument( $this->root . $this->path )->execute();
            vcsCache::cache( $this->path, $version, 'content', $content = $process->stdoutOutput );
        }

        return $content;
    }
}

