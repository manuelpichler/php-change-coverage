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
class PHP_ChangeCoverage_VersionControl_VcsResource implements PHP_ChangeCoverage_VersionControl_Resource
{
    /**
     *
     * @var vcsFile
     */
    private $file = null;

    /**
     *
     * @var PHP_ChangeCoverage_Source_File
     */
    private $sourceFile = null;

    private $startDate = 0;

    private $changedLines = null;

    public function __construct( vcsFile $file, PHP_ChangeCoverage_Source_File $file2 )
    {
        $this->file       = $file;
        $this->sourceFile = $file2;
    }

    public function setStartDate( $startDate )
    {
        $this->startDate = $startDate;
    }

    public function getPath()
    {
        return $this->sourceFile->getPath();
    }

    public function getLines()
    {
        $this->getChangedLines();
        return $this->sourceFile->getLines();
    }

    private function getChangedLines()
    {
        return $this->createOrReturnChangedLines();
    }

    private function createOrReturnChangedLines()
    {
        if ( $this->changedLines === null )
        {
            $this->changedLines = $this->createChangedLines();
        }
        return $this->changedLines;
    }

    private function createChangedLines()
    {
        $lines = array();
        foreach ( $this->file->getLog() as $log )
        {
            if ( $log->date >= $this->startDate )
            {
                $lines = $this->collectBlameInformation( $log->version, $lines );
            }
        }
        return new PHP_ChangeCoverage_VersionControl_ChangeSet( $lines );
    }

    private function collectBlameInformation( $version, array $lines )
    {
        $blame = $this->file->blame( $version );
        foreach ( $this->sourceFile->getLines() as $line )
        {
            if ( false === isset( $blame[$line->getNumber() - 1] ) )
            {
                continue;
            }
            if ( $blame[$line->getNumber() - 1]->date < $this->startDate )
            {
                continue;
            }
            $line->setChanged();
        }

        foreach ( $blame as $line => $struct )
        {
            if ( $struct->date >= $this->startDate )
            {
                if ( isset( $lines[$line + 1] ) )
                {
                    ++$lines[$line + 1];
                }
                else
                {
                    $lines[$line + 1] = 1;
                }
            }
        }
        return $lines;
    }
}