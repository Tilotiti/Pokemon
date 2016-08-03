const pogobuf = require('pogobuf');

var lat      = 48.856614;
var lng      = 2.3522219000000177;
var client = new pogobuf.Client();
var login = new pogobuf.GoogleLogin();

login.login('tilo.thibault@gmail.com', "M9212000134")
    .then(function(token) {
        client.setAuthInfo('google', token);
        client.setPosition(lat, lng);
        return client.init();
    })
    .then(function() {
        return client.getPlayer();
    })
    .then(function(data) {
        console.log(data);
    })
    .catch(console.error);
