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
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 */

/**
 * Utility class that handles autoloading.
 *
 * @category   QualityAssurance
 * @package    PHP_ChangeCoverage
 * @subpackage TextUI
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 */
class PHP_ChangeCoverage_Autoloader
{
    /**
     * Directory with the used external library code.
     *
     * @var string
     */
    private $libraryDirectory = null;

    /**
     * Autoload mapping used by one of the used libraries.
     *
     * @var array(string=>string)
     */
    private $autoloadMapping = array();

    /**
     * Constructs a new autoloader instance.
     *
     * @param string $libraryDirectory Directory with the used external library code.
     */
    public function __construct( $libraryDirectory )
    {
        $this->libraryDirectory  = $libraryDirectory;
        $this->libraryDirectory .= '/vcs_wrapper/classes/';

        $this->autoloadMapping  = include $this->libraryDirectory . 'autoload.php';
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register( array( $this, 'autoload' ) );
    }

    /**
     * Tries to load the source file for the given class name.
     *
     * @param string $className Name of the class to load.
     *
     * @return void
     */
    public function autoload( $className )
    {
        if ( strpos( $className, 'PHP_' ) === 0 )
        {
            include strtr( $className, '_', '/' ) . '.php';
        }
        else if ( isset( $this->autoloadMapping[$className] ) )
        {
            include $this->libraryDirectory . $this->autoloadMapping[$className];
        }
    }
}