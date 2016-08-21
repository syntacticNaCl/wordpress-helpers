<?php

/**
 * An extensible PHPUnit test case base class.
 * Class TestCase
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public $logOutput = true;

    /**
     * Prints test output.
     * @param $output
     */
    public function log($output)
    {
        if ( ! $this->logOutput )
        {
            return;
        }

        dump( $output );
    }

    public function setUp()
    {
    }
}