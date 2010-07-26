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

    public function create( PHP_ChangeCoverage_Source_File $file )
    {
        $fileParts = array( basename( $file->getPath() ) );
        $parts = explode( DIRECTORY_SEPARATOR, dirname( realpath( $file->getPath() ) ) );
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
                    return new vcsSvnCliFile( $root, $path );
                }
            }
            else if ( file_exists( $root . '.git' ) )
            {
                return new PHP_ChangeCoverage_ChangeSet_VersionControl( new vcsGitCliFile( $root, $path ), $file );
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
                throw new RuntimeException( 'No more elements found.' );
            }
            
            array_unshift( $fileParts, $part );
            
        } while ( true );
    }
}