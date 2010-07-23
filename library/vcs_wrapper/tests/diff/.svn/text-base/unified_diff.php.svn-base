<?php
/**
 * Basic test cases for framework
 *
 * @version $Revision$
 * @license GPLv3
 */

/**
 * Tests for the Cunified diff parser
 */
class vcsUnifiedDiffParserTests extends vcsTestCase
{
    /**
     * Array with diffs in dataprovider.
     *
     * @param array
     */
    protected static $diffs = null;

    /**
     * Return test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
	public static function suite()
	{
		return new PHPUnit_Framework_TestSuite( __CLASS__ );
	}

    public static function getUnifiedDiffFiles()
    {
        if ( self::$diffs !== null ) return $diffs;

        $files = glob( dirname( __FILE__ ) . '/../data/diff/unified/s_*.diff' );
        foreach ( $files as $file )
        {
            self::$diffs[] = array(
                $file,
                substr( $file, 0, -4 ) . 'php'
            );
        }

        return self::$diffs;
    }

    /**
     * @dataProvider getUnifiedDiffFiles
     */
    public function testParseUnifiedDiff( $from, $to )
    {
        if ( !is_file( $to ) )
        {
            $this->markTestIncomplete( "Comparision file $to does not yet exist." );
        }

        $parser = new vcsUnifiedDiffParser();
        $diff = $parser->parseFile( $from );

        // Store diff result in temp folder for manual check in case of failure
        file_put_contents( $this->tempDir . '/' . basename( $to ), "<?php\n\n return " . var_export( $diff, true ) . ";\n\n" );

        // Compare parsed diff against expected diff.
        $this->assertEquals(
            include $to,
            $diff,
            "Diff for file $from does not match expectations."
        );
    }
}

