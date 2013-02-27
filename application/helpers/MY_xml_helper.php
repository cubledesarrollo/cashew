<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function array_to_xml($array = array(), $lastkey = 'root', $xml = null)
{
    $buffer="<".$lastkey.">";
    if (!is_array($array))
    {
        if (is_numeric($array))
        {
            $buffer .= $array;
        }
        else
        {
            $buffer .= xml_convert(nl2br($array));
        }
    }
    else
    {
        foreach($array as $key=>$value)
        {
            if (is_array($value))
            {
                if ( is_numeric($key))
                {
                    foreach($value as $bkey=>$bvalue)
                    {
                        $buffer.=array_to_xml($bvalue, $bkey);
                    }
                }
                else
                {
                    $buffer.=array_to_xml($value,$key);
                }
            }
            else
            {
                    $buffer.=array_to_xml($value,$key);
            }
        }
    }
    $buffer.="</".$lastkey.">";
    return $buffer;
}