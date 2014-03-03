"""

Author: Jay Engineer
Home Page: https://github.com/jay754/Facebook-Graph-Info
Script: fbwrapper.py
Python/PHP Facebook-Graph API
PHP/Python Wrapper for Facebook-open-graph (no OAuth) 

license: The BSD 3-Clause License

"""

import urllib2
import urllib
import simplejson as json
import json
import requests
import sys
import os

#sys.setdefaultencoding("utf-8")

class DownloadError(Exception): pass #download error

class fbwrapper:

    def __init__(self, url, token = None):
        self.url = url
        self.token = token

    def __getURL(self):
        """returns Url"""

        return self.url

    def __getToken(self):
        """returns token"""

		return self.token

    def _HTTPStatus(self, url):
        """get the http status code of a site"""	

        r = requests.get(url)
        http_status = r.status_code

        return http_status

    def fbInfo(self, username):
		"""Gets the basic info for the person
		   Returns a Dictionary of basic info of username"""

        url = self.url
        results = urllib2.urlopen(url+username)
        json_decoded = json.load(results)

        return json_decoded

    def fbID(self, username):
		"""Gets the fb id of the person"""

        results = self.fbInfo(username)
        Id = str(results["id"])

        return Id
	
    def getName(self, username):
		"""Gets the First and Last Name of the person
		   Returns a Tuple of lastname first and than firstname"""		

        results = self.fbInfo(username)
        Data = (str(results["last_name"]), str(results["first_name"]))

        return Data

    def getUsername(self, username):
        """Gets the fb username of the person"""

        results = self.fbInfo(username)
        Data = str(results["username"])

        return Data

    def getLink(self, username):
        """Gets the fb link of the person"""

        results = self.fbInfo(username)
        Data = str(results["link"])

        return Data

    def getGender(self, username):
        """Gets the fb gender of the person"""

        results = self.fbInfo(username)
        Data = str(results["gender"])

        return Data

	def getPic(self, username):
        """Gets the picture of the person and saved on your computer

           -Saves the your facebook picture in the current directory and saved as jpg
           -Just put the username of the person's picture you want 
           -Does not tell you if there's a duplicate saved"""

        url = self.url
        #results = urllib2.urlopen(url+username+"/picture").read()
        cwd = os.getcwd()		

        try:
            urllib.urlretrieve(url+username+"/picture", username+".jpg")
            return "success, Your file is saved at " + cwd 
        except:
            raise DownloadError

    def getPageInfo(self, id):
        """gets the basic info of a facebook page
           Returns a dictionary of info"""

        url = self.url
        results = urllib2.urlopen(url+id)
        json_decoded = json.load(results)
        data = dict()		
		
        data = {"Name" : str(json_decoded["name"]),
                "Id" : str(json_decoded["id"]),
                "Likes" : str(json_decoded["likes"]),
                "Website" : str(json_decoded["website"]),
                "People Talking about" : str(json_decoded["talking_about_count"]),
                "About" : str(json_decoded["about"])}

        return data

    def appInfo(self, id):
        """gets the basic info of a facebook app"""

        url = self.url
        results = urllib2.urlopen(url+id)
        json_decoded = json.load(results)
        data = dict()

        data = {"Name" : str(json_decoded["name"]),
                "weekly active users" : str(json_decoded["weekly_active_users"]),
                "monthly active users" : str(json_decoded["monthly_active_users"]),
                "daily active users rank" : str(json_decoded["daily_active_users_rank"])}

        return data

    def getLikes(self, username):
        """gets like of the original person"""

        url = self.url
        token = self.token
        http_status = self._HTTPStatus(url+username+"/likes?access_token="+token)		

        if http_status == 200:
            results = urllib2.urlopen(url+username+"/likes?access_token="+token)
            json_decoded = json.load(results)
            data = json.dumps([i for i in json_decoded["data"]])				
            categories = json.dumps([i["category"] for i in json_decoded["data"]])
            names = json.dumps([i["name"] for i in json_decoded["data"]])
            ids = json.dumps([i["id"] for i in json_decoded["data"]])

            data = {"Ids" : ids,
                    "categories" : categories,
                    "Names" : names}

            return data

        else:
            return "bad request"

    def getFriends(self, username):
        """gets the friends of the original person"""

        url = self.url
        token = self.token
        http_status = self._HTTPStatus(url+username+"/friends?access_token="+token)

        if http_status == 200:
            results = urllib2.urlopen(url+username+"/friends?access_token="+token).read()
            json_decoded = json.loads(results)
            names = json.dumps([i["name"] for i in json_decoded["data"]])
            ids = json.dumps([i["id"] for i in json_decoded["data"]])

            data = {"Ids" : ids,
                    "Names" : names}

            return data

        else:
            return "bad request"

    def getGroups(self, username):
        """gets the groups that the person is in"""

        url = self.url
        token = self.token
        http_status = self._HTTPStatus(url+username+"/groups?access_token="+token)

        if http_status == 200:
            results = urllib2.urlopen(url+username+"/groups?access_token="+token).read()
            json_decoded = json.loads(results)
            ids = json.dumps([i["id"] for i in json_decoded["data"]])
            names = json.dumps([i["name"] for i in json_decoded["data"]])

            data = {"Ids" : ids,
                    "Names" : names }

            return data

        else:
            return "bad request"

    def getMusic(self, username):
	    """gets the music that the person listens to"""

        url = self.url
        token = self.token
        http_status = self._HTTPStatus(url+username+"/music?access_token="+token)

        if http_status == 200:
            results = urllib2.urlopen(url+username+"/music?access_token="+token).read()
            json_decoded = json.loads(results)
            ids = json.dumps([i["id"] for i in json_decoded["data"]])
            categories = json.dumps([i["category"] for i in json_decoded["data"]])
            names = ids = json.dumps([i["name"] for i in json_decoded["data"]])

            data = {"Ids" : ids,
                    "Names" : names,
                    "category" : categories}

            return data

        else:
            return "bad request"

fbObject = fbwrapper("https://graph.facebook.com/",Token)
print fbObject.getMusic("jay.enginer")