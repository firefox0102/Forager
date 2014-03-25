var Session = {
    userId: 1,
    userName: "apfundst",
    name: "Drew Pfundstein",
    setInfo: function (result) {
    	this.name = result.name;
    	this.id = result.id;
    	console.log(result);
        
    },
    login: function(usn, pass){
    	
    	$.post("login_request.php",{un: usn, password: pass})

    	.done(function(result) {
    		Session.userName = usn;
    		console.log(result);
    		
  			//window.location = "http://www.yoururl.com";
		})
		  
		  .fail(function() {
		    $('#error_tag').html("Login Failed");
		  });

    }
}