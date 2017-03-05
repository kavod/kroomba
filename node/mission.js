var counter = 2;

var dorita980 = require('dorita980');
var blid = process.argv[counter];
var password = process.argv[counter+1];
var ip = process.argv[counter+2];
var myRobotViaLocal = new dorita980.Local(blid, password, ip); // robot IP address
// Mission!
myRobotViaLocal.getMission().then(function(data){
  console.log(JSON.stringify( data));
  myRobotViaLocal.end();
},function(reason) {
  console.log(reason);
}).catch(function(err){
  console.error(JSON.stringify( err));
});
