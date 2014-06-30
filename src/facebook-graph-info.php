<?php

/*

	Author: Jay Engineer
	Home Page: https://github.com/jay754/Facebook-Graph-Info
	Script: facebook-graph-info.php
	PHP Facebook-Graph API

	license: The BSD 3-Clause License
*/

class FacebookGraph {
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

    public static function HTTPstatus($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3); //returns http status
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
                      "last_name" => $info['last_name']);
        
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

            $data = array("Name" => $json["name"],
                      "Id" => $json["id"],
                      "likes" => $json["likes"],
                      "website" => $json["website"],
                      "People Talking about" => $json["talking_about_count"],
                      "City" => $json["location"]["city"]);

            return $data;
		}
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
		
        if ($http_status == 200){
            $info = file_get_contents($url."/".$id."/likes?access_token=".$token);
            $json = json_decode($info, true);
            $data = array();

            for($i=0;$i<sizeof($json['data']);$i++){
                $data[$i]['category'] = $json['data'][$i]['category'];
                $data[$i]['name'] = $json['data'][$i]['name'];
                $data[$i]['id'] = $json['data'][$i]['id'];
                $data[$i]['created_time'] = $json['data'][$i]['created_time'];
            }

            return $data;
        }
		
		else {
            return "bad request";
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

        if ($http_status == 200){
            $info = file_get_contents($url."/".$id."/friends?access_token=".$token);
            $json = json_decode($info, true);
            $data = array();

            for($i=0;$i<sizeof($json['data']);$i++){
                $data[$i]['name'] = $json['data'][$i]['name'];
                $data[$i]['id'] = $json['data'][$i]['id'];
            }

            return $data;
        }
        else {
            return "bad request";
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

        if ($http_status == 200){
            $info = file_get_contents($url."/".$id."/groups?access_token=".$token);
            $json = json_decode($info, true);
            $data = array();

            for($i=0;$i<sizeof($json['data']);$i++){
                $data[$i]['name'] = $json['data'][$i]['name'];
                $data[$i]['id'] = $json['data'][$i]['id'];
                $data[$i]['version'] = $json['data'][$i]['version'];
                $data[$i]['unread_messages'] = $json['data'][$i]['unread'];
                $data[$i]['admin'] = $json['data'][$i]['administrator'];
            }
			
            return $data;
        }
        else {
            return "bad request";
        }
    }
	
    public function getMusic($id){
        $url = $this->_url;
        $token = $this->_token;
        $http_status = $this-> HTTPstatus($url."/".$id."/music?access_token=".$token); //http status 

        if ($http_status == 200){
            $info = file_get_contents($url."/".$id."/music?access_token=".$token);
            $json = json_decode($info, true);
            $data = array();

            for($i=0;$i<sizeof($json['data']);$i++){
                $data[$i]['name'] = $json['data'][$i]['name'];
                $data[$i]['id'] = $json['data'][$i]['id'];
                $data[$i]['category'] = $json['data'][$i]['category'];
                $data[$i]['created_time'] = $json['data'][$i]['created_time'];
            }

            return $data; //get results
        }
        else {
            return "bad request";
        }
    }
} //classend

$facebookObj = new FacebookGraph('https://graph.facebook.com','AAAAAAITEghMBANUwJj2k1T5X48lGNZAm2vEb9aEW6CYsWV2koEZAruE8i2uBhTQ2JjZA1vdYbxUgve6uRKIUac21jgXhZBxDBNEasDtOVlEYBG3tNi5V'); //The URL
$info = $facebookObj -> appInfo("2439131959");

print_r($info);
?>