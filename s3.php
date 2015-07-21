<?PHP

function gs_getStringToSign($request_type, $expires, $uri) {
   return "$request_type\n\n\n$expires\n$uri";
}

function gs_encodeSignature($s, $key) {
    $s = utf8_encode($s);
    $s = hash_hmac('sha1', $s, $key, true);
    $s = base64_encode($s);
    return urlencode($s);
}
 
function gs_prepareS3URL($file, $bucket) {
 
  $awsKeyId = "accesskey"; 
  $awsSecretKey = "secretkey"; 
 
 
  $file = rawurlencode($file); 
  $file = str_replace('%2F', '/', $file);
  $path = $bucket .'/'. $file;
 
  $expires = strtotime('+1 hour');
 
  $stringToSign = gs_getStringToSign('GET', $expires, "/$path"); 
  $signature = gs_encodeSignature($stringToSign, $awsSecretKey); 

  $url = "http://$bucket.s3.amazonaws.com/$file";
  $url .= '?AWSAccessKeyId='.$awsKeyId
         .'&Expires='.$expires
         .'&Signature='.$signature;
         
  return $url;
}

$file = "win.jpg";
$bucket = "bucket";
$link = gs_prepareS3URL($file, $bucket);
