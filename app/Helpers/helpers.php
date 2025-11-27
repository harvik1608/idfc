<?php
if (! function_exists('preview')) {
    function preview($data) {
        echo "<pre>";
        print_r ($data);
        exit;
    }
}

if (! function_exists('format_date')) {
    function format_date($date) {
        return date("d M, Y",strtotime($date));
    }
}

if (! function_exists('format_text')) {
    function format_text($text) {
        return ucwords(strtolower($text));
    }
}

if (! function_exists('currency')) {
    function currency() {
        return "₹";
    }
}
