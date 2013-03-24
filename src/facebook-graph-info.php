<?php

/** 

Copyright (c) 2012, Jay Engineer
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, 
are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions 
and the following disclaimer. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the documentation and/or other
materials provided with the distribution. THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.

Email: jayengineer6@gmail.com

**/

class FacebookGraph

{
	// All the info you can get from facebook without oAuth
	
	private $_url; //facebook api url
	private $_token; //access token
	public $http_status; // http status 
	
	public function __construct($url, $token = NULL){
		$this -> _url = $url; //url
		$this -> _token = $token; //access token
	}

	/**
		HTTPstatus Method
		
		@paras - none
		- No Access Token Required 
		Returns the http status of the website
		//Make sure to set the CURLOPT_SSL_VERIFYPEER and CURLOPT_SSL_VERIFYHOST to false has it messes with the SSL
	**/
	
	protected function HTTPstatus($url){
		$http = curl_init($url);
		curl_setopt($http, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($http, CURLOPT_SSL_VERIFYHOST, FALSE);
		$result = curl_exec($http);
		$http_status = $this-> http_status;
		$http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
		curl_close($http);
	
		return $http_status;
	}
	
	/**
		getURL Method
		
		@paras - none
		- No Access Token Required 
		Returns the facebook graph url of the API
	**/
	
	public static function getURL(){
		$url = $this -> _url;
		
		return $url;
	}
	
	/** 
		fbInfo Method
		
		- No Access Token Required 
		@paras - $id must be a string error otherwise Raise Error
	**/
	
	public function fbInfo($id){
		$url = $this-> _url;
		$info = file_get_contents($url.'/'.$id);
		$data = json_decode($info, true);

		return $data;
	}
	
	/** 
		fbID Method
		
		- No Access Token Required 
		@paras - $fb_username must be a string error otherwise Raise Error
	**/

	public function fbID($fb_username){
		$info = $this -> fbInfo($fb_username);
		$id = $info['id'];

		return $id;
	}

	/** 
		getName Method
		
		- No Access Token Required 
		@paras - $fb_username must be a string error otherwise Raise Error
	**/
	
	public function getName($fb_username){
		$info = $this -> fbInfo($fb_username);
		$data = array("first_name" => $info['first_name'],
					  "last_name" => $info['last_name'],
					  );
		
		return $data;
	}
	
	/** 
		fbUsername Method
		
		- No Access Token Required 
		@paras - $fb_username must be a string error otherwise Raise Error
	**/
	
	public function fbUsername($username){
		$info = $this -> fbInfo($username);
		$username = $info["username"];
		
		return $username;
	}
	
	/** 
		getLink Method
		
		- No Access Token Required 
		@paras - $fb_username must be a string error otherwise Raise Error
	**/
	
	public function getLink($username){
		$info = $this-> fbInfo($username);
		$link = $info["link"];
		
		return $link;
	}
	
	/** 
		getGender Method
		
		- No Access Token Required 
		@paras - $fb_username must be a string error otherwise Raise Error
	**/
	
	public function getGender($username){
		$info = $this-> fbInfo($username);
		$gender = $info["gender"];

		return $gender;
	}
	
//basic info out of the way	
	
	/** 
		getPicture Method
		
		@paras - $id or $groupid of the person, must be a string error otherwise Raise Error
		- No Access Token Required
		returns the photo of the inteded person or group
	**/
	
	public function getPic($id){
		$url = $this-> _url;
		header('Content-Type: image/x-png');
		$photo = file_get_contents('http://graph.facebook.com/'.$id.'/picture');

		return $photo;
	}
	
	/** 
		getPageInfo Method
		
		@paras - $Id must be a string error otherwise Raise Error
		- No Access Token Required 
	**/

	public function getPageInfo($Id){
		$url = $this-> _url;
		$info = file_get_contents($url.'/'.$Id);
		$json = json_decode($info, true);
		
		try
		{
			if (empty($json["location"])){
				throw new Exception('City is not Listed.');
			}
		}
		catch (Exception $e)
		{
		  print $e->getMessage();
		}
		
		$data = array("Name" => $json["name"],
					  "Id" => $json["id"],
					  "likes" => $json["likes"],
					  "website" => $json["website"],
					  "People Talking about" => $json["talking_about_count"],
					  "City" => $json["location"]["city"]);

		return $data;
	}
	
	/** 
		appInfo Method
		
		@paras - $appId must be a string error otherwise Raise Error
		- No Access Token Required 
	**/
	
	public function appInfo($appId){
		$url = $this-> _url;
		$info = file_get_contents($url.'/'.$appId);
		$json = json_decode($info, true);
		$data = array("name" => $json["name"],
					  "weekly active users" => $json["weekly_active_users"],
					  "monthly active users" => $json["monthly_active_users"],
					  "daily active users_rank" => $json["daily_active_users_rank"],
					  "monthly active users_rank" => $json["monthly_active_users_rank"]);
		
		return $data;
	}
	
	/** 
		getLikes Method
		
		@paras - $Id must be a string error otherwise Raise Error
		Access Token Required
		returns the likes for the individual
	**/
	
	public function getLikes($id){
	
		$url = $this-> _url;
		$token = $this-> _token;
		$http_status = $this-> HTTPstatus($url."/".$id."/likes?access_token=".$token); //http status 
		
		if ($http_status == 400){
			return "bad request";
		}
		else {
			$info = file_get_contents($url."/".$id."/likes?access_token=".$token);
			$json = json_decode($info, true);
			$data = array();
			
			foreach($json_decode as $k){
				$data[] = $k;
			}
			
			return $data;
		}
	}
	
	/** 
		getFriends Method
		
		@paras - $Id or $name of the person must be a string error otherwise Raise Error
		Access Token Required
		returns the friends for the individual
	**/
	
	public function getFriends($id){
		$url = $this-> _url;
		$token = $this-> _token;
		$http_status = $this-> HTTPstatus($url."/".$id."/friends?access_token=".$token); //http status 
		
		if ($http_status == 400){
			return "bad request";
		}
		else {
			$info = file_get_contents($url."/".$id."/friends?access_token=".$token);
			$json = json_decode($info, true);
			$data = array();
			
			foreach($json_decode as $k){
				$data[] = $k;
			}
			
			return $data;
		}
	}
	
	/** 
		getGroups Method
		
		@paras - $Id must be a string error otherwise Raise Error
		Access Token Required
		returns the groups that the individual is in
	**/
	
	public function getGroups($id){
		
		$url = $this-> _url;
		$token = $this-> _token;
		$http_status = $this-> HTTPstatus($url."/".$id."/groups?access_token=".$token); //http status 
		
		if ($http_status == 400){
			return "bad request";
		}
		else {
			$info = file_get_contents($url."/".$id."/groups?access_token=".$token);
			$json = json_decode($info, true);
			$data = array();
			
			foreach($json_decode as $k){
				$data[] = $k;
			}
			
			return $data;
		}
	}
	
	public function getMusic($id){
		$url = $this->_url;
		$token = $this->_token;
		$http_status = $this-> HTTPstatus($url."/".$id."/music?access_token=".$token); //http status 
		
		if ($http_status == 400){
			print "bad request";
		}
		
		else {
			$info = file_get_contents($url."/".$id."/music?access_token=".$token);
			$json_decode = json_decode($info, true);
			$data = array();
			
			foreach($json_decode as $k){
				$data[] = $k;
			}
			
			return $data; //get results
		}
	}
	
} //classend

$facebookObj = new FacebookGraph('https://graph.facebook.com','AAAAAAITEghMBANUwJj2k1T5X48lGNZAm2vEb9aEW6CYsWV2koEZAruE8i2uBhTQ2JjZA1vdYbxUgve6uRKIUac21jgXhZBxDBNEasDtOVlEYBG3tNi5V'); //The URL
$info = $facebookObj -> appInfo("2439131959");

print_r($info);
?>