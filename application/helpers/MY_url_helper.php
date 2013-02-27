<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * URL String
 *
 * Returns the URL or null.
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('before_url'))
{
    function before_url()
    {
        if (!session_id())
        {
            session_start();
        }
        if(isset($_SESSION['before_url']))
        {
            return $_SESSION['before_url'];
        }
        return null;
    }
}