<?php

namespace chrmorandi\jasper;

use yii\base\Component;
use yii\db\Connection;
use yii\db\Exception;
use yii\helpers\BaseStringHelper;

/**
 * Jasper implements JasperReport application component creating reports.
 *
 * By default, Jasper create reports whithout database.
 *
 *
 * ```php
 * 'jasper' => [
 *     'class' => 'chrmorandi\jasper',
 *     'redirect_output' => false, //optional
 *     'resource_directory' => false, //optional
 *     'db' => [
 *         'host' => localhost,
 *         'port' => 5432,    
 *         'driver' => 'postgres',
 *         'dbname' => db_banco,
 *         'username' => 'cajui',
 *         'password' => 'cajui',
 *         //'jdbcDir' => './jdbc', **Defaults to ./jdbc
 *         //'jdbcUrl' => 'jdbc:postgresql://"+host+":"+port+"/"+dbname',
 *     ]
 * ]
 * ```
 *
 * @author Christopher M. Mota <chrmorandi@gmail.com>
 * @since 1.0.0
 */
class Jasper extends Component
{
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     *                              After the Jasper object is created, if you want to change this property, you should
     *                              only assign it with a DB connection object.
     */
    public $db;

    /**
     * @var bool|string contains path to report resource dir. If false given the input_file directory is used.
     */
    public $resource_directory = false;

    /**
     * @var bool redirect output and errors to /dev/null
     */
    public $redirect_output = true;
    public $output_file = false;

    protected $executable = '/../JasperStarter/bin/jasperstarter';
    protected $the_command;
    protected $background;
    protected $windows = false;
    protected $formats = ['pdf', 'rtf', 'xls', 'xlsx', 'docx', 'odt', 'ods', 'pptx', 'csv', 'html', 'xhtml', 'xml', 'jrprint'];

    /**
     * Initializes the Jasper component.
     *
     * @throws Exception if [[resource_directory]] not exist.
     */
    public function init()
    {
        parent::init();

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->windows = true;
        }

        if ($this->resource_directory) {
            if (!file_exists($this->resource_directory)) {
                throw new Exception('Invalid resource directory', 1);
            }
        }
    }

    /**
     * Compile JasperReport template(JRXML) to native binary format, called Jasper file.
     * 
     * @param string $input_file
     * @param string $output_file
     * @param string $output_file
     * @param bool   $background
     * 
     * @return
     */
    public function compile($input_file, $output_file = false, $background = false)
    {
        if (is_null($input_file) || empty($input_file)) {
            throw new Exception('No input file', 1);
        }

        $command = __DIR__.$this->executable;

        $command .= ' compile ';

        $command .= $input_file;

        if ($output_file !== false) {
            $command .= ' -o '.$output_file;
        }

        $this->background = $background;
        $this->the_command = escapeshellcmd($command);

        return $this;
    }

    /**
     * Generates report . Accepts files in the format ".jrxml" or ".jasper".
     * 
     * ```php
     * $jasper->process(
     *     __DIR__ . '/vendor/chrmorandi/yii2-jasper/examples/hello_world.jasper',
     *     ['php_version' => 'xxx']
     *     ['pdf', 'ods'],
     * )->execute();
     * ```
     * 
     * @param string $input_file
     * @param array  $parameters
     * @param array  $format      available formats : pdf, rtf, xls, xlsx, docx, odt, ods, pptx, csv, html, xhtml, xml, jrprint.
     * @param string $output_file if false the input_file directory is used. Default is false
     * @param bool   $background  if true report is runing in the backgrount. The return status is 0. Default is false
     */
    public function process($input_file, $parameters = [], $format = ['pdf'], $output_file = false, $background = false)
    {
        if (is_null($input_file) || empty($input_file)) {
            throw new Exception('No input file', 1);
        }

        if (is_array($format)) {
            foreach ($format as $key) {
                if (!in_array($key, $this->formats)) {
                    throw new Exception('Invalid format!', 1);
                }
            }
        } else {
            if (!in_array($format, $this->formats)) {
                throw new Exception('Invalid format!', 1);
            }
        }

        $command = __DIR__.$this->executable;

        $command .= ' process ';

        $command .= $input_file;

        if ($output_file !== false) {
            $command .= ' -o '.$output_file;
        }

        if (is_array($format)) {
            $command .= ' -f '.implode(' ', $format);
        } else {
            $command .= ' -f '.$format;
        }

        if ($this->resource_directory) {
            $command .= ' -r '.$this->resource_directory;
        }
        else {
            $resource = BaseStringHelper::dirname($input_file);
            $command .= ' -r '.$this->resource_directory;
        }

        if (count($parameters) > 0) {
            $command .= ' -P';
            foreach ($parameters as $key => $value) {
                $command .= ' '.$key.'='.$value;
            }
        }

        if (isset($this->db)) {
            $command .= ' -t '.$this->db['driver'];
            $command .= ' -u '.$this->db['username'];

            if (isset($this->db['password']) && !empty($this->db['password'])) {
                $command .= ' -p '.$this->db['password'];
            }

            if (isset($this->db['host']) && !empty($this->db['host'])) {
                $command .= ' -H '.$this->db['host'];
            }

            if (isset($this->db['dbname']) && !empty($this->db['dbname'])) {
                $command .= ' -n '.$this->db['dbname'];
            }

            if (isset($this->db['port']) && !empty($this->db['port'])) {
                $command .= ' --db-port '.$this->db['port'];
            }

            if (isset($this->db['jdbc_url']) && !empty($this->db['jdbc_url'])) {
                $command .= ' --db-url '.$this->db['jdbc_url'];
            }

            if (isset($this->db['jdbc_dir']) && !empty($this->db['jdbc_dir'])) {
                $command .= ' --jdbc-dir '.$this->db['jdbc_dir'];
            }
        }

        $this->background = $background;
        $this->the_command = escapeshellcmd($command);

        return $this;
    }

    public function list_parameters($input_file)
    {
        if (is_null($input_file) || empty($input_file)) {
            throw new Exception('No input file', 1);
        }

        $command = __DIR__.$this->executable;

        $command .= ' list_parameters ';

        $command .= $input_file;

        $this->the_command = escapeshellcmd($command);

        return $this;
    }

    public function execute($run_as_user = false)
    {
        if ($this->redirect_output && !$this->windows) {
            $this->the_command .= ' > /dev/null 2>&1';
        }

        if ($this->background && !$this->windows) {
            $this->the_command .= ' &';
        }

        if ($run_as_user !== false && strlen($run_as_user > 0) && !$this->windows) {
            $this->the_command = 'su -u '.$run_as_user.' -c "'.$this->the_command.'"';
        }

        $output = [];
        $return_var = 0;

        exec($this->the_command, $output, $return_var);

        if ($return_var != 0) {
            throw new Exception('Your report has an error and couldn\'t be processed! Try to output the command: ' . escapeshellcmd($this->the_command), 1);
        }

        return $output;
    }
}
