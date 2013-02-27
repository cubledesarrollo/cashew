<?php
/**
 * 
 * @author Marcos Gabarda
 *
 */
class Node
{
    public $key = null;
    public $words = 0;
    public $children = array();
    public $parent = null;
    
    /**
     * 
     * @param string $word
     */
    function append($word)
    {
        if (count($word) >= 1)
        {
            $first = substr($word, 0, 1);
            if (array_key_exists($first, $this->children))
            {
                $node = $this->children[$first];
            }
            else
            {
                $node = new Node();
                $node->key = $first;
                $node->parent = $this;
                $this->children[$first] = $node;
            }
            $node->append(substr($word, 1));
        }
        else
        {
            $this->words++;
            $parent = $this->parent;
            while($parent)
            {
                $parent->words++;
                $parent = $parent->parent;
            }
        }
    }
}

/**
 * 
 * @author Marcos Gabarda
 *
 */
 class CashewSearch
{
    /**
     * 
     * @var array
     */
    private $data = array();
    
    public function add($word)
    {
        if (!empty($word))
        {
            $first = substr($word, 0, 1);
            $node = null;
            if (!array_key_exists($first, $this->data))
            {
                $node = new Node();
                $node->key = $first;
                $this->data[$first] = $node;
            }
            else
            {
                $node = $this->data[$first];
            }
            $node->append(substr($word, 1));
        }
    }
}