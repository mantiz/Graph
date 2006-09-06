<?php
/**
 * File containing the abstract ezcGraphChart class
 *
 * @package Graph
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class containing the container for the charts datasets
 *
 * @package Graph
 */

class ezcGraphChartDataContainer implements ArrayAccess, Iterator, Countable
{
    /**
     * Contains the data of a chart
     * 
     * @var array( ezcGraphDataSet )
     */
    protected $data = array();

    /**
     * Chart using this data set storage
     * 
     * @var ezcGraphChart
     */
    protected $chart;

    public function __construct( ezcGraphChart $chart )
    {
        $this->chart = $chart;
    }

    /**
     * Adds a dataset to the charts data
     * 
     * @param string $name Name of dataset
     * @param mixed $values Values to create dataset with
     * @throws ezcGraphTooManyDataSetExceptions
     *          If too many datasets are created
     * @return ezcGraphDataSet
     */
    protected function addDataSet( $name, ezcGraphDataSet $dataSet )
    {
        $this->data[$name] = $dataSet;
        
        $this->data[$name]->label = $name;
        $this->data[$name]->palette = $this->chart->palette;
        $this->data[$name]->displayType = $this->chart->getDefaultDisplayType();
    }

    /**
     * Returns if the given offset exists.
     *
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array.
     * 
     * @param string $key Identifier of dataset.
     * @return bool True when the offset exists, otherwise false.
     */
    public function offsetExists( $key )
    {
        return isset( $this->data[$key] );
    }

    /**
     * Returns the element with the given offset. 
     *
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. 
     * 
     * @param string $key Identifier of dataset.
     * @return ezcGraphDataSet
     *
     * @throws ezcGraphNoSuchDataSetException
     *         If no dataset with identifier exists
     */
    public function offsetGet( $key )
    {
        if ( !isset( $key ) )
        {
            throw new ezcGraphNoSuchDataSetException( $key );
        }

        return $this->data[$key];
    }

    /**
     * Set the element with the given offset. 
     *
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. 
     * 
     * @param string $key Identifier of dataset
     * @param ezcGraphDataSet$value The dataset to assign.
     * @return void
     *
     * @throws ezcGraphUnknownDataSetSourceException
     *         If supplied value is not an ezcGraphDataSet
     */
    public function offsetSet( $key, $value )
    {
        if ( !$value instanceof ezcGraphDataSet )
        {
            throw new ezcGraphUnknownDataSetSourceException( $value );
        }

        return $this->addDataSet( $key, $value );
    }

    /**
     * Unset the element with the given offset. 
     *
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. 
     * 
     * @param int $offset The offset to unset the value for.
     * @return void
     */
    public function offsetUnset( $key )
    {
        if ( !isset( $key ) )
        {
            throw new ezcGraphNoSuchDataSetException( $key );
        }

        unset( $this->data[$key] );
    }

    /**
     * Returns the currently selected dataset.
     *
     * This method is part of the Iterator interface to allow access to the 
     * datasets of this row by iterating over it like an array (e.g. using
     * foreach).
     * 
     * @return ezcGraphDataSet The currently selected dataset.
     */
    public function current()
    {
        return current( $this->data );
    }

    /**
     * Returns the next dataset and selects it or false on the last dataset.
     *
     * This method is part of the Iterator interface to allow access to the 
     * datasets of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @return mixed ezcGraphDataSet if the next dataset exists, or false.
     */
    public function next()
    {
        return next( $this->data );
    }

    /**
     * Returns the key of the currently selected dataset.
     *
     * This method is part of the Iterator interface to allow access to the 
     * datasets of this row by iterating over it like an array (e.g. using
     * foreach).
     * 
     * @return int The key of the currently selected dataset.
     */
    public function key()
    {
        return key( $this->data );
    }

    /**
     * Returns if the current dataset is valid.
     *
     * This method is part of the Iterator interface to allow access to the 
     * datasets of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @return bool If the current dataset is valid
     */
    public function valid()
    {
        return ( current( $this->data ) !== false );
    }

    /**
     * Selects the very first dataset and returns it.
     * This method is part of the Iterator interface to allow access to the 
     * datasets of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @return ezcGraphDataSet The very first dataset.
     */
    public function rewind()
    {
        return reset( $this->data );
    }

    /**
     * Returns the number of datasets in the row.
     *
     * This method is part of the Countable interface to allow the usage of
     * PHP's count() function to check how many datasets exist.
     *
     * @return int Number of datasets.
     */
    public function count()
    {
        return count( $this->data );
    }
}
?>