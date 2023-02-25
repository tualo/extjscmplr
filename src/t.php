<?php 
$str = "[INF] Processing Build Descriptor : classic (production environment)";

preg_match('/(?P<level>\[(\w+)\])\s(?P<note>.+)/', $str, $matches, PREG_OFFSET_CAPTURE);
print_r($matches);