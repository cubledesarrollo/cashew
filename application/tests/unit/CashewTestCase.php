<?php
/**
 * 
 * @author Marcos Gabarda
 *
 */
class CashewTestCaseConfig
{
    public $CI;
    static private $instance;
    static public function get_instance()
    {
        if (is_null(self::$instance))
        {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }
    private function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->database();
        $this->CI->load->dbforge();
        
        $tables = $this->CI->db->list_tables();
        foreach ($tables as $table)
        {
            if ($table != 'migrations')
            {
                $this->CI->dbforge->drop_table($table);
            }
            else
            {
                $this->CI->db->truncate($table);
            }
        }
        
        $this->CI->load->library('migration');
        $this->CI->migration->current();
    }
}
/**
 * 
 * @author Marcos Gabarda
 *
 */
class CashewTestCase extends PHPUnit_Framework_TestCase
{

    protected $CI;
    
    protected function setUp()
    {
        $config = CashewTestCaseConfig::get_instance();
        $this->CI = $config->CI;
        
        $tables = $this->CI->db->list_tables();
        foreach ($tables as $table)
        {
            if ($table != 'migrations')
            {
                $this->CI->db->truncate($table);
            }
        }
        $this->CI->load->library('CashewAsserts');
        $this->CI->cashewasserts->load_sql();
        $this->CI->cashewasserts->load_models();
        
        $this->CI->load->library('session');
    }
    public function tearDown()
    {

    }
}
