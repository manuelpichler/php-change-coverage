<?php
/**
 * PHP VCS wrapper SVN Ext file wrapper
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
 * File implementation vor SVN Ext wrapper
 */
class vcsSvnExtFile extends vcsSvnExtResource implements vcsFile, vcsBlameable, vcsFetchable
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
            // Silence warning about binary files, and just use the return
            // value. There is no good way to know this beforehand. The
            // mime-type might be an indicator, but the list of possible
            // "binary" mime types is to long to really check for that.
            $svnBlame = @svn_blame( $this->root . $this->path );

            if ( ( $blame = $svnBlame ) !== false )
            {
                $blame = array();
                foreach ( $svnBlame as $entry )
                {
                    $blame[] = new vcsBlameStruct(
                        $entry['line'],
                        $entry['rev'],
                        $entry['author'],
                        strtotime( $entry['date'] )
                    );
                }
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
            // Execute command
            $content = svn_cat( $this->root . $this->path, $version );
            vcsCache::cache( $this->path, $version, 'content', $content );
        }

        return $content;
    }
}

