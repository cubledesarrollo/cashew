<?php
class Token extends CashewModel
{
    protected function _meta()
    {
        $this->table = 'oauth_token';
    }
    
    public function is_request()
    {
        return $this->type == 1;
    }
    public function is_access()
    {
        return $this->type == 2;
    }
}