<?php 
global $sites;

$sites = array(
    'http://www.qqgexingqianming.com',
);
return $config = array(
    md5('http://www.qqgexingqianming.com') => array(
        'crawler' => 'snoopy',
        'parser'  => 'dom', 
    ), 
    md5(md5('http://www.qqgexingqianming.com')) => array(
        'crawler' => 'snoopy',
        'parser'  => 'dom', 
    ), 
);
