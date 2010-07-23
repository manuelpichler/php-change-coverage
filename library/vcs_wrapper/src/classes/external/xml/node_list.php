<?php
/**
 * Arbit Xml node list
 *
 * This file is part of Arbit.
 *
 * Arbit is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * Arbit is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Arbit; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package XML
 * @version $Revision: 963 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * XML node list
 *
 * Single element node in an XML document mostly behaving like a
 * SimpleXMLElement node list.
 */
class arbitXmlNodeList implements ArrayAccess, Iterator, Countable
{
    /**
     * Nodes in node list
     * 
     * @var array(arbitXmlNode)
     */
    protected $nodes;
    
    /**
     * Create new configuration node list.
     *
     * Create new configuration node list.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->nodes = array();
    }

    /**
     * Access property in node list
     *
     * Accessing a property directly on a node list will return a new node list
     * with all childs with this name of all nodes in the current node list.
     * 
     * @param string $childName 
     * @return arbitXmlNodeList
     */
    public function __get( $childName )
    {
        // Create new list to fill up
        $list = new arbitXmlNodeList();

        // Iterate over all nodes and check if a child with the requested name
        // exists.
        foreach ( $this->nodes as $node )
        {
            if ( $node->$childName )
            {
                // If such a node exists iterate over returned node list and
                // append all childs to returned node list.
                foreach ( $node->$childName as $childNode )
                {
                    $list[] = $childNode;
                }
            }
        }

        return $list;
    }

    /**
     * Access childs through object properties
     *
     * Access childs through object properties
     * 
     * @param string $property 
     * @param mixed $value
     * @return arbitXmlNode
     */
    public function __set( $property, $value )
    {
        throw new arbitAccessException( $property, 'Setting not allowed' );
    }

    /**
     * Check if a property exists
     * 
     * Check if a property given by its name as object property exists. This is
     * a quite expensive operation on a node list.
     * 
     * @param string $childName 
     * @return bool
     */
    public function __isset( $childName )
    {
        // Iterate over all nodes and check if a child with the requested name
        // exists.
        foreach ( $this->nodes as $node )
        {
            if ( isset( $node->$childName ) )
            {
                // We found something, so that we can immediatly exit with
                // true. The general count is out of interest here.
                return true;
            }
        }

        // Return false, if no such property could be found before...
        return false;
    }

    /**
     * Set object state after var_export.
     * 
     * Set object state after var_export.
     * 
     * @param array $array 
     * @return void
     */
    public static function __set_state( array $array )
    {
        $list = new arbitXmlNodeList();

        // Just append all known nodes using array access.
        foreach ( $array['nodes'] as $node )
        {
            $list[] = $node;
        }

        return $list;
    }

    /**
     * String representation of node list
     *
     * String representation of node list returns the string representation of
     * the first node in the list.
     * 
     * @return string
     */
    public function __toString()
    {
        $firstNode = reset( $this->nodes );
        return (string) $firstNode;
    }

    /*
     * ArrayAccess
     */

    /**
     * Check if node exists in node list.
     *
     * Check if node exists in node list.
     * 
     * @param int $item 
     * @return void
     */
    public function offsetExists( $item )
    {
        return array_key_exists( $item, $this->nodes );
    }
    
    /**
     * Get node from node list.
     *
     * Get node from node list by its number.
     * 
     * @param int $item 
     * @return arbitXmlNode
     */
    public function offsetGet( $item )
    {
        if ( $this->offsetExists( $item ) )
        {
            return $this->nodes[$item];
        }

        return false;
    }
    
    /**
     * Append node to node list
     *
     * Append node to node list
     * 
     * @param int $item 
     * @param arbitXmlNode $node
     * @return void
     */
    public function offsetSet( $item, $node )
    {
        // We only allow to append nodes to node list, so that we bail out on
        // all other array keys then null.
        if ( $item !== null )
        {
            throw new arbitValueException( $item, 'null' );
        }

        return $this->nodes[] = $node;
    }
    
    /**
     * Remove node from node list.
     * 
     * Removing nodes from the node list is not allowed. You may only add nodes
     * here.
     * 
     * @param int $item 
     * @return void
     */
    public function offsetUnset( $item )
    {
        throw new arbitValueException( 'unset', 'Removing nodes not allowed.' );
    }

    /**
     * Iterator
     */

    /**
     * Implements current() for Iterator
     * 
     * @return mixed
     */
    public function current()
    {
        return current( $this->nodes );
    }

    /**
     * Implements key() for Iterator
     * 
     * @return int
     */
    public function key()
    {
        return key( $this->nodes );
    }

    /**
     * Implements next() for Iterator
     * 
     * @return mixed
     */
    public function next()
    {
        return next( $this->nodes );
    }

    /**
     * Implements rewind() for Iterator
     * 
     * @return mixed
     */
    public function rewind()
    {
        // If no nodes are in subtree return false
        if ( !count( $this->nodes ) )
        {
            return false;
        }

        return reset( $this->nodes );
    }

    /**
     * Implements valid() for Iterator
     * 
     * @return boolean
     */
    public function valid()
    {
        return ( current( $this->nodes ) !== false );
    }

    /**
     * Countable
     */

    /**
     * Return count of nodes in node list. 
     * 
     * Return count of nodes in node list. 
     * 
     * @return int
     */
    public function count()
    {
        return count( $this->nodes );
    }
}

