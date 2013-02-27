<?php
function unix_to_human($time = '', $seconds = FALSE, $fmt = 'us')
{
    $r  = date('d', $time).'/'.date('m', $time).'/'.date('Y', $time).' ';

    if ($fmt == 'us')
    {
        $r .= date('h', $time).':'.date('i', $time);
    }
    else
    {
        $r .= date('H', $time).':'.date('i', $time);
    }

    if ($seconds)
    {
        $r .= ':'.date('s', $time);
    }

    if ($fmt == 'us')
    {
        $r .= ' '.date('A', $time);
    }

    return $r;
}