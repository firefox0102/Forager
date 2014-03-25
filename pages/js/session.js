var Session = {
    userId: 1,
    userName: "apfundst",
    name: "Drew Pfundstein",
    getInfo: function () {
        
    },
    login: function(usn, pass){
    	
    	$.post("login_request.php",{un: usn, password: pass})

    	.done(function(result) {
    		this.name = result.name;
    		this.id = result.id;
    		console.log(result);
    		this.userName = un;
    		console.log(this);
  			//window.location = "http://www.yoururl.com";
		})
		  
		  .fail(function() {
		    $('#error_tag').html("Login Failed");
		  });

    }
}