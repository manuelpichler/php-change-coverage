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

/**
 * This factory creates report instances for a given coverage xml report file.
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
class PHP_ChangeCoverage_Report_Factory
{
    /**
     * This method takes the given file and checks if it is a support coverage
     * report format and then creates a report instance for this format. If
     * the given file format is not supported or the file does not exist, this
     * method will throw exception.
     *
     * @param string $fileName The coverage xml report file.
     *
     * @return PHP_ChangeCoverage_Report
     * @throws RuntimeException When the given file does not exist or if the
     *         given file uses an unsupported coverage format.
     */
    public function createReport( $fileName )
    {
        if ( false === file_exists( $fileName ) )
        {
            throw new RuntimeException( "File '{$fileName} does not exist." );
        }

        $sxml = simplexml_load_file( $fileName );
        if ( isset( $sxml->project ) )
        {
            return new PHP_ChangeCoverage_Report_Clover( $sxml );
        }
        throw new RuntimeException( 'The given report format is not supported.' );
    }
}