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
    		Session.name = result['name'];
    		Session.userId = result.user_id;
    		console.log(result);
    		//Session.setInfo(result);
    		//console.log(Session);
    		
  			//window.location = "http://www.yoururl.com";
		})
		  
		  .fail(function() {
		    $('#error_tag').html("Login Failed");
		  });

    }
}