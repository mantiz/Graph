<?php
/**
 * File containing the abstract ezcGraphChartElementNumericAxis class
 *
 * @package Graph
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class to represent a numeric axis. The axis tries to calculate "nice" start
 * and end values for the axis scale. The used interval is considered as nice, 
 * if it is equal to [1,2,5] * 10^x with x in [.., -1, 0, 1, ..].
 *
 * The start and end value are the next bigger / smaller multiple of the 
 * intervall compared to the maximum / minimum axis value.
 *
 * @property float $min
 *           Minimum value of displayed scale on axis.
 * @property float $max
 *           Maximum value of displayed scale on axis.
 * @property-read float $minValue
 *                Minimum Value to display on this axis.
 * @property-read float $maxValue
 *                Maximum value to display on this axis.
 *           
 * @version //autogentag//
 * @package Graph
 * @mainclass
 */
class ezcGraphChartElementNumericAxis extends ezcGraphChartElementAxis
{

    /**
     * Constant used for calculation of automatic definition of major scaling 
     * steps
     */
    const MIN_MAJOR_COUNT = 5;

    /**
     * Constant used for automatic calculation of minor steps from given major 
     * steps 
     */
    const MIN_MINOR_COUNT = 8;

    /**
     * Constructor
     * 
     * @param array $options Default option array
     * @return void
     * @ignore
     */
    public function __construct( array $options = array() )
    {
        $this->properties['min'] = null;
        $this->properties['max'] = null;
        $this->properties['minValue'] = null;
        $this->properties['maxValue'] = null;

        parent::__construct( $options );
    }

    /**
     * __set 
     * 
     * @param mixed $propertyName 
     * @param mixed $propertyValue 
     * @throws ezcBaseValueException
     *          If a submitted parameter was out of range or type.
     * @throws ezcBasePropertyNotFoundException
     *          If a the value for the property options is not an instance of
     * @return void
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'min':
                if ( !is_numeric( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'float' );
                }

                $this->properties['min'] = (float) $propertyValue;
                $this->properties['initialized'] = true;
                break;
            case 'max':
                if ( !is_numeric( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'float' );
                }

                $this->properties['max'] = (float) $propertyValue;
                $this->properties['initialized'] = true;
                break;
            default:
                parent::__set( $propertyName, $propertyValue );
                break;
        }
    }

    /**
     * Returns a "nice" number for a given floating point number.
     *
     * Nice numbers are steps on a scale which are easily recognized by humans
     * like 0.5, 25, 1000 etc.
     * 
     * @param float $float Number to be altered
     * @return float Nice number
     */
    protected function getNiceNumber( $float )
    {
        // Get absolute value and save sign
        $abs = abs( $float );
        $sign = $float / $abs;

        // Normalize number to a range between 1 and 10
        $log = (int) round( log10( $abs ), 0 );
        $abs /= pow( 10, $log );


        // find next nice number
        if ( $abs > 5 )
        {
            $abs = 10.;
        }
        elseif ( $abs > 2.5 )
        {
            $abs = 5.;
        }
        elseif ( $abs > 1 )
        {
            $abs = 2.5;
        }
        else
        {
            $abs = 1;
        }

        // unnormalize number to original values
        return $abs * pow( 10, $log ) * $sign;
    }

    /**
     * Calculate minimum value for displayed axe basing on real minimum and
     * major step size
     * 
     * @param float $min Real data minimum 
     * @param float $max Real data maximum
     * @return void
     */
    protected function calculateMinimum( $min, $max )
    {
        $this->properties['min'] = floor( $min / $this->properties['majorStep'] ) * $this->properties['majorStep'];
    }

    /**
     * Calculate maximum value for displayed axe basing on real maximum and
     * major step size
     * 
     * @param float $min Real data minimum 
     * @param float $max Real data maximum
     * @return void
     */
    protected function calculateMaximum( $min, $max )
    {
        $this->properties['max'] = ceil( $max / $this->properties['majorStep'] ) * $this->properties['majorStep'];
    }

    /**
     * Calculate size of minor steps based on the size of the major step size
     *
     * @param float $min Real data minimum 
     * @param float $max Real data maximum
     * @return void
     */
    protected function calculateMinorStep( $min, $max )
    {
        $stepSize = $this->properties['majorStep'] / self::MIN_MINOR_COUNT;
        $this->properties['minorStep'] = $this->getNiceNumber( $stepSize );
    }

    /**
     * Calculate size of major step based on the span to be displayed and the
     * defined MIN_MAJOR_COUNT constant.
     *
     * @param float $min Real data minimum 
     * @param float $max Real data maximum
     * @return void
     */
    protected function calculateMajorStep( $min, $max )
    {
        $span = $max - $min;
        $stepSize = $span / self::MIN_MAJOR_COUNT;
        $this->properties['majorStep'] = $this->getNiceNumber( $stepSize );
    }

    /**
     * Add data for this axis
     * 
     * @param array $values Value which will be displayed on this axis
     * @return void
     */
    public function addData( array $values )
    {
        foreach ( $values as $value )
        {
            if ( $this->properties['minValue'] === null ||
                 $value < $this->properties['minValue'] )
            {
                $this->properties['minValue'] = $value;
            }

            if ( $this->properties['maxValue'] === null ||
                 $value > $this->properties['maxValue'] )
            {
                $this->properties['maxValue'] = $value;
            }
        }

        $this->properties['initialized'] = true;
    }

    /**
     * Calculate axis bounding values on base of the assigned values 
     * 
     * @abstract
     * @access public
     * @return void
     */
    public function calculateAxisBoundings()
    {
        // Prevent division by zero, when min == max
        if ( $this->properties['minValue'] == $this->properties['maxValue'] )
        {
            if ( $this->properties['minValue'] == 0 )
            {
                $this->properties['maxValue'] = 1;
            }
            else
            {
                if ( $this->properties['majorStep'] !== null )
                {
                    $this->properties['minValue'] -= $this->properties['majorStep'];
                    $this->properties['maxValue'] += $this->properties['majorStep'];
                }
                else
                {
                    $this->properties['minValue'] -= ( $this->properties['minValue'] * .1 );
                    $this->properties['maxValue'] += ( $this->properties['maxValue'] * .1 );
                }
            }
        }

        // Use custom minimum and maximum if available
        if ( $this->properties['min'] !== null )
        {
            $this->properties['minValue'] = $this->properties['min'];
        }

        if ( $this->properties['max'] !== null )
        {
            $this->properties['maxValue'] = $this->properties['max'];
        }

        // Calculate "nice" values for scaling parameters
        if ( $this->properties['majorStep'] === null )
        {
            $this->calculateMajorStep( $this->properties['minValue'], $this->properties['maxValue'] );
        }

        if ( $this->properties['minorStep'] === null )
        {
            $this->calculateMinorStep( $this->properties['minValue'], $this->properties['maxValue'] );
        }

        if ( $this->properties['min'] === null )
        {
            $this->calculateMinimum( $this->properties['minValue'], $this->properties['maxValue'] );
        }

        if ( $this->properties['max'] === null )
        {
            $this->calculateMaximum( $this->properties['minValue'], $this->properties['maxValue'] );
        }
    }

    /**
     * Get coordinate for a dedicated value on the chart
     * 
     * @param float $value Value to determine position for
     * @return float Position on chart
     */
    public function getCoordinate( $value )
    {
        // Force typecast, because ( false < -100 ) results in (bool) true
        $floatValue = (float) $value;

        if ( ( $value === false ) &&
             ( ( $floatValue < $this->properties['min'] ) || ( $floatValue > $this->properties['max'] ) ) )
        {
            switch ( $this->position )
            {
                case ezcGraph::LEFT:
                case ezcGraph::TOP:
                    return 0.;
                case ezcGraph::RIGHT:
                case ezcGraph::BOTTOM:
                    return 1.;
            }
        }
        else
        {
            switch ( $this->position )
            {
                case ezcGraph::LEFT:
                case ezcGraph::TOP:
                    return ( $value - $this->properties['min'] ) / ( $this->properties['max'] - $this->properties['min'] );
                case ezcGraph::RIGHT:
                case ezcGraph::BOTTOM:
                    return 1 - ( $value - $this->properties['min'] ) / ( $this->properties['max'] - $this->properties['min'] );
            }
        }
    }

    /**
     * Return count of minor steps
     * 
     * @return integer Count of minor steps
     */
    public function getMinorStepCount()
    {
        return (int) ( ( $this->properties['max'] - $this->properties['min'] ) / $this->properties['minorStep'] );
    }

    /**
     * Return count of major steps
     * 
     * @return integer Count of major steps
     */
    public function getMajorStepCount()
    {
        return (int) ( ( $this->properties['max'] - $this->properties['min'] ) / $this->properties['majorStep'] );
    }

    /**
     * Get label for a dedicated step on the axis
     * 
     * @param integer $step Number of step
     * @return string label
     */
    public function getLabel( $step )
    {
        if ( $this->properties['labelCallback'] !== null )
        {
            return call_user_func_array(
                $this->properties['labelCallback'],
                array(
                    $this->properties['min'] + ( $step * $this->properties['majorStep'] ),
                    $step,
                )
            );
        }
        else
        {
            return $this->properties['min'] + ( $step * $this->properties['majorStep'] );
        }
    }

    /**
     * Is zero step
     *
     * Returns true if the given step is the one on the initial axis position
     * 
     * @param int $step Number of step
     * @return bool Status If given step is initial axis position
     */
    public function isZeroStep( $step )
    {
        return ( $this->getLabel( $step ) == 0 );
    }
}

?>
