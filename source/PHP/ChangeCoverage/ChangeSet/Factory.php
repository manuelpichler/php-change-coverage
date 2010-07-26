<?php
/**
 * This file is part of PHP_ChangeCoverage.
 *
 * PHP Version 5
 *
 * Copyright (c) 2010, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   QualityAssurance
 * @package    PHP_ChangeCoverage
 * @subpackage VersionControl
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

/**
 * Factory class that should should be used to calculate the changeset for a
 * source file instance.
 *
 * @category   QualityAssurance
 * @package    PHP_ChangeCoverage
 * @subpackage VersionControl
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_ChangeCoverage_ChangeSet_Factory
{
    /**
     * Creates a changeset instance for the given source file.
     *
     * @param PHP_ChangeCoverage_Source_File $file
     *
     * @return PHP_ChangeCoverage_ChangeSet
     */
    public function create( PHP_ChangeCoverage_Source_File $file )
    {
        if ( is_object( $vcs = $this->createVcsFile( $file->getPath() ) ) )
        {
            return new PHP_ChangeCoverage_ChangeSet_VersionControl( $vcs, $file );
        }
        return new PHP_ChangeCoverage_ChangeSet_FileSystem( $file );
    }

    /**
     * This method tries to determine the used version control, to create a
     * corresponding instance of {@link vcsFile}. If no matching or known version
     * control system was found, this method simply returns <b>null</b>.
     *
     * @param string $file The full qualified file path.
     *
     * @return vcsFile
     */
    protected function createVcsFile( $file )
    {
        $fileParts = array( basename( $file ) );
        $parts = explode( DIRECTORY_SEPARATOR, dirname( realpath( $file ) ) );
        do {

            $root = join( DIRECTORY_SEPARATOR, $parts ) . DIRECTORY_SEPARATOR;
            $path = DIRECTORY_SEPARATOR . join( DIRECTORY_SEPARATOR, $fileParts );

            if ( file_exists( $root . '.svn' ) )
            {
                if ( extension_loaded( 'svn' ) )
                {
                    return new vcsSvnExtFile( $root, $path );
                }
                else
                {
                    // @codeCoverageIgnoreStart
                    return new vcsSvnCliFile( $root, $path );
                    // @codeCoverageIgnoreEnd
                }
            }
            else if ( file_exists( $root . '.git' ) )
            {
                return new vcsGitCliFile( $root, $path );
            }
            else if ( file_exists( $root . '.hg' ) )
            {
                return new vcsHgCliFile( $root, $path );
            }
            else if ( file_exists( $root . '.bzr' ) )
            {
                return new vcsBzrCliFile($root, $path);
            }
            else if ( file_exists( $root . 'CVS' ) )
            {
                return new vcsCvsCliFile( $root, $path );
            }

            if ( ( $part = array_pop( $parts ) ) === null )
            {
                break;
            }

            array_unshift( $fileParts, $part );

        } while ( true );

        return null;
    }
}