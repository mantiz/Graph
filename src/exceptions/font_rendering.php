<?php
/**
 * File containing the ezcGraphFontRenderingException class
 *
 * @package Graph
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown when it is not possible to render a string beacause of 
 * minimum font size in the desinated bounding box.
 *
 * @package Graph
 * @version //autogen//
 */
class ezcGraphFontRenderingException extends ezcGraphException
{
    public function __construct( $string, $size, $width, $height )
    {
        parent::__construct( "Could not fit string <{$string}> with font size <{$size}> in box <{$width} * {$height}>." );
    }
}

?>