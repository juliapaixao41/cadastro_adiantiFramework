<?php

/**
 * Created on 08.04.2010
 *
 * @author sz
 */
class MergedCellsSampleTest extends PHPRtfLiteSampleTestCase
{

    private $_name = 'merged_cells';

    public function test()
    {
        $this->processTest($this->_name . '.php');
    }

    protected function getSampleFile()
    {
        return $this->getSampleDir() . '/generated/' . $this->_name . '.rtf';
    }

}
