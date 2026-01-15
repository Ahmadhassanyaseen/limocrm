<?php include_once "logs/logger.php"; ?>
<?php

function curlRequest($data) {
    $api_url = 'https://zabrin.xyz/limogen/index.php?entryPoint=CustomEntryPoint';
    
    // Log the request data
    log_message("Sending to API: " . print_r($data, true));
    
    $curl = curl_init($api_url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Only for debugging, remove in production
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Only for debugging, remove in production
    
    $response = curl_exec($curl);
    
    // Log any cURL errors
    if (curl_errno($curl)) {
        $curlError = curl_error($curl);
        log_message("cURL Error: " . $curlError, 'ERROR');
    }
    
    // Log the raw response
    log_message("API Response: " . $response);
    
    curl_close($curl);
    
    $decoded = json_decode($response, true);
    
    // Log if JSON decode failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        $jsonError = json_last_error_msg();
        log_message("JSON Decode Error: " . $jsonError, 'ERROR');
    }
    
    return $decoded;
}

function fetchAllUserLeads($data){
    $data["action"] = "fetchAllUserLeads";
    return curlRequest($data);
}