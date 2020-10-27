<?php

include_once "env.php";



function put_bucket_versioning($tenant,$bucket,$is_versioning_enabled=false){
    global $BASE_URL;
    $username = "admin-apps";
    $password = "P@ssw0rd"; 
    $TOKEN = "HCP ".utf8_encode(base64_encode($username)).":".utf8_encode(md5($password)); 
    $URLConnection = "https://$tenant.$BASE_URL/hs3/$bucket?versioning";
    $cURLConnection = curl_init(); 
    if($is_versioning_enabled){
        $versioning='Enabled';
    } else {
        $versioning='Suspended';
    }
    $payload_tenant = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
    <VersioningConfiguration
        xmlns="http://s3.amazonaws.com/doc/2006-03-01/">
        <Status>'.$versioning.'</Status>
    </VersioningConfiguration>
';
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN, 
        'x-hcp-pretty-print: true', 
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant),
    )); 
    $response = curl_exec($cURLConnection);   
    // $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 

    return $response;
}

function get_bucket_versioning($tenant,$bucket){
    global $BASE_URL;
    $username = "admin-apps";
    $password = "P@ssw0rd"; 
    $TOKEN = "HCP ".utf8_encode(base64_encode($username)).":".utf8_encode(md5($password)); 
    $URLConnection = "https://$tenant.$BASE_URL/hs3/$bucket?versioning";
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);  
    // curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN, 
        'x-hcp-pretty-print: true', 
    )); 
    $response = curl_exec($cURLConnection);   
    // $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 

    return $response;
}
// $return = get_bucket_acl('apps','versioning-namespace');


function get_bucket_acl($tenant,$bucket){
    global $BASE_URL;
    $username = "admin-apps";
    $password = "P@ssw0rd"; 
    $TOKEN = "HCP ".utf8_encode(base64_encode($username)).":".utf8_encode(md5($password)); 
    $URLConnection = "https://$tenant.$BASE_URL/hs3/$bucket?acl";
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);  
    // curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN, 
        'x-hcp-pretty-print: true', 
    )); 
    $response = curl_exec($cURLConnection);   
    // $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 

    return $response;
}
// $return = get_bucket_acl('apps','versioning-namespace');

function put_bucket_acl($tenant,$bucket){
    global $BASE_URL;
    $username = "admin-apps";
    $password = "P@ssw0rd"; //.gmdate('Ymd\THis\Z');
    $awsAccessKey = base64_encode(utf8_encode("admin-apps"));
    $awsSecretKey = utf8_encode(base64_encode(hash("sha1", $password)));//md5(utf8_encode("P@ssw0rd"),TRUE);
    // $TOKEN = utf8_encode("HCP ".$awsAccessKey.":".$awsSecretKey);
    $TOKEN = "HCP ".utf8_encode(base64_encode($username)).":".utf8_encode(md5($password)); 
    $URLConnection = "https://$tenant.$BASE_URL/hs3/$bucket?acl";
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN, 
        'x-hcp-pretty-print: true',
        'x-amz-grant-read: emailAddress=all_users',
        'x-amz-grant-write: emailAddress=admin-apps,emailAddress=bagas',
        'x-amz-grant-read-acp: emailAddress=admin-apps',
        'x-amz-grant-write-acp: emailAddress=admin-apps',
        // 'Date: '. utf8_encode(gmdate('D, d M Y H:i:s T'))
    )); 
    $response = curl_exec($cURLConnection);   
    // $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 

    return $response;
}
// $return = put_bucket_acl('apps','versioning-namespace');

function put_bucket($tenant,$bucket){
    global $BASE_URL;
    $username = "admin-apps";
    $password = "P@ssw0rd"; //.gmdate('Ymd\THis\Z');
    $awsAccessKey = base64_encode(utf8_encode("admin-apps"));
    $awsSecretKey = utf8_encode(base64_encode(hash("sha1", $password)));//md5(utf8_encode("P@ssw0rd"),TRUE);
    // $TOKEN = utf8_encode("HCP ".$awsAccessKey.":".$awsSecretKey);
    $TOKEN = "HCP ".utf8_encode(base64_encode($username)).":".utf8_encode(md5($password)); 
    $URLConnection = "https://$tenant.$BASE_URL/hs3/$bucket";
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN,
        'Content-Type: application/xml',
        'x-hcp-pretty-print: true'
        // 'Date: '. utf8_encode(gmdate('D, d M Y H:i:s T'))
    )); 
    $response = curl_exec($cURLConnection);   
    // $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 

    return $response;
}
// $return = put_bucket('https://apps.hcp.lab.lo/hs3/<nama-bucket-baru>');

function get_info($URLConnection){
    $username = "admin-apps";
    $password = "P@ssw0rd"; //.gmdate('Ymd\THis\Z'); 
    // $TOKEN = utf8_encode("HCP ".$awsAccessKey.":".$awsSecretKey);
    $TOKEN = "HCP ".utf8_encode(base64_encode($username)).":".utf8_encode(md5($password)); 

    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true); 
    // curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    // curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN,
        'Content-Type: application/xml',
        'x-hcp-pretty-print: true'
        // 'Date: '. utf8_encode(gmdate('D, d M Y H:i:s T'))
    )); 
    $response = curl_exec($cURLConnection);   
    // $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 

    return $response;
}
// header('Content-Type: application/xml');
// $return = get_info('https://apps.hcp.lab.lo/hs3'); 

// print_r($return);
 