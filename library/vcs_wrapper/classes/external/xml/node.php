<?php
/**
 * Arbit Xml node
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
 * @version $Revision: 1009 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * XML node
 *
 * Single element node in an XML document mostly behaving like a
 * SimpleXMLElement object.
 */
class arbitXmlNode implements ArrayAccess
{
    /**
     * Childnodes of this node
     * 
     * @var array(arbitXmlNode)
     */
    protected $childs;

    /**
     * Configuration attribute values
     * 
     * @var array
     */
    protected $attributes;

    /**
     * Text content of the node.
     * 
     * @var string
     */
    protected $content;
    
    /**
     * Create new configuration node.
     *
     * Create new configuration node with optionally given parent.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->childs     = array();
        $this->attributes = array();
        $this->content    = '';
    }

    /**
     * Set content of node.
     *
     * Set content of node supplied by parameter.
     * 
     * @param string $string 
     * @return void
     */
    public function setContent( $string )
    {
        $this->content = (string) $string;
    }

    /**
     * Access childs through object properties
     *
     * Access childs through object properties
     * 
     * @param string $childName 
     * @return arbitXmlNode
     */
    public function __get( $childName )
    {
        if ( isset( $this->childs[$childName] ) )
        {
            return $this->childs[$childName];
        }

        return false;
    }

    /**
     * Access childs through object properties
     *
     * Access childs through object properties
     * 
     * @param string $childName 
     * @param arbitXmlNode $child
     * @return arbitXmlNode
     */
    public function __set( $childName, arbitXmlNode $child )
    {
        if ( !is_string( $childName ) )
        {
            // We only accept strings for child names
            throw new arbitValueException( $childName, 'string' );
        }

        // Check if there already is a node list, othwerwise create it
        if ( !isset( $this->childs[$childName] ) )
        {
            $this->childs[$childName] = new arbitXmlNodeList();
        }

        return $this->childs[$childName][] = $child;
    }

    /**
     * Check if a child exists
     * 
     * Check if a child given by its name as object property exists.
     * 
     * @param string $childName 
     * @return bool
     */
    public function __isset( $childName )
    {
        return array_key_exists( $childName, $this->childs );
    }

    /**
     * Retun if an attribute accessed through array access exists.
     *
     * Retun if an attribute accessed through array access exists.
     * 
     * @param string $attributeName 
     * @return void
     */
    public function offsetExists( $attributeName )
    {
        return array_key_exists( $attributeName, $this->attributes );
    }
    
    /**
     * Get attribute value accessed through array access
     *
     * Get attribute value accessed through array access
     * 
     * @param string $attributeName 
     * @return void
     */
    public function offsetGet( $attributeName )
    {
        if ( $this->offsetExists( $attributeName ) )
        {
            return $this->attributes[$attributeName];
        }

        return false;
    }
    
    /**
     * Set attribute value acceesd through array access
     *
     * Set attribute value acceesd through array access
     * 
     * @param string $attributeName 
     * @param string $attribute 
     * @return void
     */
    public function offsetSet( $attributeName, $attribute )
    {
        if ( !is_string( $attributeName ) ||
             !is_string( $attribute ) )
        {
            // We only accept strings for name AND content
            throw new arbitValueException( $attribute, 'string' );
        }

        $this->attributes[$attributeName] = $attribute;
    }
    
    /**
     * Unset attribute through array access
     * 
     * Unset attribute through array access
     * 
     * @param string $attributeName 
     * @return void
     */
    public function offsetUnset( $attributeName )
    {
        unset( $this->attributes[$attributeName] );
    }

    /**
     * Return text content on string casting.
     *
     * Return text content of node, when casted to string or echoed.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }

    /**
     * Convert current node into a document node
     * 
     * @return arbitXml
     */
    public function toDocument()
    {
        return arbitXml::__set_state( array( 
            'childs'     => $this->childs,
            'attributes' => $this->attributes,
            'content'    => $this->content,
        ) );
    }

    /**
     * Set object state after var_export.
     * 
     * Set object state after var_export.
     * 
     * @param array $array 
     * @param string $class
     * @return arbitXmlNode
     */
    public static function __set_state( array $array, $class = 'arbitXmlNode' )
    {
        $node = new $class();

        // Reassign all childrens to the node. For that we get all elements out
        // of the node list and assign them to the node.
        foreach ( $array['childs'] as $name => $nodeList )
        {
            foreach ( $nodeList as $child )
            {
                $node->$name = $child;
            }
        }

        // Reassign all attribute values
        foreach ( $array['attributes'] as $name => $value )
        {
            $node[$name] = $value;
        }

        // Set content of node
        $node->setContent( $array['content'] );

        // Done - return created node
        return $node;
    }
}

