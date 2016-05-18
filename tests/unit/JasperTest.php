<?php
/**
 * @link      https://github.com/chrmorandi/yii2-jasper for the canonical source repository
 * @package   yii2-jasper
 * @author    Christopher Mota <chrmorandi@gmail.com>
 * @license   MIT License - view the LICENSE file that was distributed with this source code.
 */

namespace chrmorandi\jasper\test;

//require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use chrmorandi\jasper\Jasper;

class jasperTest extends \PHPUnit_Framework_TestCase
{
    protected $executable;

    protected function setUp()
    {
        $this->executable = "/../../src/JasperStarter/bin/jasperstarter";
    }

    protected function tearDown()
    {
    }
    
    public function testCreateInstance()
    {
        $obj = new Jasper;
        $this->assertTrue($obj instanceof Jasper);
    }

    public function testJava()
    {
        exec('which java', $output, $returnVar);
        if($returnVar != 0) {
            return $this->assertTrue(false);
        }
        $this->assertTrue(true);
    }

    public function testJasperStarter()
    {
        $executable = __DIR__ . $this->executable . " -h";
        
        exec($executable, $output, $returnVar);

        if($returnVar != 0) {
            return $this->assertTrue(true);            
        }
        return $this->assertTrue(false);
    }

    public function testCompileException()
    {
        $this->setExpectedExceptionRegExp('Exception');
        $obj = new Jasper;
        $obj->compile(null);
    }

    public function testProcessException()
    {
        $this->setExpectedExceptionRegExp('Exception');
        $obj = new Jasper;
        $obj->process(null);
    }

    public function testCompileOutput()
    {
        $obj = new Jasper;
        echo $obj->compile("../../examples/hello_world.jrxml")->output() . PHP_EOL;
        echo $obj->process("file.jasper")->output() . PHP_EOL;
    }
    
    
}