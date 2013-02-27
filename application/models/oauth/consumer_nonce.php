<?php
class Consumer_nonce extends CashewModel
{
    protected function _meta()
    {
        $this->table = 'oauth_consumer_nonce';
    }
}