<?php

include_once "env.php"; 

function get_headers_from_curl_response($response)
{
    $headers = array(); 
    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

    foreach (explode("\r\n", $header_text) as $i => $line)
        if ($i === 0)
            $headers['http_code'] = $line;
        else
        {
            list ($key, $value) = explode(': ', $line); 
            $headers[$key] = $value;
        }

    return $headers;
}

function get_metadata($URLConnection,$filename,$annotation_name){
    $username = "admin";
    $password = "P@ssw0rd";
    $TOKEN = "HCP ".base64_encode($username).":".md5($password);

    $cURLConnection = curl_init();   
    $fp = fopen("downloads/".$filename, "wb"); 
    $URLConnection.= "?type=custom-metadata&annotation=$annotation_name";
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_HEADER, false);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($cURLConnection, CURLOPT_ENCODING , "gzip");
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN
    )); 
    $response = curl_exec($cURLConnection);  
    fwrite($fp, $response);  
    $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);

    curl_close($cURLConnection); 
    fclose($fp);
    // $response = get_headers_from_curl_response($response);
    return $response;
}
// $return = get_object("https://teacher.$HCP_URL/rest/examples/centos-1-logo-png-transparent.png","file_baru.png");


function get_object($URLConnection,$filename){
    $username = "admin";
    $password = "P@ssw0rd";
    $TOKEN = "HCP ".base64_encode($username).":".md5($password);

    $cURLConnection = curl_init();   
    $fp = fopen("downloads/".$filename, "wb"); 
          
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    // curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    // curl_setopt($cURLConnection, CURLOPT_HEADER, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($cURLConnection, CURLOPT_ENCODING , "gzip");
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN
    )); 
    $response = curl_exec($cURLConnection);  
    fwrite($fp, $response);  
    // $response = get_headers_from_curl_response($response);
    $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 
    fclose($fp);

    return $response;
}
// $return = get_object("https://teacher.$HCP_URL/rest/examples/centos-1-logo-png-transparent.png","file_baru.png");


function get_object_gzip($URLConnection,$filename){
    $username = "admin";
    $password = "P@ssw0rd";
    $TOKEN = "HCP ".base64_encode($username).":".md5($password);

    $cURLConnection = curl_init();   
    $fp = fopen("downloads/".$filename, "wb"); 
          
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    // curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    // curl_setopt($cURLConnection, CURLOPT_HEADER, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_ENCODING , "gzip");
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN,
        'Accept-Encoding: gzip'
    )); 
    $response = curl_exec($cURLConnection);  
    fwrite($fp, $response);  
    // $response = get_headers_from_curl_response($response);
    $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 
    fclose($fp);

    return $response;
}
// $return = get_object("https://teacher.$HCP_URL/rest/examples/centos-1-logo-png-transparent.png","file_baru.png");


function put_object($URLConnection,$file_name){
    $username = "bagas";
    $password = "P@ssw0rd"; 
    $TOKEN = "HCP ".base64_encode($username).":".md5($password);

    $path = "uploads/file_upload.png";
    $data = file_get_contents($path);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->buffer($data);
    $URLConnection .= $file_name ;
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL,$URLConnection);
    curl_setopt($cURLConnection, CURLOPT_TIMEOUT, 60); //timeout after 30 seconds
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($cURLConnection, CURLOPT_HEADER, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        "Authorization: ".$TOKEN,
        "Content-Type: ".$type,
        "Expect: "
        )
    );
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data);

    // Make the REST call, returning the result
    $response = curl_exec($cURLConnection);
    // $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    $httpcode = get_headers_from_curl_response($response);
    curl_close($cURLConnection);
    return $httpcode;
}
// $return = put_object("https://non-versioning-namespace.$HCP_URL/rest/examples/","file_baru.png");
// $return = put_object("https://versioning-namespace.$HCP_URL/rest/examples/","file_baru.png");


function put_object_gzip($URLConnection,$file_name){
    $username = "bagas";
    $password = "P@ssw0rd"; 
    $TOKEN = "HCP ".base64_encode($username).":".md5($password);

    $path = "uploads/file_upload.png";
    $data = gzencode( file_get_contents($path));
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->buffer($data);
    $URLConnection .= $file_name ;
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL,$URLConnection);
    curl_setopt($cURLConnection, CURLOPT_TIMEOUT, 60); //timeout after 30 seconds
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($cURLConnection, CURLOPT_HEADER, true);
    curl_setopt($cURLConnection, CURLOPT_ENCODING , "gzip");
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        "Authorization: ".$TOKEN,
        "Content-Type: ".$type,
        "Content-Encoding: gzip",
        "Transfer-Encoding: chunked",
        "Expect: "
        )
    );
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data);

    // Make the REST call, returning the result
    $response = curl_exec($cURLConnection);
    // $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    $httpcode = get_headers_from_curl_response($response);
    curl_close($cURLConnection);
    return $httpcode;
}
// $return = put_object_gzip("https://non-versioning-namespace.$HCP_URL/rest/examples/","file_baru.png");
// $return = put_object_gzip("https://versioning-namespace.$HCP_URL/rest/examples/","file_baru.png");


function put_metadata($URLConnection,$file_name,$annotation_name){
    $username = "bagas";
    $password = "P@ssw0rd"; 
    $TOKEN = "HCP ".base64_encode($username).":".md5($password);
    
    $path_annotation = "uploads/custom_metadata.xml";
    $meta_size = filesize($path_annotation);
    $data_annotation = file_get_contents($path_annotation);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $type = $finfo->buffer($data_annotation);
    $URLConnection .= $file_name."?type=custom-metadata&annotation=".$annotation_name;
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL,$URLConnection);
    curl_setopt($cURLConnection, CURLOPT_TIMEOUT, 60); //timeout after 30 seconds
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($cURLConnection, CURLOPT_HEADER, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        "Authorization: ".$TOKEN,
        "Content-Type: ".$type,
        // "X-HCP-Size: ". ($obj_size+$meta_size),
        // "expect: "
        )
    );
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data_annotation);

    // Make the REST call, returning the result
    $response = curl_exec($cURLConnection);
    // $httpcode = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    $httpcode = get_headers_from_curl_response($response);
    curl_close($cURLConnection);
    return $httpcode;
}
// $return = put_object_with_metadata("https://non-versioning-namespace.$HCP_URL/rest/examples/","file_baru.png");
// $return = put_object_with_metadata("https://versioning-namespace.$HCP_URL/rest/examples/","file_baru.png");


function delete_object($URLConnection){
    $username = "bagas";
    $password = "P@ssw0rd";
    $TOKEN = "HCP ".base64_encode($username).":".md5($password); 


    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_TIMEOUT, 60); //timeout after 30 seconds
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN
    )); 
    $response = curl_exec($cURLConnection);   
    $response = get_headers_from_curl_response($response);
    // $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 

    return $response;
}
// $return = delete_object("https://non-versioning-namespace.$HCP_URL/rest/file_baru.png");
// print_r($return);


function get_info($URLConnection){
    $username = "bagas";
    $password = "P@ssw0rd";
    $TOKEN = "HCP ".base64_encode($username).":".md5($password); 

    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN,
        'Content-Type: text/xml'
    )); 
    $response = curl_exec($cURLConnection);   
    // $response = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
    curl_close($cURLConnection); 

    return $response;
}
// header('Content-Type: application/xml');
// $return = get_info("https://$HCP_IP/proc");

// print_r($return);
 