<?php
/**
 * Modelo para una categoría.
 * 
 * @author Marcos Gabarda
 *
 */
class Category extends CashewModel
{

    /**
     * 
     * @var string
     */
    private $hash_function = 'sha256';
    
    /**
     * Crear relaciones con otros modelos.
     * @see CashewModel::_meta()
     */
    protected  function _meta()
    {
        $this->create_one_to_many_relation('Alias');
    }
    
    /**
     * Devuelve TRUE si la categoría es raíz, FALSE en caso contrario.
     * 
     * @return boolean 
     */
    public function is_root()
    {
        return is_null($this->category_id);
    }
    
    /**
     * Sobrecarga para devolver la categoría padre o null en caso de que sea 
     * raiz. 
     * 
     * @see CashewModel::__get()
     */
    public function __get($key)
    {
        if ($key == 'parent')
        {
            if ($this->is_root())
            {
                return null;
            }
            return $this->get($this->category_id);
        }
        return parent::__get($key);
    }
     
    
    /**
     * Sobrecarga para poder asignar una categoría directamente al atributo 
     * parent.
     * 
     * @see CashewModel::__set()
     */
    public function __set($key, $value)
    {
        if ($key == 'parent')
        {
            if (is_null($value))
            {
                $this->category_id = null;
            }
            else if (get_class($value) == __CLASS__ &&
                    !is_null($value->id))
            {
                $this->category_id = $value->id;
            }
        }
        else
        {
            if ($key == 'name')
            {
                $this->hash_name = hash($this->hash_function, $value);
            }
            parent::__set($key, $value);
        }
    }
    
    /**
     * Obtiene las categorias hijas.
     * 
     * @return array
     */
    public function children()
    {
        $children = $this->filter(array('category_id' => $this->id));
        return $children;
    }
    
    /**
     * 
     * @param string $alias
     * @return boolean
     */
    public function add_alias($alias)
    {
        $aliases = $this->Alias->filter(array('hash_value' => 
                hash($this->hash_function, $alias),
                        'category_id' => $this->id));
        if (count($aliases) == 0)
        {
            $alias_model = new Alias();
            $alias_model->value = $alias;
            $alias_model->category_id = $this->id;
            return $alias_model->save();
        }
        return false;
    }
    
    /**
     * 
     * @param string $alias
     * @return array
     */
    public function filter_by_alias($alias_name)
    {
        $aliases = $this->Alias->filter(array('hash_value' =>
                hash($this->hash_function, $alias_name)));
        $categories = array();
        foreach ($aliases as $alias)
        {
            $categories[] = $this->get($alias->category_id);
        }
        return $categories;
    }
}