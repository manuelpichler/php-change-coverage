<?php
/**
 * PHP VCS wrapper unified diff parser
 *
 * This file is part of vcs-wrapper.
 *
 * vcs-wrapper is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * vcs-wrapper is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with vcs-wrapper; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package VCSWrapper
 * @subpackage Diff
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Parser for unified diffs
 */
class vcsUnifiedDiffParser extends vcsDiffParser
{
    /**
     * Parse diff string
     *
     * Parse the diff, given as a string, into a vcsDiff objects. The different
     * diff objects are returned in an array.
     *
     * @param string $string 
     * @return array(vcsDiff)
     */
    public function parseString( $string )
    {
        $lines     = preg_split( '(\r\n|\r|\n)', $string );
        $lineCount = count( $lines );
        $diffs     = array();
        $diff      = null;
        $collected = array();
        
        for ( $i = 0; $i < $lineCount; ++$i )
        {
            if ( preg_match( '(^---\\s+(?P<file>\\S+))', $lines[$i], $fromMatch ) &&
                 preg_match( '(^\\+\\+\\+\\s+(?P<file>\\S+))', $lines[$i + 1], $toMatch ) )
            {
                // If a diff already has started, parse the colected lines for
                // the affected diff.
                if ( $diff !== null )
                {
                    $this->parseFileDiff( $diff, $collected );
                    $diffs[]   = $diff;
                    $collected = array();
                }

                $diff = new vcsDiff( $fromMatch['file'], $toMatch['file'] );
                ++$i;
            }
            else
            {
                // Collect all lines, which do not indicate a starting diff.
                $collected[] = $lines[$i];
            }
        }

        // We reached the end of the diff, perhaps there are still lines to
        // calcualte a diff from?
        if ( count( $collected ) &&
             ( $diff !== null ) )
        {
            $this->parseFileDiff( $diff, $collected );
            $diffs[] = $diff;
        }

        return $diffs;
    }

    /**
     * Parse the diff of one file
     *
     * Parse the unified diff for one file, which may consists of a finitie
     * amount of diff chunks.
     *
     * @param vcsDiff $diff 
     * @param array $lines 
     * @return void
     */
    protected function parseFileDiff( vcsDiff $diff, array $lines )
    {
        $chunks = array();
        while ( count( $lines ) )
        {
            // Skip lines until we hit a range specification
            while ( !preg_match( '(^@@\\s+-(?P<start>\\d+)(?:,\\s*(?P<startrange>\\d+))?\\s+\\+(?P<end>\\d+)(?:,\\s*(?P<endrange>\\d+))?\\s+@@)', $last = array_shift( $lines ), $match ) )
            {
                // If we reached the end, break
                if ( $last === null )
                {
                    break 2;
                }
            }

            $chunk = new vcsDiffChunk(
                $match['start'],
                ( isset( $match['startrange'] ) ? max( 1, $match['startrange'] ) : 1 ),
                $match['end'],
                ( isset( $match['endrange'] ) ? max( 1, $match['endrange'] ) : 1 )
            );

            // Read following diff lines
            $diffLines = array();
            $last      = null;
            while ( count( $lines ) &&
                    ( preg_match( '(^(?P<type>[+ -])(?P<line>.*))', $last = array_shift( $lines ), $match ) ||
                      ( strpos( $last, '\\ No newline at end of file' ) === 0 ) ) )
            {
                // We ignore the missing newlines for now
                if ( count( $match ) )
                {
                    $diffLines[] = new vcsDiffLine(
                        ( $match['type'] === '+' ? vcsDiffLine::ADDED :
                            ( $match['type'] === '-' ? vcsDiffLine::REMOVED : vcsDiffLine::UNCHANGED ) ),
                        $match['line']
                    );
                }
            }
            $chunk->lines = $diffLines;
            $chunks[]     = $chunk;

            // Repreprend last not matching line
            if ( $last !== null )
            {
                array_unshift( $lines, $last );
            }
        }
        
        $diff->chunks = $chunks;
    }
}

