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
require_once dirname( __FILE__ ) . '/AbstractTestCase.php';

/**
 * Unit tests for class {@link PHP_ChangeCoverage_Xdebug}.
 *
 * @category  QualityAssurance
 * @package   PHP_ChangeCoverage
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_ChangeCoverage_XdebugUnitTest extends PHP_ChangeCoverage_AbstractTestCase
{
    /**
     * testGeneratedXdebugArrayContainesCoveredAndChangedLines
     *
     * @return void
     * @covers PHP_ChangeCoverage_Xdebug
     * @group unittest
     */
    public function testGeneratedXdebugArrayContainesCoveredAndChangedLines()
    {
        $file = new PHP_ChangeCoverage_Source_File(
            '/tmp/foo.php',
            array(
                new PHP_ChangeCoverage_Source_Line( 23, 1, true ),
                new PHP_ChangeCoverage_Source_Line( 42, 1, true ),
            )
        );

        $xdebug = new PHP_ChangeCoverage_Xdebug();
        $actual = iterator_to_array( $xdebug->generateData( $file ) );

        $this->assertEquals(
            array(
                array(
                    '/tmp/foo.php'  =>  array(
                        23 => 1,
                        42 => 1
                    ),
                )
            ),
            $actual
        );
    }

    /**
     * testGeneratedXdebugArrayContainsUncoveredButChangedLines
     *
     * @return void
     * @covers PHP_ChangeCoverage_Xdebug
     * @group unittest
     */
    public function testGeneratedXdebugArrayContainsUncoveredButChangedLines()
    {
        $file = new PHP_ChangeCoverage_Source_File(
            '/tmp/foo.php',
            array(
                new PHP_ChangeCoverage_Source_Line( 23, 1, true ),
                new PHP_ChangeCoverage_Source_Line( 42, 0, true ),
            )
        );

        $xdebug = new PHP_ChangeCoverage_Xdebug();
        $actual = iterator_to_array( $xdebug->generateData( $file ) );

        $this->assertEquals(
            array(
                array(
                    '/tmp/foo.php'  =>  array(
                        23 => 1,
                        42 => -1
                    ),
                )
            ),
            $actual
        );
    }

    /**
     * testGeneratedXdebugArrayContainsUncoveredAndNotChangedLines
     *
     * @return void
     * @covers PHP_ChangeCoverage_Xdebug
     * @group unittest
     */
    public function testGeneratedXdebugArrayContainsUncoveredAndNotChangedLines()
    {
        $file = new PHP_ChangeCoverage_Source_File(
            '/tmp/foo.php',
            array(
                new PHP_ChangeCoverage_Source_Line( 23, 0, true ),
                new PHP_ChangeCoverage_Source_Line( 42, 1, false ),
            )
        );

        $xdebug = new PHP_ChangeCoverage_Xdebug();
        $actual = iterator_to_array( $xdebug->generateData( $file ) );

        $this->assertEquals(
            array(
                array(
                    '/tmp/foo.php'  =>  array(
                        23 => -1,
                        42 => -2
                    ),
                )
            ),
            $actual
        );
    }

    /**
     * testGeneratedDataContainsXdebugArrayForEachPossibleExecution
     *
     * @return void
     * @covers PHP_ChangeCoverage_Xdebug
     * @group unittest
     */
    public function testGeneratedDataContainsXdebugArrayForEachPossibleExecution()
    {
        $file = new PHP_ChangeCoverage_Source_File(
            '/tmp/foo.php',
            array(
                new PHP_ChangeCoverage_Source_Line( 13, 4, true ),
                new PHP_ChangeCoverage_Source_Line( 17, 2, true ),
                new PHP_ChangeCoverage_Source_Line( 23, 0, true ),
                new PHP_ChangeCoverage_Source_Line( 42, 3, false ),
            )
        );

        $xdebug = new PHP_ChangeCoverage_Xdebug();
        $actual = iterator_to_array( $xdebug->generateData( $file ) );

        $this->assertEquals(
            array(
                array(
                    '/tmp/foo.php'  =>  array(
                        13 =>  1,
                        17 =>  1,
                        23 => -1,
                        42 => -2
                    ),
                ),
                array(
                    '/tmp/foo.php'  =>  array(
                        13 =>  1,
                        17 =>  1,
                        23 => -1,
                        42 => -2
                    ),
                ),
                array(
                    '/tmp/foo.php'  =>  array(
                        13 =>  1,
                        17 => -1,
                        23 => -1,
                        42 => -2
                    ),
                ),
                array(
                    '/tmp/foo.php'  =>  array(
                        13 =>  1,
                        17 => -1,
                        23 => -1,
                        42 => -2
                    ),
                ),
            ),
            $actual
        );
    }
}