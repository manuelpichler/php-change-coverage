<?php
/**
 * PHP VCS wrapper ZIP archive based repository wrapper
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
 * @subpackage ArchiveWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Handler for ZIP archive based "checkouts"
 */
class vcsZipArchiveCheckout extends vcsArchiveCheckout
{
    /**
     * Initialize repository
     *
     * Initialize repository from the given URL. Optionally username and
     * password may be passed to the method, if required for the repository.
     *
     * @param string $url 
     * @param string $user 
     * @param string $password 
     * @return void
     */
    public function initialize( $url, $user = null, $password = null )
    {
        if ( !is_file( $url ) || !is_readable( $url ) )
        {
            throw new vcsNoSuchFileException( $url );
        }

        // Try to extract given zip archive
        $archive = new ZipArchive();
        $return  = $archive->open( $url );
        if ( $return !== true )
        {
            throw new vcsInvalidZipArchiveException( $url, $return );
        }

        // Extract, if archive has been opened successfully
        $archive->extractTo( $this->root );

        // Move all files from the repository root to the checkout root.
        $root  = $this->findRepositoryRoot( $archive );
        $files = glob( $this->root . '/' . $root . '*' );
        foreach ( $files as $file )
        {
            rename( $file, $this->root . '/' . basename( $file ) );
        }
        rmdir( $this->root . '/'. $root );

        // Finished Zip extraction
        $archive->close();
    }

    /**
     * Find repository root
     *
     * Often all files in an archive can be found in some subdirectory. We want
     * to detect which subdirectory this is to move all files to the root.
     *
     * @param ZipArchive $archive 
     * @return string
     */
    protected function findRepositoryRoot( ZipArchive $archive )
    {
        // Find root directory in zip file.
        $count    = $archive->numFiles;
        $rootFile = $archive->statIndex( 0 );
        $root     = $rootFile['name'];
        for ( $i = 1; $i < $count; ++$i )
        {
            $file = $archive->statIndex( $i );
            $root = $this->commonStartString( $root, $file['name'] );
        }

        return $root;
    }

    /**
     * Common start string
     * 
     * Find and return the longest common start string of two strings.
     * 
     * @param string $string1 
     * @param string $string2 
     * @return string
     */
    protected function commonStartString( $string1, $string2 )
    {
        $length = min( strlen( $string1 ), strlen( $string2 ) );
        $common = '';
        for ( $i = 0; $i < $length && $string1[$i] === $string2[$i]; ++$i )
        {
            $common .= $string1[$i];
        }

        return $common;
    }
}

