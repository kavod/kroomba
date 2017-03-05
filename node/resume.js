var dorita980 = require('dorita980');
var blid = process.argv[2];
var password = process.argv[3];
var ip = process.argv[4];

var myRobotViaLocal = new dorita980.Local(blid, password, ip); // robot IP address

// Resume!
myRobotViaLocal.resume().then(function(data){
  console.log(data);
  myRobotViaLocal.end();
}).catch(function(err){
  console.error(err);
});
