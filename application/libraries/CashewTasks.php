<?php
/**
 * Libreria para la gestión de tareas en background usando Celery. Se 
 * basa en el envío de mensajes a una cola de mensajes AMQ que pueda entender 
 * Celery.
 *
 * Para la instalación del paquete PECL de amqp, ver:
 * http://php.net/manual/en/amqp.installation.php
 *
 * Basado en: https://github.com/gjedeer/celery-php/blob/master/celery.php
 * 
 * @author Cuble Desarrollo S.L.

 *
 */
class CashewTasks
{
    /**
     * Referencia al core de CodeIgniter.
     * @var 
     */
    private $CI;
    
    /**
     * Detalles de la conexion.
     * @var array
     */
    private $connection_details = array();
    
    /**
     * Conexion a la cola de tareas.
     * @var AMQPConnection
     */
    private $connection = null;
    
    /**
     * 
     */
    function __construct($connection_details = array())
    {
        /**
         * Se necesita tener soporte para conectarse a AMQP.
         */
        if(!class_exists('AMQPConnection'))
        {
            die("Class AMQPConnection not found\n".
                    "Make sure that AMQP extension is installed and enabled:\n".
                    "http://www.php.net/manual/en/amqp.installation.php");
        }
        
        /**
         * Obtenemos la configuración por defecto si no se ha introduciod una 
         * por parámetro.
         */
        $this->CI = & get_instance();
        if (!is_array($connection_details) || count($connection_details) == 0)
        {
            $this->CI->load->config('tasks');
            $connection_details = $this->CI->config->item('tasks_config');
        }
        
        /**
         * Guardamos la configuración.
         */
        foreach(array('host', 'login', 'password', 'vhost', 'exchange',
                 'binding', 'port') as $detail)
        {
            $this->connection_details[$detail] = $connection_details[$detail];
        }
        
        $this->connection = new AMQPConnection();
        $this->connection->setHost($this->connection_details['host']);
        $this->connection->setLogin($this->connection_details['login']);
        $this->connection->setPassword($this->connection_details['password']);
        $this->connection->setVhost($this->connection_details['vhost']);
        $this->connection->setPort($this->connection_details['port']);
    }
    
    /**
     * Lanza una tarea en background
     * 
     * @param string $task
     * @param array $args
     */
    function post_task($task, $args)
    {
        $this->connection->connect();
        if(!is_array($args))
        {
            throw new CeleryException("Args should be an array");
        }
        $id = uniqid('php_', TRUE);
        $ch = new AMQPChannel($this->connection);
        $xchg = new AMQPExchange($ch);
        $xchg->setName($this->connection_details['exchange']);
        
        /* $args is numeric -> positional args */
        if(array_keys($args) === range(0, count($args) - 1))
        {
            $kwargs = array();
        }
        /* $args is associative -> contains kwargs */
        else
        {
            $kwargs = $args;
            $args = array();
        }
        
        $task_array = array(
                'id' => $id,
                'task' => $task,
                'args' => $args,
                'kwargs' => (object)$kwargs,
        );
        $task = json_encode($task_array);
        $params = array('content_type' => 'application/json',
                'content_encoding' => 'UTF-8',
                'immediate' => false,
        );
        
        $success = $xchg->publish($task, $this->connection_details['exchange'], 0, $params);
        $this->connection->disconnect();
        
        // TODO Integrar respuestas asincronas con CodeIgniter
        // return new AsyncResult($id, $this->connection_details, $task_array['task'], $args);
        return true;
    }
    
    /**
     * Recupera el AsyncResult de una tarea 
     * @param string $id
     * @retunr AsyncResult
     */
public function get_result($id)
    {
        return new AsyncResult($id, $this->connection_details, $task_array['task'], $args);
    }
}

