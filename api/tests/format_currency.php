<?php
    $fmt = numfmt_create( 'pt_BR', NumberFormatter::CURRENCY );
    echo numfmt_format_currency($fmt, 1234567.891234567890000, "R$ ")."\n";
?>