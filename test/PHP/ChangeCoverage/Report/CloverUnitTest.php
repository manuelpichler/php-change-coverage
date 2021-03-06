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
 * @subpackage Report
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

require_once dirname( __FILE__ ) . '/../AbstractTestCase.php';

/**
 * Unit tests for class {@link PHP_ChangeCoverage_Report_Clover}.
 *
 * @category   QualityAssurance
 * @package    PHP_ChangeCoverage
 * @subpackage Report
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_ChangeCoverage_Report_CloverUnitTest extends PHP_ChangeCoverage_AbstractTestCase
{
    private $cloverFixture = '<?xml version="1.0"?>
        <coverage>
            <project>
                <file name="/foo.php">
                    <line num="17" count="2" type="method" />
                    <line num="23" count="2" type="stmt" />
                    <line num="42" count="2" type="stmt" />
                </file>
                <file name="/bar.php">
                    <line num="17" count="2" type="method" />
                    <line num="23" count="2" type="stmt" />
                    <line num="42" count="2" type="stmt" />
                </file>
                <file name="/baz.php">
                    <line num="17" count="2" type="method" />
                    <line num="23" count="2" type="stmt" />
                    <line num="42" count="2" type="stmt" />
                </file>
            </project>
        </coverage>';

    /**
     * testGetFilesReturnsAnIteratorInstance
     *
     * @return void
     * @covers PHP_ChangeCoverage_Report_Clover
     * @group report
     * @group unittest
     */
    public function testGetFilesReturnsAnIteratorInstance()
    {
        $report = $this->createCloverReport();
        self::assertType( 'Iterator', $report->getFiles() );
    }

    /**
     * testGetFilesIteratorContainsExpectedNumberEntries
     *
     * @return void
     * @covers PHP_ChangeCoverage_Report_Clover
     * @group report
     * @group unittest
     */
    public function testGetFilesIteratorContainsExpectedNumberEntries()
    {
        $report = $this->createCloverReport();
        self::assertEquals( 3, iterator_count( $report->getFiles() ) );
    }

    /**
     * testGetFilesIteratorContainsSourceFileInstances
     *
     * @return void
     * @covers PHP_ChangeCoverage_Report_Clover
     * @group report
     * @group unittest
     */
    public function testGetFilesIteratorContainsSourceFileInstances()
    {
        $report = $this->createCloverReport();
        self::assertType( 'PHP_ChangeCoverage_Source_File', $report->getFiles()->current() );
    }

    /**
     * Creates a test fixture.
     *
     * @return PHP_ChangeCoverage_Report_Clover
     */
    protected function createCloverReport()
    {
        $path = $this->createFile( 'report.xml', $this->cloverFixture );
        $sxml = simplexml_load_file( $path );

        return new PHP_ChangeCoverage_Report_Clover( $sxml );
    }
}