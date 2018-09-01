<?php
	class MF2PayClient {
		var apiURL = "http://mf2pay.in/api/payment";
		
		function isJSON($string) {
			return ((is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
		}
		
		function cURL($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			if(isset($_SERVER['HTTP_USER_AGENT'])) {
				curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$result = curl_exec($ch);
			curl_close($ch);
			if ($result){
				return $result;
			} else {
				return '';
			}
		}
		
		function create($data, $private) {
			$api = $this->apiURL;
			$data['Seed'] = hash('sha256', $data['PaymentID'].hash('sha256', $private));
			$json = $this->cURL($api."?method=create&".http_build_query($data));
			if($this->isJSON($json)) {
				$result = json_decode($json, true);
				if($result['success'] == true) {
					return $result['data'];
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
		function check($id) {
			$api = $this->apiURL;
			$json = $this->cURL($api."?method=check&id=".$id);
			if($this->isJSON($json)) {
				$result = json_decode($json, true);
				if($result['success'] == true) {
					if($result['data']['status'] == "done") {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
	