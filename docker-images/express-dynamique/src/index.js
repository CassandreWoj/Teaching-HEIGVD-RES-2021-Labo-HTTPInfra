var Chance = require('chance');
var chance = new Chance();

var express = require('express');
var app = express();

app.get('/', function(req, res){
	res.send(generateAddresses());
});

app.listen(3000, function() {
	console.log('Accepting HTTP resquests on port 3000.');
});

function generateAddresses(){
	var noAd = chance.integer({
		min: 3,
		max: 15
	});

	var addr = [];
	for(var i = 0; i < noAd; ++i){
		var street = chance.address();
		var city = chance.city();
		var country = chance.country({full : true});

		addr.push({
			street: street,
			city: city,
			country: country
		});
	};
	console.log(addr);
	return addr;
}
