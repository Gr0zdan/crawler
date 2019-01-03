<?php

$content = file_get_contents("index.html");
$content = explode('    ', $content);
$contents = [];
while(count($content)){
    if($content[0] === '')
        $content = array_slice($content, 1);
    else {
        $contents[] = $content[0];
        $content = array_slice($content, 1);
    }
}
print_r($contents);

//need to experiment a bit to be able to edit it properly