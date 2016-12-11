'use strict';

const dgram = require('dgram');
const request = require('request');

function discovery (cb) {
  const server = dgram.createSocket('udp4');

  server.on('error', (err) => {
    server.close();
    cb(err);
  });

  server.on('message', (msg) => {
    try {
      let parsedMsg = JSON.parse(msg);
      if (parsedMsg.hostname && parsedMsg.ip && parsedMsg.hostname.split('-')[0] === 'Roomba') {
        server.close();
        //console.log('Robot found! with blid ' + parsedMsg.hostname.split('-')[1]);
        cb(null, parsedMsg.ip,parsedMsg.hostname.split('-')[1]);
      }
    } catch (e) {}
  });

  server.on('listening', () => {
    //console.log('Looking for robots...');
  });

  server.bind(5678, function () {
    const message = new Buffer('irobotmcs');
    server.setBroadcast(true);
    server.send(message, 0, message.length, 5678, '255.255.255.255');
  });
}

function check(rid,blid,requestOptions) {
  if (rid === 120) {
	console.log('Timeout getting password. Are you following the instructions? You already setup your robot? Its the robot IP correct?');
	process.exit(1);
  }

  requestOptions.body = '{"do":"get","args":["passwd"],"id":' + rid + '}';

  request(requestOptions, function (error, response, body) {
	if (error) {
	  console.log('Fatal error connecting to robot. Please verify the IP address and connectivity:', error);
	  process.exit(1);
	}

	if (response.statusCode === 401) {
	  setTimeout(function () { check(++rid,blid,requestOptions); }, 2000);
	} else if (response.statusCode === 200) {
	  let pass = JSON.parse(body).ok.passwd;
	  console.log('Password: ' + pass);
	  //console.log(blid);
	} else {
	  console.log('Unespected response. Checking again...');
	  setTimeout(function () { check(++rid,blid,requestOptions); }, 2000);
	}
  });
}

discovery(function(ierr,ip,blid) {
	console.log('IP:' + ip);
	console.log('blid:' + blid);
	//console.log('Make sure your robot is on the Home Base and powered on (green lights on). Then press and hold the HOME button on your robot until it plays a series of tones (about 2 seconds). Release the button and your robot will flash WIFI light. Then wait and look here...');

	var requestOptions = {
	  'method': 'POST',
	  'uri': 'https://' + ip + ':443/umi',
	  'strictSSL': false,
	  'headers': {
		'Content-Type': 'application/json',
		'Connection': 'close',
		'User-Agent': 'aspen%20production/2618 CFNetwork/758.3.15 Darwin/15.4.0',
		'Content-Encoding': 'identity',
		'Accept': '*/*',
		'Accept-Language': 'en-us',
		'Host': ip
	  }
	};
	check(1,blid,requestOptions);
});

//module.exports = discovery;


/*var dorita980 = require('dorita980');

dorita980.getRobotIP(function (ierr, ip) {
  if (!ierr) {
	console.log(ip);
    var myRobotViaLocal = new dorita980.Local('MyUsernameBlid', 'MyPassword', ip);

    myRobotViaLocal.getMission().then((response) => {
      console.log(response);
    }).catch((err) => {
      console.log(err);
    });
  } else {
    console.log('error looking for robot IP');
  }
});*/
