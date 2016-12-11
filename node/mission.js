var dorita980 = require('dorita980');
var blid = process.argv[2];
var password = process.argv[3];
var ip = process.argv[4];

var myRobotViaLocal = new dorita980.Local(blid, password, ip); // robot IP address

// Pause!
myRobotViaLocal.getMission().then(function(data){
  console.log(JSON.stringify( data));
}).catch(function(err){
  console.error(JSON.stringify( err));
});
