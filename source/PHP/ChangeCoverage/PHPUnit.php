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
 * This class provides a simple abstraction for PHPUnit's cli binary. It will
 * be used by the application to invoke the original PHPUnit.
 *
 * @category  QualityAssurance
 * @package   PHP_ChangeCoverage
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_ChangeCoverage_PHPUnit
{
    /**
     * The PHPUnit cli tool to use.
     *
     * @var string
     */
    private $phpunitBinary = null;

    /**
     * Arguments passed to PHP_ChangeCoverage's cli binary.
     *
     * @var array(string)
     */
    private $arguments = array();

    /**
     * Exit code returned by PHPUnit's cli binary.
     *
     * @var integer
     */
    private $exitCode = 0;

    /**
     * The ctor of this class takes optionally the file for the phpunit cli
     * binary.
     *
     * @param string $phpunitBinary Optional phpunit binary file.
     */
    public function __construct( $phpunitBinary )
    {
        $this->phpunitBinary = $phpunitBinary;
    }

    /**
     * Executes PHPUnit for the current configuration and returns the exit
     * code returned by PHPUnit's cli binary.
     *
     * @param array(array) $argv
     *
     * @return integer
     */
    public function run( array $argv )
    {
        $this->arguments = array_map( 'escapeshellarg', $argv );

        $command = sprintf(
            '%s %s',
            escapeshellarg( $this->phpunitBinary ),
            join( ' ', $this->arguments )
        );

        passthru( $command, $this->exitCode );

        return $this->exitCode;
    }

    /**
     * Returns the exit code, as it was returned by PHPUnit's cli binary.
     *
     * @return integer
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Returns <b>true</b> when PHPUnit has exited with an error or when the
     * help option was passed to the PHP_ChangeCoverage cli binary.
     *
     * @return boolean
     */
    public function isHelp()
    {
        return ( $this->exitCode === 2
            || in_array( 
                escapeshellarg( '--help' ), $this->arguments
            )
        );
    }
}