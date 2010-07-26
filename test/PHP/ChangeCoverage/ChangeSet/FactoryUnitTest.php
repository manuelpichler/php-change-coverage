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
 * Unit tests for class {@link PHP_ChangeCoverage_ChangeSet_Factory}.
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
class PHP_ChangeCoverage_ChangeSet_FactoryUnitTest extends PHP_ChangeCoverage_AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->deleteTempDirectory();
    }

    protected function tearDown()
    {
        $this->deleteTempDirectory();

        parent::tearDown();
    }

    /**
     * testFactoryCreatesVersionControlChangeSet
     *
     * @param string $meta Name of the local directory that normally holds
     *        meta data used by the version control system.
     *
     * @return void
     * @covers PHP_ChangeCoverage_ChangeSet_Factory
     * @group changeset
     * @group unittest
     * @dataProvider dataProviderVersionControlSystems
     */
    public function testFactoryCreatesVersionControlChangeSet( $meta )
    {
        $path = $this->createDirectory( $meta );

        $factory   = new PHP_ChangeCoverage_ChangeSet_Factory();
        $changeSet = $factory->create( new PHP_ChangeCoverage_Source_File( $path, array() ) );

        self::assertType( 'PHP_ChangeCoverage_ChangeSet_VersionControl', $changeSet );
    }

    /**
     * testFactoryTraversesTheSourceTreeToDetectTheVersionControlSystem
     *
     * @return void
     * @covers PHP_ChangeCoverage_ChangeSet_Factory
     * @group changeset
     * @group unittest
     */
    public function testFactoryTraversesTheSourceTreeToDetectTheVersionControlSystem()
    {
        $this->createDirectory( 'foo/.bzr' );
        $path = $this->createDirectory( 'foo/bar/baz' );

        $factory   = new PHP_ChangeCoverage_ChangeSet_Factory();
        $changeSet = $factory->create( new PHP_ChangeCoverage_Source_File( $path, array() ) );

        self::assertType( 'PHP_ChangeCoverage_ChangeSet_VersionControl', $changeSet );
    }

    /**
     * testFactoryReturnsFallbackFileSystemChangeSet
     *
     * @return void
     * @covers PHP_ChangeCoverage_ChangeSet_Factory
     * @group changeset
     * @group unittest
     */
    public function testFactoryReturnsFallbackFileSystemChangeSet()
    {
        $path = $this->createDirectory( 'foo/bar/baz' );

        $factory   = new PHP_ChangeCoverage_ChangeSet_Factory();
        $changeSet = $factory->create( new PHP_ChangeCoverage_Source_File( $path, array() ) );

        self::assertType( 'PHP_ChangeCoverage_ChangeSet_FileSystem', $changeSet );
    }

    public static function dataProviderVersionControlSystems()
    {
        return array(
            array( '.bzr' ),
            array( '.git' ),
            array( '.hg' ),
            array( '.svn' ),
            array( 'CVS' )
        );
    }

    protected function createDirectory( $directory )
    {
        $path = $this->createTempDirectory() . '/' . $directory;
        mkdir( $path, 0755, true );
        return $path;
    }

    protected function createTempDirectory()
    {
        if ( false === file_exists( $this->getTempDirectory() ) )
        {
            mkdir( $this->getTempDirectory(), 0755, true );
        }
        return $this->getTempDirectory();
    }

    protected function deleteTempDirectory()
    {
        if ( file_exists( $this->getTempDirectory() ) )
        {
            $this->deleteDirectory( $this->getTempDirectory() );
        }
    }

    protected function deleteDirectory( $directory )
    {
        $dir = new DirectoryIterator( $directory );
        foreach ( $dir as $file )
        {
            if ( $file->isFile() )
            {
                unlink( $file->getPathname() );
            }
            else if ( $file->getFilename() !== '.' && $file->getFilename() !== '..' )
            {
                $this->deleteDirectory( $file->getPathname() );
            }
        }
        rmdir( $directory );
    }

    protected function getTempDirectory()
    {
        return sys_get_temp_dir() . '/~phpunit-phpcc';
    }
}