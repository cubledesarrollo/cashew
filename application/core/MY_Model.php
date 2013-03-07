<?php
/**
 *
 * Modelo enriquecido.Un modelo que herede de esta clase tiene que tener
 * obligatoriametne un campo 'id', y la tabla de la base de datos por defecto
 * se tiene que llamar como el nombre de la clase en minusculas.
 *
 * Si se quiere usar otra tabla, habria que sobrecarcar el metodo _meta del
 * nuevo modelo, y hacer $this->table = 'nombre de la tabla'.
 *
 * Sistema de etiquetas para atributos
 * -----------------------------------
 * 
 * Se pueden definir etiequetas que apunten a atributos del modelo. Esto es 
 * útil para la generación automática de formularios, o para usar los 
 * atributos en las urls localizadas.
 * 
 * Para definir etiquetas, sobreescribimos el metodo _meta
 * 
 * protected function _meta()
 * {
 *     $this->create_label('email', 'Correo electrónico', 'es_ES');
 *     $this->create_label('email', 'E-Mail', 'en_US');
 * }
 *
 * Para obtener el label de un atributo:
 * 
 * $this->label('atributo', 'local'); -> nombre de label
 * 
 * Para obtener el atributo de un label:
 * 
 * $this->attribute('label');  -> nombre de atributo
 * 
 * @author Marcos Gabarda
 *
 */
class CashewModel extends CI_Model
{
    /**
     * Atributos del modelo.
     * @var Array
     */
    private $args = array();

    /**
     * Nombre de la tabla.
     * @var string
     */
    protected $table;

    /**
     *
     * Nombre de la clase.
     * @var string
     */
    protected $class_name;

    /**
    *
    * Array con los campos del modelo.
    * @var array
    */
    protected $fields = null;


    /**
     *
     * Array de modelos con los que se esta relacionado muchos a muchos.
     * @var array
     */
    protected $many_to_many_relations;

    /**
     *
     * Relacion de modelos con los que se tiene una relacion de uno a muchos.
     * @var array
     */
    protected $one_to_many_relations;

    /**
     *
     * Relacion de modelos con los que se tiene una relacion de muchos a uno.
     * @var array
     */
    protected $many_to_one_relations;

    
    /**
     * Etiquetas de los atributos.
     * @var array
     */
    protected $labels;
    
    
    /**
     * La última query.
     * @var array
     */
    public $last_query;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('inflector');
        $this->load->library('CashewCache');
        $this->class_name = get_class($this);
        $this->table = plural(strtolower(get_class($this)));
        
        $this->many_to_many_relations = array();
        $this->one_to_many_relations = array();
        $this->many_to_one_relations = array();
        $this->labels = array();
        
        $this->last_query = null;
        
        $this->_meta();
        $this->_init();
    }

    /**
     * Define el nombre de la clase y el nombre de la tabla. Sobrecargar para
     * cambiar el nombre de tabla a la que accede el modelo.
     * 
     * Para definir los campos del modelo, se tiene que crear el array 
     * $this->fields, como se muestra en el ejemplo:
     * 
     * $this->fields = array (
     *     'attr1',
     *     'attr2,
     *     ...
     * );
     * 
     * Para definir etiquetas de un atributo:
     * 
     * $this->create_label(
     *     'attr', // Atributo en el modelo.
     *     'Atributo', // Nombre de la etiqueta.
     *     'es_ES', // Local asociada.
     * );
     * 
     */
    protected function _meta()
    {
    }


    /**
     * Inicializa los tributos del modelo.
     */
    private function _init()
    {
        if (!is_null($this->fields))
        {
            foreach ($this->fields as $field)
            {
                $this->args[$field] = null;
            }
        }
        else if ($this->db->table_exists($this->table))
        {
            $result = $this->cashewcache->get($this->class_name.'_columns');
            if ($result === false)
            {
                $query = $this->db->query("SHOW COLUMNS FROM ".$this->table);
                $result = $query->result();
                $this->cashewcache->set($this->class_name.'_columns', $result);
            }
            foreach ($result as $row)
            {
                $this->args[$row->Field] = null;
                if (!is_null($row->Default))
                {
                    $this->args[$row->Field] = $row->Default;
                }
            }
        }
    }

    /**
     * Se llama a este metodo cuando se intenta acceder a una propiedad no
     * definida, y se sobrecarga para que este acceso a una propiedad no
     * definida se transforme en un acceso al array $this->args.
     *
     * @see CI_Model::__get()
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->args))
        {
            return $this->args[$key];
        }
        return parent::__get($key);
    }

    /**
     *
     * Sobrecarga del metodo magico __set para modificar los atributos.
     * 
     * @param string $key
     * @param string $value
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->args))
        {
            $this->args[$key] = $value;
        }
    }

    /**
     * Comprueba si el atributo existe.
     * 
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->args);
    }

    /**
     *
     * Sobrecarga del metodo magico __call. Se llama cuando se intenta acceder
     * a un metodo no definido, y se utiliza para acceder a los metodos
     * dinamicos disponibles cuando el modelo tiene una relacion de muchos a
     * muchos.
     *
     * Por ejemplo, para añadir un nuevo elemento a la relacion:
     *
     * $modelo_original->add_[modelo_relacionado]($modelo_relacionado)
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        $matches = null;
        if (preg_match('/^add_all_(.+)$/', $name, $matches))
        {
            $table = $matches[1];
            if (!isset($this->many_to_many_relations[$table]))
                return;
            $this->add_all_relation($table);
        }
        else if (preg_match('/^del_all_(.+)$/', $name, $matches))
        {
            $table = $matches[1];
            if (!isset($this->many_to_many_relations[$table]))
                return;
            $this->del_all_relation($table);
        }
        else if (preg_match('/^add_(.+)$/', $name, $matches))
        {
            $table = $matches[1];
            if (!isset($this->many_to_many_relations[$table]))
                return;
            $this->add_relation($table, $arguments[0]);
        }
        else if (preg_match('/^is_(.+)$/', $name, $matches))
        {
            $table = $matches[1];
            if (!isset($this->many_to_many_relations[$table]))
                return;
            return $this->is_relation($table, $arguments[0]);
        }
        else if (preg_match('/^del_(.+)$/', $name, $matches))
        {
            $table = $matches[1];
            if (!isset($this->many_to_many_relations[$table]))
                return;
            $this->del_relation($table, $arguments[0]);
        }
        else if (isset($this->many_to_many_relations[$name]))
        {
            return $this->get_all_relation($name);
        }
        else if (isset($this->one_to_many_relations[$name]))
        {
            return $this->get_many_relation($name);
        }
        else if (isset($this->many_to_one_relations[$name]))
        {
            return $this->get_one_relation($name);
        }
    }

    /**
     *
     * Tiempo actual en formato compatible con la base de datos.
     */
    protected function current_datetime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     *
     * Crea una instancia del modelo con identificador $id y la rellenea con
     * los datos de la base de datos.
     *
     * @param integer $id
     * @return CashewModel
     */
    public function get($id)
    {
        $model =  $this->cashewcache->get($this->class_name.$id);
        if ($model !== false)
        {
            return $model;
        }
        else
        {
            $query = $this->db->get_where($this->table, array('id' => $id));
            $result = $query->result(); 
            if (count($result) == 0)
            {
                return null;
            }
            else {
                $item = new $this->class_name();
                foreach ((array)$result[0] as $field => $value)
                {
                    $item->$field = $value;
                }
                $this->cashewcache->set($this->class_name.$id, $item);
                return $item;
            }
            return null;
        }
    }

    /**
     *
     * Listado de modelos segun un parametro. Siempre devuelve una unica
     * instancia de un modelo.
     *
     * La implementacion actual llama a $this->get() para crear el modelo e
     * intentar hacer uso de la cache.
     *
     * @param string $key
     * @param string $value
     * @return CashewModel
     */
    public function get_by($key, $value)
    {
        $this->db->where($key, $value);
        $this->db->select('id');
        $query = $this->db->get($this->table);
        $this->last_query = $this->db->last_query();  
        $results = $query->result();
        $list = array();
        foreach ($results as $result)
        {
            $class_name = $this->class_name;
            $item = $this->$class_name->get($result->id);
            $list[] = $item;
        }

        if (count($list) == 0)
            return null;
        return $list[0];
    }

    /**
     * Obtiene una lista de modelos filtrados por un conjunto de criterios. Los
     * criterios se almacenan en un array asociativo de tal forma que:
     *
     * - key == nombre del campo
     * - value == valor que tiene que ser igual
     *
     * CashewModel->filter(array('campo' => 'valor')
     *
     * @param array $criteria
     * @return array
     */
    public function filter($criteria = array())
    {
        foreach ($criteria as $key => $value)
        {
            $this->db->where($key, $value);
        }
        $this->db->select('id');
        $query = $this->db->get($this->table);
        $this->last_query = $this->db->last_query();
        $results = $query->result();
        $list = array();
        foreach ($results as $result)
        {
            $class_name = $this->class_name;
            $item = $this->$class_name->get($result->id);
            $list[] = $item;
        }
        return $list;
    }


    /**
     * Obtiene el listado de todos los modelos.
     *
     * @return array
     */
    public function all()
    {
        $query = $this->db->get($this->table);
        $results = $query->result();
        $list = array();
        foreach ($results as $result)
        {
            $item = new $this->class_name();
            foreach ((array)$result as $key => $value)
            {
                $item->$key = $value;
            }
            $list[] = $item;
        }
        return $list;
    }

    /**
     * -------------------------------
     * --- RELACIONES MANY TO MANY ---
     * -------------------------------
     */

    /**
     *
     * Crea una relación de muchos a muchos con el modelo que se le pasa
     * por parametro.
     *
     * La tabla extra tiene que estar creada en la base de datos, y nombre
     * tiene que ser la union de las dos tablas, separados por '_' y en
     * orden alfabetico.
     *
     * @param string $model_name
     */
    protected function create_many_to_many_relation ($model_name)
    {
        $table = plural(strtolower($model_name)); // FIXME No funcionara con tablas personalizadas.
        $relation_table = $this->table."_".$table;
        if (strcmp($this->table, $table) > 0)
            $relation_table = $table."_".$this->table;
        $data = array('table' => $relation_table,
                      'model' => $model_name);
        $this->many_to_many_relations[$table] = $data;
    }

    /**
     *
     * Añade a la relacion 'muchos a muchos' todos los registros que existan.
     * @param string $table
     */
    private function add_all_relation($table)
    {
        $model = $this->many_to_many_relations[$table]['model'];
        $relations = $this->$model->get_all();
        foreach($relations as $relation)
        {
            $this->add_relation($table, $relation);
        }
    }
    /**
     * Elimina todos los elementos relacionados.
     * @param string $table
     */
    private function del_all_relation($table)
    {
        $relation_table = $this->many_to_many_relations[$table]['table'];
        $this->db->delete($relation_table,
                          array(singular($this->table).'_id' => $this->id));
    }

    /**
     * Añade un elemento a la relacion.
     * @param string $table
     * @param CashewModel $relation
     */
    private function add_relation($table, $relation)
    {
        $relation_table = $this->many_to_many_relations[$table]['table'];
        $params = array(singular($table).'_id' => $relation->id,
                        singular($this->table).'_id' => $this->id);
        $this->db->insert($relation_table, $params);
    }

    /**
     * Consulta si el elemento esta relacionado.
     * @param string $table
     * @param CashewModel $relation
     */
    private function is_relation($table, $relation)
    {
        $relation_table = $this->many_to_many_relations[$table]['table'];
        $this->db->where(singular($table).'_id', $relation->id);
        $this->db->where(singular($this->table).'_id', $this->id);
        $query = $this->db->get($relation_table);
        $results = $query->result();
        if (count($results) == 0)
        {
            return false;
        }
        return true;
    }

    /**
     * Elimina la relacion con un elemento.
     * @param string $table
     * @param CashewModel $relation
     */
    private function del_relation($table, $relation)
    {
        $relation_table = $this->many_to_many_relations[$table]['table'];
        $this->db->where(singular($table).'_id', $relation->id);
        $this->db->where(singular($this->table).'_id', $this->id);
        $this->db->delete($relation_table);
    }

    /**
     * Obitene todos los elementos relacionados.
     * @param string $table
     */
    private function get_all_relation($table)
    {
        $relation_table = $this->many_to_many_relations[$table]['table'];
        $model = $this->many_to_many_relations[$table]['model'];
        $this->db->where(singular($this->table).'_id', $this->id);
        $query = $this->db->get($relation_table);
        $results = $query->result();
        $relations = array();
        foreach ($results as $result)
        {
            $arg = singular($table).'_id';
            $relation = $this->$model->get($result->$arg);
            $relations[] = $relation;
        }
        return $relations;
    }

   /**
    * -------------------------------
    * --- RELACIONES ONE TO MANY ---
    * -------------------------------
    */

    /**
     *
     * Crea una relacion uno a muchos. Tienes que poder acceder a una lista de
     * modelos relacionados con el propio modelo.
     *
     * @param string $model_name
     */
    protected function create_one_to_many_relation($model_name)
    {
        $table = plural(strtolower($model_name)); // FIXME No funcionara con tablas personalizadas.
        $data = array('table' => $table,
                      'model' => $model_name);
        $this->one_to_many_relations[$table] = $data;
    }

    /**
     *
     * Obtiene el array de modelos relacionados. El parametro $name viene en
     * plural.
     *
     * @param string $name
     * @return array
     */
    private function get_many_relation($name)
    {
        $table = $name;
        $model = $this->one_to_many_relations[$table]['model'];
        $this->db->where(singular($this->table).'_id', $this->id);
        $query = $this->db->get($table);
        $results = $query->result();
        $relations = array();
        foreach ($results as $result)
        {
            $relation = $this->$model->get($result->id);
            $relations[] =  $relation;
        }
        return $relations;
    }

   /**
    * -------------------------------
    * --- RELACIONES MANY TO ONE ---
    * -------------------------------
    */

    /**
    *
    * Crea una relacion uno a muchos. Tienes que poder acceder a una lista de
    * modelos relacionados con el propio modelo.
    *
    * @param string $model_name
    */
    protected function create_many_to_one_relation($model_name)
    {
        $table = strtolower($model_name); // FIXME No funcionara con tablas personalizadas.
        $data = array('table' => plural($table),
                      'model' => $model_name);
        $this->many_to_one_relations[$table] = $data;
    }

    /**
     *
     * Obtiene el array de modelos relacionados.
     *
     * @param string $name
     * @return CashewModel
     */

    private function get_one_relation($name)
    {
        $table = $this->many_to_one_relations[$name]['table'];
        $model = $this->many_to_one_relations[$name]['model'];
        $this->db->where('id', $this->args[$name.'_id']);
        $query = $this->db->get($table);
        $results = $query->result();
        $relations = array();
        foreach ($results as $result)
        {
            $relation = $this->$model->get($result->id);
            $relations[] = $relation;
        }
        if (count($relations) == 0)
        {
            return null;
        }
        return $relations[0];
    }

    /**
     * Crea una instancia a partir de un array asociativo con los atributos.
     *
     * @param array $params
     * @return CashewModel
     */
    public function create($params)
    {
        $new = new $this->class_name();
        foreach($params as $key => $value)
        {
            if (isset($new->$key))
            {
                $new->$key = $value;
            }
        }
        return $new;
    }

    /**
     * Actualiza o salva el modelo.
     *
     * @return boolean
     */
    public function save()
    {
        /**
         * Si hay un id, estamos actualizando.
         */
        if (!is_null($this->args['id']))
        {
            if (isset($this->args['updated_at']))
            {
                $this->args['updated_at'] = $this->current_datetime();
            }
            $this->db->where('id', $this->args['id']);
            if (!$this->db->update($this->table, $this->args))
            {
                return false;
            }
            $this->cashewcache->set($this->class_name.$this->args['id'], $this);
        }
        /**
         * Si no hay id, estamos creando uno nuevo.
         */
        else
        {
            if (array_key_exists('created_at', $this->args))
            {
                $this->args['created_at'] = $this->current_datetime();
            }
            if (array_key_exists('updated_at', $this->args))
            {
                $this->args['updated_at'] = $this->current_datetime();
            }
            $params = array();
            foreach($this->args as $key => $arg)
            {
                if ($key != 'id' || !is_null($arg))
                {
                    $params[$key] = $arg;
                }
            }
            if (!$this->db->insert($this->table, $params))
            {
                return false;
            }
            $this->args['id'] = $this->db->insert_id();
        }
        $this->cashewcache->set($this->class_name.$this->args['id'], $this);
        return true;
    }

    /**
     * Borra el modelo de la base de datos.
     */
    public function delete()
    {
        $this->cashewcache->delete($this->class_name.$this->args['id']);
        foreach ($this->many_to_many_relations as $table => $relation)
        {
            $this->del_all_relation($table);
        }
        if (isset($this->args['id']))
        {
            $this->db->delete($this->table, array('id' => $this->args['id']));
            $this->args['id'] = null;
        }
    }
    
    /**
     * Crea una etiqueta de un atributo.
     * 
     * @param string $attribute
     * @param string $label
     * @param string $local
     */
    protected function create_label($attribute, $label, $locale)
    {
        if (isset($this->labels[$attribute]))
        {
            $this->labels[$attribute][$locale] = $label;
        }
        else
        {
            $this->labels[$attribute] = array ($locale => $label);
        }
        
    }
    
    /**
     * Muestra la etiqueta asociada al atributo $atribute. Por defecto es el 
     * nombre del atributo si la etiqueta no esta seleccionada.
     * 
     * Si no se especifica la loca, se toma la que esta definida por defecto.
     * 
     * @param string $attribute
     * @param string $locale
     * @return string|null
     */
    public function label($attribute, $locale = null)
    {
        if (is_null($locale))
        {
            $this->load->library('CashewLanguage');
            $locale = $this->cashewlanguage->get_session_locale();
        }
        if (isset($this->$attribute))
        {
            if (isset($this->labels[$attribute]))
            {
                if (isset($this->labels[$attribute][$locale]))
                {
                    return $this->labels[$attribute][$locale];
                }
            }
            return $attribute;
        }
        return null;
    }
    
    /**
     * 
     * @param string $label
     * @return string|NULL
     */
    public function attribute($label)
    {
        foreach ($this->labels as $attribute => $info)
        {
            foreach($info as $locale => $current_label)
            {
                if ($current_label == $label)
                {
                    return $attribute;
                }
            }
        }
        return null;
    }
    
    /**
     *
     * @param string $root
     * @param boolean $expand
     * @return array
     */
   public function to_array($root = null, $expand = true)
    {
        if (is_null($root))
        {
            $root = singular($this->table);
        }
        $array = array($root => $this->args);
        // Si esta hablitada la expansion, expanden las relaciones
        if ($expand)
        {
            // Añadimos las relaciones muchos a muchos
            foreach ($this->many_to_many_relations as $table => $data)
            {
                $array[$root][$table] = array();
                $table_name = $data['table'];
                $models = $this->get_all_relation($table);
                foreach ($models as $model)
                {
                    $array[$root][$table][] = $model->to_array(null, false);
                }
            }
            // Añadimos las uno a muchos
            foreach ($this->one_to_many_relations as $table => $data)
            {
                $array[$root][$table] = array();
                $table_name = $data['table'];
                $models = $this->get_many_relation($table_name);
                foreach ($models as $model)
                {
                    $array[$root][$table][] = $model->to_array(null, false);
                }
            }
            // Añadimos los muchos a uno
            foreach ($this->many_to_one_relations as $table => $data)
            {
                $array[$root][$table] = array();
                $table_name = $data['table'];
                $model = $this->get_one_relation($table);
                if (!is_null($model))
                {
                    unset($array[$root][$table.'_id']);
                    $array_model = $model->to_array(null, false);
                    $array[$root][$table] =
                    $array_model[$table];
                }
            }
        }
        return $array;
    }
}

/**
 * 
 * @author Cuble Desarrollo S.L.
 *
 */
class CashewMongoModel extends CI_Model
{
    protected $collection_name;
    
    protected $class_name;
    
    protected $document;
    
    protected $attributes;
    
    /**
     * 
     */
    function __construct()
    {
        parent::__construct();
        
        $this->load->library('CashewMongo');
        $this->load->helper('inflector');
        
        $this->class_name = get_class($this);
        $this->collection_name = plural(strtolower(get_class($this)));
        
        // Creamos la colection, si esta no esta ya creada
        $res = $this->cashewmongo->db->execute('db.getCollectionNames()');
        if ($res['ok'] == 1 && 
                array_search($this->collection_name, $res['retval']))
        {
            $this->cashewmongo->db->createCollection($this->collection_name);
        }
        
        // Inicialmente no hay nungun documento asociado al modelo.
        $this->attributes = array();
        $this->document = null;
        
    }
    
    /**
     * Completa el modelo actual usando el cursor dado por parámetro.
     * 
     * @param array $cursor
     */
    public function fill($cursor)
    {
       foreach($cursor as $key => $value)
       {
           $this->attributes[$key] = $value;
       } 
    }
    
    /**
     * Obtiene un modelo basado en el identificador $id que se pasa por 
     * parámetro.
     * 
     * @param integer $id
     */
    public function get($id)
    {
        $collection_name = $this->collection_name;
        $collection = $this->cashewmongo->db->$collection_name;
        
        $results = $collection->find(array('_id' => "ObjectId($id)"));
        
        if (count($results) != 1)
        {
            return null;
        }
        
        $class_name = $this->class_name;
        $model = new $class_name();
        $model->fill($results[0]);
        return $model;
    } 
    
    
    /**
     * Obtiene un modelo basado en el identificador $key y $value que se pasan por 
     * parámetro.
     * 
     * @param string $key
     * @param string $value
     *  
     */
    public function get_by($key,$value)
    {
        $collection_name = $this->collection_name;
        $collection = $this->cashewmongo->db->$collection_name;
        $results = $collection->find(array($key=>$value));        
        $models = array();
        foreach ($results as $result)
        {
            $class_name = $this->class_name;
            $model = new $class_name();
            $model->fill($result);
            $models[] = $model;
        }

        if (count($models) == 0)
            return null;
        return $models[0];
    } 
    
    /**
     * 
     * @param unknown_type $criteria
     */
    public function filter($criteria = array())
    {
        $collection_name = $this->collection_name;
        $collection = $this->cashewmongo->db->$collection_name;
        
        $models = array();
        $results = $collection->find($criteria);
        foreach ($results as $result)
        {
            $class_name = $this->class_name;
            $model = new $class_name();
            $model->fill($result);
            $models[] = $model;
        }
        return $models;
    }
    
    /**
     * 
     * @return multitype:unknown
     */
    public function all()
    {
        $collection_name = $this->collection_name;
        $collection = $this->cashewmongo->db->$collection_name;
        
        $models = array();
        $results = $collection->find();
        foreach ($results as $result)
        {
            $class_name = $this->class_name;
            $model = new $class_name();
            $model->fill($result);
            $models[] = $model;
        }
        return $models;
    }
    
    /**
     * (non-PHPdoc)
     * @see CI_Model::__get()
     */
    public function __get($key)
    {
        $parent_return = parent::__get($key);
        if (is_null($parent_return))
        {
            if (!isset($this->attributes[$key]))
            {
                return null;
            }
            else
            {
                return $this->attributes[$key];
            }
        }
        return $parent_return;
    }
    
    /**
     * Guarda un atributo.
     * 
     * @param string $key
     * @param string $value
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }
    
    public function save()
    {
        $collection_name = $this->collection_name;
        $collection = $this->cashewmongo->db->$collection_name;
        
        // Creación de un nuevo documento.
        if (!isset($this->_id))
        {
            $collection->insert($this->attributes);
        }
        // Edición de un documento.
        else
       {
            $collection->update(array('_id' => $this->_id), 
                    $this->attributes);
        }
        return true;
    }
    
    public function delete()
    {
        $collection_name = $this->collection_name;
        $collection = $this->cashewmongo->db->$collection_name;
        
        if (!isset($this->_id))
        {
            $collection->remove(array('_id' => $this->_id));
        }
    }

}

/**
 * Hack para poder llamar al modelo CashewModel.
 * @author Cuble Desarollo S.L.
 */
class MY_Model extends CashewModel {}