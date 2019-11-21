<?php

function antiXSS($str) //to preventing Cross-site scripting (XSS)
{
    return htmlspecialchars($str);
}

function truncate($string, $length, $dots = "...")
{
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

function generateSelect($name = '', $options = array(), $default = 1) //preselect the selected option from database
{
    $html = '<select class="form-control" name="' . $name . '">';
    foreach ($options as $option => $value) {
        if ($option == $default) {
            $html .= '<option value=' . $value . ' selected="selected">' . $option . '</option>';
        } else {
            $html .= '<option value=' . $value . '>' . $option . '</option>';
        }
    }
    $html .= '</select>';
    return $html;
}