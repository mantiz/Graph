<?php
/**
 * ezcGraphBoundingsTest 
 * 
 * @package Graph
 * @version //autogen//
 * @subpackage Tests
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Tests for ezcGraph class.
 * 
 * @package ImageAnalysis
 * @subpackage Tests
 */
class ezcGraphBoundingsTest extends ezcTestCase
{
	public static function suite()
	{
		return new ezcTestSuite( "ezcGraphBoundingsTest" );
	}

    public function testCreateBoundings()
    {
        $boundings = new ezcGraphBoundings( 0, 1, 10, 11 );

        $this->assertEquals( $boundings->x0, 0 );
        $this->assertEquals( $boundings->y0, 1 );
        $this->assertEquals( $boundings->x1, 10 );
        $this->assertEquals( $boundings->y1, 11 );
    }

    public function testPseudoProperties()
    {
        $boundings = new ezcGraphBoundings( 0, 1, 10, 21 );

        $this->assertEquals( $boundings->width, 10 );
        $this->assertEquals( $boundings->height, 20 );
    }

    public function testCreateReverseBoundings()
    {
        $boundings = new ezcGraphBoundings( 10, 11, 0, 1 );

        $this->assertEquals( $boundings->x0, 0 );
        $this->assertEquals( $boundings->y0, 1 );
        $this->assertEquals( $boundings->x1, 10 );
        $this->assertEquals( $boundings->y1, 11 );
    }
}

?>