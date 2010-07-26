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
 * @subpackage ChangeSet
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

// Ignore the class signature
// @codeCoverageIgnoreStart

/**
 * This class implements a simple/fallback changeset that uses the file
 * modification time to calculate the changed source lines.
 *
 * @category   QualityAssurance
 * @package    PHP_ChangeCoverage
 * @subpackage ChangeSet
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_ChangeCoverage_ChangeSet_FileSystem implements PHP_ChangeCoverage_ChangeSet
{

    // @codeCoverageIgnoreEnd
    // Ignore the class signature

    /**
     * The context source file.
     *
     * @var PHP_ChangeCoverage_Source_File
     */
    private $file = null;

    /**
     * Unix timestamp representing the start date of this changeset.
     *
     * @var integer
     */
    private $startDate = null;

    /**
     * Constructs a new changeset instance.
     *
     * @param PHP_ChangeCoverage_Source_File $file The context source file.
     */
    public function __construct( PHP_ChangeCoverage_Source_File $file )
    {
        $this->file = $file;
    }

    /**
     * Sets the start date as an unix timestamp for this changeset.
     *
     * @param integer $startDate The changset's start date.
     *
     * @return void
     */
    public function setStartDate( $startDate )
    {
        $this->startDate = $startDate;
    }

    /**
     * Calculates the changed lines for the context source file and returns a
     * prepared file instance where the <b>hasChanged()</b> flag is set to
     * <b>true</b>.
     *
     * @return PHP_ChangeCoverage_Source_File
     */
    public function calculate()
    {
        if ( filemtime( $this->file->getPath() ) >= $this->startDate )
        {
            $this->updateChangedStatus();
        }
        return $this->file;
    }

    /**
     * This method updates the changed status of all lines in the context source
     * file to <b>true</b>.
     *
     * @return void
     */
    protected function updateChangedStatus()
    {
        foreach ( $this->file->getLines() as $line )
        {
            $line->setChanged();
        }
    }
}