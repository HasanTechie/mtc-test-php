<?php
function antiInjection($str)
{
    global $connection;
    return mysqli_real_escape_string($connection, $str);
}

function antiXSS($str)
{
    return htmlspecialchars($str);
}