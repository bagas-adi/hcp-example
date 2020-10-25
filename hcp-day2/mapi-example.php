<?php

include_once "env.php";

function hswift_api_url($object_name, $bucket_name){
    global $BASE_URL;
    return "https://api.$BASE_URL/swift/v1/".$object_name."/".$bucket_name;
}

function generateToken($user, $pass){
    return base64_encode($user).':'.md5($pass);
} 
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

function chargeBackReport($username,$password,$tenantName){ 
    global $BASE_URL;
    $TOKEN = "HCP ".base64_encode($username).":".md5($password);
    // $params = "start=".Date("Y-m-d", strtotime("-1 Month"))."T00:00:00-0500&end=".date("Y-m-d")."T00:00:00-0500&granularity=day";
       $params = "";//"?granularity=hour";
    $cURLConnection = curl_init(); 
    $URLConnection = "https://$tenantName.$BASE_URL:9090/mapi/tenants/".$tenantName."/chargebackReport".$params;  
    
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  // we want headers
    // curl_setopt($cURLConnection, CURLOPT_NOBODY  , true);  // we don't need body
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN,
        'Accept: text/csv',
        'Expect:'
    )); 
    $response = curl_exec($cURLConnection);  
    $data_report =  str_getcsv($response, "\n"); 
    unset($data_report[0]);  
    curl_close($cURLConnection); 
    return $response;
}

// Tenant Resource
function create_tenant($username,$password,$tenant_name, $tenant_description='-', $tenant_size){
    global $BASE_URL;
    // $username = $user;
    // $password = $password;
    // $tenant_name = $tenant_name ;
    // $tenant_size = $tenant_size; 
    $username_system = "admin"; 
    $password_system = "P@ssw0rd";
    $payload_tenant = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
    <tenant>   
    <name>$tenant_name</name>   
    <systemVisibleDescription>$tenant_description</systemVisibleDescription>
    <hardQuota>$tenant_size GB</hardQuota>
    <softQuota>90</softQuota>
    <authenticationTypes>
    <authenticationType>LOCAL</authenticationType>
    </authenticationTypes>
    <complianceConfigurationEnabled>true</complianceConfigurationEnabled>
    <versioningConfigurationEnabled>true</versioningConfigurationEnabled>
    <searchConfigurationEnabled>true</searchConfigurationEnabled>
    <replicationConfigurationEnabled>true</replicationConfigurationEnabled> 
    <tags>
    <tag>$tenant_name</tag>   
    </tags> 
    <servicePlanSelectionEnabled>false</servicePlanSelectionEnabled> 
    </tenant>";

    $TOKEN_SYSTEM = "HCP ".base64_encode($username_system).":".md5($password_system);  

    $cURLConnection = curl_init(); 
    $URLConnection = "https://admin.$BASE_URL:9090/mapi/tenants?username=".$username."&password=".$password; 

    // echo $URLConnection."<br/><br/><br/>";
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  // we want headers
    // curl_setopt($cURLConnection, CURLOPT_NOBODY  , true);  // we don't need body 
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_SYSTEM,
        // 'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant) ,
        // "Expect:"
    ));
    $response_code = curl_exec($cURLConnection);  
    // $response_code = explode(" ", $response_code); 
    $response_code = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $response_code;
}

function edit_tenant($tenant_name,$tenant_description, $tenant_size){ 
    global $BASE_URL;
    $username_system = "admin"; 
    $password_system = "P@ssw0rd";
    $payload_tenant = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
    <tenant>     
    <systemVisibleDescription>
        $tenant_description
    </systemVisibleDescription>
    <hardQuota>$tenant_size GB</hardQuota>
    </tenant>";

    $TOKEN_SYSTEM = "HCP ".base64_encode($username_system).":".md5($password_system);  

    $cURLConnection = curl_init(); 
    $URLConnection = "https://admin.$BASE_URL:9090/mapi/tenants/".$tenant_name;  
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_POST, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true); 
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_SYSTEM,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant),
        "Expect:"
    ));
    $response_code = curl_exec($cURLConnection); 
    $response_code = get_headers_from_curl_response($response_code);
    // $response_code = explode(" ", $response_code); 
    curl_close($cURLConnection);  
    return ($response_code);
}

function del_tenant($tenant_name){ 
    global $BASE_URL;
    $username_system = "admin"; 
    $password_system = "P@ssw0rd"; 
    $TOKEN_SYSTEM = "HCP ".base64_encode($username_system).":".md5($password_system);  

    $cURLConnection = curl_init(); 
    $URLConnection = "https://admin.$BASE_URL:9090/mapi/tenants/".$tenant_name;  
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_SYSTEM,
        'Accept: application/xml' 
    ));
    $response_code = curl_exec($cURLConnection);  
    $response_code = get_headers_from_curl_response($response_code);
    // $response_code = explode(" ", $response_code); 
    curl_close($cURLConnection);  
    return ($response_code);
}
function set_user_policy($tenant_name,$username,$password){ 
    global $BASE_URL;
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/userAccounts/".$username;
    // echo $URLConnection."<br/><br/><br/>";
    $TOKEN_USER =  "HCP ".base64_encode($username).":".md5($password); 
    $payload_tenant = 
    "<userAccount>
        <roles>
            <role>SECURITY</role>
            <role>ADMINISTRATOR</role>
            <role>COMPLIANCE</role>
            <role>MONITOR</role>
        </roles>
    </userAccount>";
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_POST, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  // we want headers
    // curl_setopt($cURLConnection, CURLOPT_NOBODY  , true);  // we don't need body 
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);

    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant),
        "Expect:"
    ));

    $response_code = curl_exec($cURLConnection); 
    $headers = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $headers; 

}
function assign_namespace_minimumAccess($tenant_name,$namespace_name,$token){
    global $BASE_URL;
    //Tenant-level user
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/namespaces/".$namespace_name;
    $TOKEN_USER =  "HCP ".$token ; //.base64_encode($username).":".md5($password);
    $payload_tenant = "
    <namespace>
        <authMinimumPermissions> 
            <permission>READ</permission>
            <permission>WRITE</permission>
        </authMinimumPermissions>
        <authAndAnonymousMinimumPermissions>
            <permission>READ</permission>  
        </authAndAnonymousMinimumPermissions> 
    </namespace>";
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_POST, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant),
        "Expect:"
    ));
    
    $response_code = curl_exec($cURLConnection);  
    $response_code = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $response_code;
}


// Namespace Resource
function create_namespace($tenant_name,$namespace_name,$namespace_desc,$namespace_size,$token){ 
    global $BASE_URL;
    $namespace_desc = $namespace_desc ?? 'No Description';
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/namespaces";
    $TOKEN_USER =  "HCP ".$token ; 
    $payload_tenant = "<namespace>
        <aclsUsage>ENFORCED</aclsUsage>
        <name>".$namespace_name."</name>
        <description>".$namespace_desc."</description>
        <hashScheme>SHA-256</hashScheme>
        <enterpriseMode>true</enterpriseMode>
        <hardQuota>".$namespace_size." GB</hardQuota>
        <softQuota>75</softQuota>
        <versioningSettings>
            <enabled>true</enabled> 
            <prune>true</prune>
            <pruneDays>0</pruneDays>
            <keepDeletionRecords>false</keepDeletionRecords>
        </versioningSettings>
        <authUsersAlwaysGrantedAllPermissions>
            false
        </authUsersAlwaysGrantedAllPermissions>
        <optimizedFor>ALL</optimizedFor> 
        <replicationEnabled>false</replicationEnabled>
        <readFromReplica>false</readFromReplica>
        <searchEnabled>false</searchEnabled>
        <indexingEnabled>false</indexingEnabled>
        <customMetadataIndexingEnabled>false</customMetadataIndexingEnabled>
        <serviceRemoteSystemRequests>true</serviceRemoteSystemRequests>
        <tags><tag>".$namespace_name."</tag></tags>
    </namespace>";

     
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  // we want headers
    // curl_setopt($cURLConnection, CURLOPT_NOBODY  , true);  // we don't need body
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant),
        "Expect:"
    ));
    $response_code = curl_exec($cURLConnection); 
    $response_code = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $response_code;
} 

function edit_namespace($tenant_name,$namespace_name,$namespace_desc,$namespace_size,$namespace_old_name,$token){ 
    global $BASE_URL;
    $namespace_desc = $namespace_desc ?? 'No Description'; 
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/namespaces/".$namespace_old_name;
    $TOKEN_USER =  "HCP ".$token ; //.base64_encode($username).":".md5($password);
    $payload_tenant = "
    <namespace> 
        <name>$namespace_name</name>
        <description>$namespace_desc</description>
        <hardQuota>$namespace_size GB</hardQuota>
        <softQuota>75</softQuota>
    </namespace>";
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_POST, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  // we want headers
    // curl_setopt($cURLConnection, CURLOPT_NOBODY  , true);  // we don't need body
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant),
        "Expect:"
    ));
    $response_code = curl_exec($cURLConnection);  
    $headers = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $headers;
}

function del_namespace($tenant_name,$namespace,$token){ 
    global $BASE_URL;
    // $username_system = "admin"; 
    // $password_system = "P@ssw0rd";
    // $TOKEN_SYSTEM = "HCP ".base64_encode($username_system).":".md5($password_system);  
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/namespaces/".$namespace;
    $TOKEN_USER =  "HCP ".$token ;  
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);   
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml' 
    ));
    $response_code = curl_exec($cURLConnection);
    $response_code = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $response_code; 
}

// User Account Resource
function create_user_account($tenant_name,$username,$userdesc,$password, $token){ 
    global $BASE_URL;
    $userdesc = $userdesc ?? 'No Description';
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/userAccounts?password=".$password;
    // echo $URLConnection."<br/><br/><br/>";
    $TOKEN_USER =  "HCP ".$token; //base64_encode($username).":".md5($password); 
    // fullName, enabled, forcePasswordChange, localAuthentication
    $payload_tenant = "
    <userAccount>
        <username>".$username."</username>
        <description>".$userdesc."</description>
        <fullName>".$username."</fullName> 
        <localAuthentication>true</localAuthentication>
        <forcePasswordChange>false</forcePasswordChange>
        <enabled>true</enabled>
        <allowNamespaceManagement>true</allowNamespaceManagement>
    </userAccount>";
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true); 
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);

    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant) ,
        "Expect:"
    ));

    $response_code = curl_exec($cURLConnection); 
    $headers = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $headers; 
}
function edit_user_account($tenant_name,$username,$oldusername,$userdesc, $token){ 
    global $BASE_URL;
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/userAccounts/".$oldusername;
    // echo $URLConnection."<br/><br/><br/>";
    $TOKEN_USER =  "HCP ".$token; //base64_encode($username).":".md5($password); 
    // fullName, enabled, forcePasswordChange, localAuthentication
    $payload_tenant = "
    <userAccount>
        <username>".$username."</username>
        <description>".$userdesc."</description>  
    </userAccount>";
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_POST, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  // we want headers
    // curl_setopt($cURLConnection, CURLOPT_NOBODY  , true);  // we don't need body 
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);

    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant) ,
        "Expect:"
    ));

    $response_code = curl_exec($cURLConnection);  
    $headers = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $headers; 
}
function del_user_account($tenant_name,$username, $token){ 
    global $BASE_URL;
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/userAccounts/".$username; 
    $TOKEN_USER =  "HCP ".$token;  
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml'
    ));

    $response_code = curl_exec($cURLConnection);  
    $headers = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $headers; 
}
function editPassword_user_account($tenant_name,$username,$password, $token){ 
    // TODO : example
    // $response_code = editPassword_user_account(
    //     $db_tenant[0]->object_name,
    //     $get_user[0]->username,
    //     "coba123", 
    //     $db_tenant[0]->object_token
    // );

    global $BASE_URL;

    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/userAccounts/".$username."?password=".$password; 
    $TOKEN_USER =  "HCP ".$token;  
    $payload_tenant = " 
    <userAccount>
    </userAccount>";
    $cURLConnection = curl_init(); 
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_POST, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);   
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  // we want headers 
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);

    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant) 
    ));

    $response_code = curl_exec($cURLConnection);  
    $response_code = explode(" ", $response_code); 
    curl_close($cURLConnection);  
    return trim($response_code[1]); 

}


function assign_namespace_permission($tenant_name,$username,$namespace_name,$permission_list,$token){ 
    global $BASE_URL;
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/userAccounts/".$username."/dataAccessPermissions";
    $TOKEN_USER =  "HCP ".$token ; //.base64_encode($username).":".md5($password);
    $payload_tenant = "<dataAccessPermissions>
        <namespacePermission>
        <namespaceName>$namespace_name</namespaceName>
        $permission_list
        </namespacePermission> 
    </dataAccessPermissions>";
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_POST, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant),
        "Expect:"
    ));
    
    $response_code = curl_exec($cURLConnection);  
    $response_code = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $response_code;
}

function assign_namespace_protocols($tenant_name,$namespace_name,$protocols_list){
    global $BASE_URL;
    //System-level user
    $username_system = "admin"; 
    $password_system = "P@ssw0rd";
    $TOKEN_SYSTEM = "HCP ".base64_encode($username_system).":".md5($password_system); 
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/namespaces/".$namespace_name."/protocols";
    $payload_tenant = "<protocols>".$protocols_list."</protocols>";
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_POST, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_SYSTEM,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant),
        "Expect:"
    ));
    
    $response_code = curl_exec($cURLConnection);  
    $response_code = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $response_code;
}

function assign_namespace_httpProtocol($tenant_name,$namespace_name,$protocols_list,$token){
    global $BASE_URL;
    //Tenant-level user
    $URLConnection = "https://".$tenant_name.".$BASE_URL:9090/mapi/tenants/".$tenant_name."/namespaces/".$namespace_name."/protocols/http";
    $TOKEN_USER =  "HCP ".$token ; //.base64_encode($username).":".md5($password);
    $payload_tenant = "<httpProtocol>".$protocols_list."</httpProtocol>";
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $URLConnection);
    curl_setopt($cURLConnection, CURLOPT_POST, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURLConnection, CURLOPT_VERBOSE, true);
    curl_setopt($cURLConnection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HEADER  , true);  
    curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $payload_tenant);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Authorization: '.$TOKEN_USER,
        'Accept: application/xml',
        'Content-Type: application/xml',
        'Content-Length: ' . strlen($payload_tenant),
        "Expect:"
    ));
    
    $response_code = curl_exec($cURLConnection);  
    $response_code = get_headers_from_curl_response($response_code); 
    curl_close($cURLConnection);  
    return $response_code;
}

function getLinkBucket($tenant_name, $namespace_name){
    global $BASE_URL;
    return "https://".$namespace_name.".".$tenant_name.".$BASE_URL/";
}
function getLinkTenant($tenant_name){
    global $BASE_URL;
    return "https://".$tenant_name.".$BASE_URL/";
}



