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
 * @category  QualityAssurance
 * @package   PHP_ChangeCoverage
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

/**
 * This is a utility class that takes a changeset from a version control system
 * and raw input data from another coverage process and then creates the so
 * called change coverage in a format compatible to xdebug.
 *
 * @category  QualityAssurance
 * @package   PHP_ChangeCoverage
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_ChangeCoverage_Xdebug
{
    /**
     * The changeset for the context source file.
     *
     * @var PHP_ChangeCoverage_VersionControl_Resource
     */
    private $changeSet = null;

    /**
     * Should the collect coverage implementation stop execution.
     *
     * @var boolean
     */
    private $stopExecution = false;

    /**
     * Constructs a new xdebug coverage data instance.
     *
     * @param PHP_ChangeCoverage_VersionControl_Resource $changeSet Changeset
     *        for the parsed/context source file.
     */
    public function __construct(
        PHP_ChangeCoverage_VersionControl_Resource $changeSet
    ) {
        $this->changeSet = $changeSet;
    }

    /**
     * This method returns coverage array similar to those generated by xdebug.
     * Additionally this method acts like an iterator and it returns different
     * coverage arrays for all possible file execution. The return value of this
     * method will be <b>null</b> when no more coverage data exists for the
     * context file.
     *
     * @return array(integer=>integer)
     */
    public function getCoverage()
    {
        if ( $this->stopExecution )
        {
            return null;
        }
        return $this->createXdebugCoverageArray();
    }

    /**
     * This method creates an array with coverage data similar to that arrays
     * which xdebug's coverage code generates.
     *
     * This method sets an internal execution flag to <b>true</b> when there is
     * more coverage information available than the current array contains.
     *
     * @return array(integer=>integer)
     */
    protected function createXdebugCoverageArray()
    {
        $this->stopExecution = true;

        $xdebug = array();
        foreach ( $this->changeSet->getLines() as $line )
        {
            if ( $line->hasChanged( $line ) )
            {
                if ( $line->getCount() === 0 )
                {
                    $xdebug[$line->getNumber()] = -1;
                }
                else
                {
                    $this->stopExecution = false;

                    $line->decrementCount();
                    $xdebug[$line->getNumber()] = 1;
                }
            }
            else
            {
                $xdebug[$line->getNumber()] = -2;
            }
        }
        return array( $this->changeSet->getPath() => $xdebug );
    }
}