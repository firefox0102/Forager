var Session = {
    userId: 1,
    userName: "apfundst",
    name: "Drew Pfundstein",
    getInfo: function () {
        
    },
    login: function(un, pass){
    	$.post("login_request.php",{un: un, password: pass},function(result) {
    		this.name = result.name;
    		this.id = result.id;
    		this.userName = un;
  			window.location = "http://www.yoururl.com";
		})
		  
		  .fail(function() {
		    $('#error_tag').html("Login Failed");
		  });

    }
}