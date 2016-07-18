<?php
/**
 * @link      https://github.com/chrmorandi/yii2-jasper for the canonical source repository
 * @package   yii2-jasper
 * @author    Christopher Mota <chrmorandi@gmail.com>
 * @license   MIT License - view the LICENSE file that was distributed with this source code.
 */

namespace chrmorandi\jasper;

use yii\base\Component;
use yii\base\Exception;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

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
 *     'locale' => pt_BR, //optional
 *     'db' => [
 *         'host' => localhost,
 *         'port' => 5432,
 *         'driver' => 'postgres',
 *         'dbname' => db_banco,
 *         'username' => 'username',
 *         'password' => 'password',
 *         //'jdbcDir' => './jdbc', **Defaults to ./jdbc
 *         //'jdbcUrl' => 'jdbc:postgresql://"+host+":"+port+"/"+dbname',
 *     ]
 * ]
 * ```
 *
 * @author Christopher M. Mota <chrmorandi@gmail.com>
 * @since  1.0.0
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
    
    /**
     * @var boll if true report is runing in the backgrount. The return status is 0. Default is false
     */
    public $background = false;
    public $locale = null;
    public $output_file = false;
    

    protected $executable = '/../JasperStarter/bin/jasperstarter';
    protected $the_command;
    
    protected $windows = false;
    protected $formats = [
        'pdf', 'rtf', 'xls', 'xlsx', 'docx', 'odt', 'ods',
        'pptx', 'csv', 'html', 'xhtml', 'xml', 'jrprint'
    ];

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
     * @param  string $input_file
     * @param  string $output_file
     * @param  string $output_file
     * @return Jasper
     */
    public function compile($input_file, $output_file = false)
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
        
        $this->the_command = escapeshellcmd($command);

        return $this;
    }

    /**
     * Process report . Accepts files in the format ".jrxml" or ".jasper".
     *
     * ```php
     * $jasper->process(
     *     __DIR__ . '/vendor/chrmorandi/yii2-jasper/examples/hello_world.jasper',
     *     ['php_version' => 'xxx']
     *     ['pdf', 'ods'],
     * )->execute();
     * ```
     *
     * @param  string $input_file
     * @param  array  $parameters
     * @param  array  $format      available formats : pdf, rtf, xls, xlsx, docx, odt, ods, pptx, csv, html, xhtml, xml, jrprint.
     * jrprint.
     * @param  string $output_file if false the input_file directory is used. Default is false
     * @return Jasper
     */
    public function process($input_file, $parameters = [], $format = ['pdf'], $output_file = false)
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
        
        if (!empty($this->locale) && $this->locale != null) {
            $parameters = ArrayHelper::merge(['REPORT_LOCALE' => $this->locale], $parameters);
        }

        if (count($parameters) > 0) {
            $command .= ' -P';
            foreach ($parameters as $key => $value) {
                $command .= ' '.$key.'='.$value;
            }
        }
        
        $command .= $this->databaseParams;

        $this->the_command = escapeshellcmd($command);

        return $this;
    }

    /**
     * Report parameters list
     *
     * @param  type $input_file
     * @return Jasper
     * @throws Exception
     */
    public function listParameters($input_file)
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

    /**
     * Output command
     *
     * @return string
     */
    public function output()
    {
        return escapeshellcmd($this->the_command);
    }

    /**
     * Make report.
     *
     * @param  type $run_as_user Switch without password with "su" command need be enable.
     * @return array
     * @throws Exception
     */
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
            throw new Exception(
                'Your report has an error and couldn\'t be processed! Try to output the command: ' .
                escapeshellcmd($this->the_command),
                1
            );
        }

        return $output;
    }
    
    /**
     * @return string
     */
    protected function getDatabaseParams()
    {
        if (!isset($this->db)) {
            return '';
        }
        
        $command .= ' -t '.$this->db['driver'];
        $command .= ' -u '.$this->db['username'];

        if (!empty($this->db['password'])) {
            $command .= ' -p '.$this->db['password'];
        }

        if (!empty($this->db['host'])) {
            $command .= ' -H '.$this->db['host'];
        }

        if (!empty($this->db['dbname'])) {
            $command .= ' -n '.$this->db['dbname'];
        }

        if (!empty($this->db['port'])) {
            $command .= ' --db-port '.$this->db['port'];
        }

        if (!empty($this->db['jdbc_url'])) {
            $command .= ' --db-url '.$this->db['jdbc_url'];
        }

        if (!empty($this->db['jdbc_dir'])) {
            $command .= ' --jdbc-dir '.$this->db['jdbc_dir'];
        }
        
        return $command;
    }
}
