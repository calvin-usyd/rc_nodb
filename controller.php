<?php
//this controller is separate from the another for security reason to control path authorization.

foreach (glob("classes/*.php") as $filename)
{
    include $filename;
}

require "CommonController.php";

?>