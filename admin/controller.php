<?php
//ini_set('display_errors', 'On');
//ini_set('html_errors', 0);

foreach (glob("../classes/*.php") as $filename)
{
    include $filename;
}

require "../CommonController.php";

?>