'use strict';

const dgram = require('dgram');
const request = require('request');

var ip = process.argv[2];
var blid = process.argv[3];

function check(rid,blid,requestOptions) {
  if (rid === 30) {
	console.log("Error");
  //console.log('Timeout getting password. Are you following the instructions? You already setup your robot? Its the robot IP correct?');
	process.exit(1);
  }

  requestOptions.body = '{"do":"get","args":["passwd"],"id":' + rid + '}';

  request(requestOptions, function (error, response, body) {
	if (error) {
	  //console.log('Fatal error connecting to robot. Please verify the IP address and connectivity:', error);
    console.log('Error');
	  process.exit(1);
	}

	if (response.statusCode === 401) {
	  setTimeout(function () { check(++rid,blid,requestOptions); }, 2000);
	} else if (response.statusCode === 200) {
	  let pass = JSON.parse(body).ok.passwd;
	  console.log('Password:' + pass);
	  //console.log(blid);
	} else {
	  console.log('Unespected response. Checking again...');
	  setTimeout(function () { check(++rid,blid,requestOptions); }, 2000);
	}
  });
}

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
