const pogobuf = require('pogobuf');

console.log(process.argv);

var username = process.argv[2];
var password = process.argv[3];
var provider = 'google';
var lat      = 48.856614;
var lng      = 2.3522219000000177;

var login = new pogobuf.GoogleLogin();
var client = new pogobuf.Client();

login.login(username, password)
    .then(token => {
        client.setAuthInfo('google', token);
        client.setPosition(lat, lng);
        return client.init();
    })
    .then(() => {
        return client.getInventory(0);
    })
    .then(inventory => {
        if (!inventory.success) throw Error('success=false in inventory response');

        inventory = pogobuf.Utils.splitInventory(inventory);
        
        // TODO : Save information in DB
        console.log('Full inventory:', inventory);
    })
    .catch(console.error);