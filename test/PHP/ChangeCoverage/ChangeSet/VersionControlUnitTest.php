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

require_once dirname( __FILE__ ) . '/../AbstractTestCase.php';

/**
 * Unit tests for class {@link PHP_ChangeCoverage_ChangeSet_VersionControl}.
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
class PHP_ChangeCoverage_ChangeSet_VersionControlUnitTest extends PHP_ChangeCoverage_AbstractTestCase
{
    /**
     * testCalculateReturnsOriginalSourceFileInstance
     *
     * @return void
     * @covers PHP_ChangeCoverage_ChangeSet_VersionControl
     * @group changeset
     * @group unittest
     */
    public function testCalculateReturnsOriginalSourceFileInstance()
    {
        $file = new PHP_ChangeCoverage_Source_File( '/foo/bar.php', array() );
        $vcs  = $this->createVcsFileMock();

        $changeSet = new PHP_ChangeCoverage_ChangeSet_VersionControl( $vcs, $file );
        self::assertSame( $file, $changeSet->calculate() );
    }

    /**
     * testCalculateSkipsFilesWhereRevisionLogIsOlderThanDateRange
     *
     * @return void
     * @covers PHP_ChangeCoverage_ChangeSet_VersionControl
     * @group changeset
     * @group unittest
     */
    public function testCalculateSkipsFilesWhereRevisionLogIsOlderThanDateRange()
    {
        $file = new PHP_ChangeCoverage_Source_File(
            '/foo/bar.php',
            array(
                new PHP_ChangeCoverage_Source_Line( 1, 2 ),
                new PHP_ChangeCoverage_Source_Line( 2, 2 ),
                new PHP_ChangeCoverage_Source_Line( 3, 2 ),
                new PHP_ChangeCoverage_Source_Line( 5, 2 ),
            )
        );
        $vcs = $this->createVcsFileMock( array( array( 1, 360 ), array( 2, 360 ) ) );

        $changeSet = new PHP_ChangeCoverage_ChangeSet_VersionControl( $vcs, $file );
        $changeSet->setStartDate( 86401 );

        $actual = array();
        foreach ( $changeSet->calculate()->getLines() as $line )
        {
            $actual[$line->getNumber()] = $line->hasChanged();
        }

        self::assertEquals(
            array(
                1  =>  false,
                2  =>  false,
                3  =>  false,
                5  =>  false,
            ),
            $actual
        );
    }

    /**
     * testCalculateSkipsLinesWhereRevisionLogIsOlderThanDateRange
     *
     * @return void
     * @covers PHP_ChangeCoverage_ChangeSet_VersionControl
     * @group changeset
     * @group unittest
     */
    public function testCalculateSkipsLinesWhereRevisionLogIsOlderThanDateRange()
    {
        $file = new PHP_ChangeCoverage_Source_File(
            '/foo/bar.php',
            array(
                new PHP_ChangeCoverage_Source_Line( 1, 2 ),
                new PHP_ChangeCoverage_Source_Line( 2, 2 ),
                new PHP_ChangeCoverage_Source_Line( 3, 2 ),
                new PHP_ChangeCoverage_Source_Line( 5, 2 ),
            )
        );
        $vcs = $this->createVcsFileMock(
            array( array( 1, 86400 ) ),
            array( 86400, 86398, 86400, 86398 )
        );

        $changeSet = new PHP_ChangeCoverage_ChangeSet_VersionControl( $vcs, $file );
        $changeSet->setStartDate( 86399 );

        $actual = array();
        foreach ( $changeSet->calculate()->getLines() as $line )
        {
            $actual[$line->getNumber()] = $line->hasChanged();
        }

        self::assertEquals(
            array(
                1  =>  true,
                2  =>  false,
                3  =>  true,
                5  =>  false,
            ),
            $actual
        );
    }

    /**
     * Creates a mocked vcs file instance.
     *
     * @return vcsFile
     */
    protected function createVcsFileMock( $logs = array(), $blame = array() )
    {
        include_once dirname(__FILE__) . '/_stubs/VcsFileStub.php';

        $fileStub = new PHP_ChangeCoverage_ChangeSet_VcsFileStub();
        foreach ( $logs as $log )
        {
            $fileStub->logs[] = $this->createLogEntry( $log );
        }
        foreach ( $blame as $date )
        {
            $fileStub->blame[] = $this->createBlameEntry( $date );
        }
        
        return $fileStub;
    }

    protected function createLogEntry( array $log )
    {
        $obj          = new stdClass();
        $obj->version = $log[0];
        $obj->date    = $log[1];

        return $obj;
    }

    protected function createBlameEntry( $date )
    {
        $obj       = new stdClass();
        $obj->date = $date;

        return $obj;
    }
}