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
 * Report implementation for xml log files compatible with clover.
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
class PHP_ChangeCoverage_Report_Clover extends IteratorIterator implements PHP_ChangeCoverage_Report
{
    /**
     * Constructs a new clover coverage report instance.
     *
     * @param SimpleXMLElement $sxml Simple xml representation of the clover report.
     */
    public function __construct( SimpleXMLElement $sxml )
    {
        parent::__construct( $sxml->project->file );
        $this->rewind();
    }

    /**
     * Returns an iterator of {@link PHP_ChangeCoverage_Source_File} objects
     * representing those files available in the coverage report file.
     *
     * @return Iterator
     */
    public function getFiles()
    {
        return $this;
    }

    /**
     * Returns a source file instance for the currently active file node in the
     * context clover report or <b>null</b> when the iterator has reached the
     * end.
     *
     * @return PHP_ChangeCoverage_Source_File
     */
    public function current()
    {
        $sxml = parent::current();
        return ( is_object( $sxml ) ? $this->createSourceFile( $sxml ) : null );
    }

    /**
     * This method takes the xml representation of a covered file and creates
     * the corresponding source file instance.
     *
     * @param SimpleXMLElement $file The xml representation of a covered file.
     *
     * @return PHP_ChangeCoverage_Source_File
     */
    protected function createSourceFile( SimpleXMLElement $file )
    {
        $lines = array();
        foreach ( $file->line as $line )
        {
            if ( 'method' === (string) $line['type'] )
            {
                continue;
            }
            $lines[] = new PHP_ChangeCoverage_Source_Line(
                (int) $line['num'],
                (int) $line['count']
            );
        }
        return new PHP_ChangeCoverage_Source_File( (string) $file['name'], $lines );
    }
}