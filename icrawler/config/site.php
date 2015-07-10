<?php 
global $sites;

$sites = array(
    'http://www.smartlei.com',
);
return $config = array(
    md5($sites[0]) => array(
        'crawler' => 'snoopy',
        'parser'  => 'dom', 
    ), 
    md5(md5($sites[0])) => array(
        'crawler' => 'snoopy',
        'parser'  => 'dom', 
    ), 
);
