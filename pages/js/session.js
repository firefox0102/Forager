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
    		
  			window.location = "dashboard.html";
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

var scanTime = {
    startTime: 0,
    endTime: 0,
    total: 0,
    timer: 0,
    start: function(){
            scanTime.startTime = new Date();
            dataStore.set("scanTimer", scanTime);
        
        //else already started so do nothing.
    },
    stop: function(){
        scanTime.endTime = new Date();
        scanTime.total = scanTime.endTime - scanTime.startTime;
        dataStore.set("scanTimer", scanTime);
    },
    getElapsed: function(){
        console.log(dataStore.get("scanTimer"));
        var now = new Date();
        var elapsed = now - scanTime.startTime
        scanTime.total = elapsed;
        dataStore.set("scanTimer", scanTime);
        var value = dataStore.get("scanTimer");

        return value.total;
    }
}

/*var curReport = function(){
    id: 0,
    name: 0,
    creator: 0,
    dateCreated: 0,
    totalPagesScanned: 0,
    totalErrors: 0,
    totalTime: 0,
    getFromServer: function(){
        $post("get_report.php",{id: curReport.id})
        .done(function(){

        })
        .fail(function(){

        });
    }

}*/