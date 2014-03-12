<?php ob_start();?>
<?php

/* Example "Get user orders" request */  

$user_number = "";
$api_key = "";
$api_url = 'http://api.btceur.eu/private/btceur/orders/';

echo '<pre>';
print_r(jsonCurlRequest($api_url, $user_number.':'.$api_key, null));
echo '</pre>';
@ob_end_flush();

function jsonCurlRequest($url, $apiUserPwd, $post = null)
{
	if (!is_null($post))
	{
		$header = array(
    		'Content-Type: application/json',                                                                                
    		'Content-Length: ' . strlen($post)                                                                       
		);
	}
	//print_r($post);
	$result = 'unknown Error';
	//check if you have curl loaded
	if(!function_exists("curl_init")) die("cURL extension is not installed");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERPWD, $apiUserPwd);
	if (!is_null($post))
	{
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if (!is_null($post))
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$start = array_sum(explode(' ', microtime()));
	$result = curl_exec($ch);
	$stop = array_sum(explode(' ', microtime()));
	$totalTime = $stop - $start;
	/**     * Check for errors    */
	if ( curl_errno($ch) )
	{
		$result = 'cURL ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch);
	}
	else
	{
		//PRINT_R(curl_getinfo($ch));
		$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		switch($returnCode)
		{
			case 404:	$result = 'ERROR -> 404 Not Found';
			break;
			case 200:
				break;
			default:	$result = 'HTTP ERROR -> ' . $returnCode;
			break;
		}
	}
	if( curl_errno( $ch ) == CURLE_OK )	// Check whether the curl_exec worked.
	{
		$content = $result;	//exit;
	}
	curl_close( $ch );	// Clean up CURL, and return any error.
	if (!isset($content)) return $result;
	$response = json_decode($content,true);
	if (is_null($response))
	{
		return $content;
	}
	return $response;
}
