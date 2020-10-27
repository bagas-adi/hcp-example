<?php
include_once "hs3-example.php"; 
header('Content-Type: application/xml');
$return = get_bucket_versioning('apps','versioning-namespace-2'); //get_info('https://apps.hcp.lab.lo/hs3'); 
// $return = put_bucket_versioning('apps','versioning-namespace-2',$is_versioning_enabled=true);
print_r($return);