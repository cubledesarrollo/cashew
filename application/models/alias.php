<?php
/**
 * Modelo para los alias.
 * 
 * @author Marcos Gabarda
 *
 */
class Alias extends CashewModel
{
    
    /**
     *
     * @var string
     */
    private $hash_function = 'sha256';
    
    /**
     * (non-PHPdoc)
     * @see CashewModel::_meta()
     */
    protected function _meta()
    {
        $this->create_many_to_one_relation('Category');
    }
    
    /**
     * Sobrecarga para automatizar el hash del valor del alias.
     *
     * @see CashewModel::__set()
     */
    public function __set($key, $value)
    {
        if ($key == 'value')
        {
            $this->hash_value = hash($this->hash_function, $value);
        }
        parent::__set($key, $value);
    }
}