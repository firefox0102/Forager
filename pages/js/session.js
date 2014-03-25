var Session = {
    userId: 1,
    userName: "bob",
    name: "bob",
    setInfo: function (result) {//takes a json object, intended to come from server
    	this.name = result.name;
    	this.id = result.id;
    	//console.log(result);
        
    },
    login: function(usn, pass){
    	console.log(usn);
    	console.log(pass);
    	$.post("login_request.php",{un: usn, password: pass})

    	.done(function(result) {
    		Session.userName = usn;
    		var data = JSON.parse(result);
    		Session.name = data['name'];
    		Session.userId = data.user_id;
    		console.log(data);
    		dataStore.set("user", Session);
    		//Session.setInfo(result);
    		//console.log(Session);
    		
  			//window.location = "http://www.yoururl.com";
		})
		  
		  .fail(function() {
		    $('#error_tag').html("Login Failed");
		  });

    }
}

var dataStore = {
  set: function(key, value) {
    if (!key || !value) {return;}

    if (typeof value === "object") {
      value = JSON.stringify(value);
    }
    localStorage.setItem(key, value);
  },
  get: function(key) {
    var value = localStorage.getItem(key);

    if (!value) {return;}

    // assume it is an object that has been stringified
    if (value[0] === "{") {
      value = JSON.parse(value);
    }

    return value;
  }
}