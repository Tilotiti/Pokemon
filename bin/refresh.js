const pogobuf = require('pogobuf');

if(!process.argv[2])
    return console.error("Argument 1 must be the Login");

if(!process.argv[3])
    return console.error("Argument 2 must be the Password");

var type = 'ptc';

if(process.argv[2].indexOf('@') != -1) {
    type = 'google';
}

var username = process.argv[2];
var password = process.argv[3];
var lat      = 48.856614;
var lng      = 2.3522219000000177;
var client = new pogobuf.Client();
var login;

if(type == "google") {
    login = new pogobuf.GoogleLogin();
} else {
    login = new pogobuf.PTCLogin();
}

var callback = {
    player: {},
    pokedex: []
};

login.login(username, password)
    .then(function(token) {
        client.setAuthInfo(type, token);
        client.setPosition(lat, lng);
        return client.init();
    })
    .then(function() {
        return client.getInventory(0);
    })
    .then(function(inventory) {
        if (!inventory.success) throw Error('success=false in inventory response');

        inventory = pogobuf.Utils.splitInventory(inventory);

        // Player informations
        var player = {
            level: inventory.player.level,
            xp: inventory.player.experience.low,
            km: Math.round(parseInt(inventory.player.km_walked)),
            discovered: inventory.player.unique_pokedex_entries,
            catched: inventory.player.pokemons_captured,
            evolved: inventory.player.evolutions
        };


        // Pokedex Informations
        var pokedex = [];
        for(var i in inventory.pokemon) {
            var pokemon = inventory.pokemon[i];

            pokedex.push({
                id: pokemon.pokemon_id,
                cp: pokemon.cp,
                pokeball: pokemon.pokeball
            });
        }

        callback.player = player;
        callback.pokedex = pokedex;
    })
    .then(function() {
        return client.getPlayer();
    })
    .then(function(response) {
        var player = {
            username: response.player_data.username,
            team: response.player_data.team
        };

        callback.player.username = player.username;
        callback.player.team = player.team;
    })
    .then(function() {
        console.log(JSON.stringify(callback));
    })
    .catch(console.error);
