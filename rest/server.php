<?php
$arguments = file_get_contents('php://input');
$arguments = unserialize($arguments);
print_r($arguments);
