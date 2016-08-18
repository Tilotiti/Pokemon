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
var lat      = parseFloat(process.argv[4]) || 48.856614;
var lng      = parseFloat(process.argv[5]) || 2.3522219000000177;

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
            evolved: inventory.player.evolutions,
            prevLevel: inventory.player.prev_level_xp.toString(),
            nextLevel: inventory.player.next_level_xp.toString()
        };

        // Pokedex Informations
        var pokedex = [];
        for(var i in inventory.pokemon) {
            var pokemon = inventory.pokemon[i];
            
            var iv = pogobuf.Utils.getIVsFromPokemon(pokemon);

            pokedex.push({
                id: pokemon.pokemon_id,
                cp: pokemon.cp,
                pokeball: pokemon.pokeball,
                attack: iv.att,
                defense: iv.def,
                stamina: iv.stam,
                iv: iv.percent
            });
        }

        callback.player = player;
        callback.pokedex = pokedex;
    })
    .then(function() {
        return client.getPlayer();
    })
    .then(function(player) {
        callback.player.username = player.player_data.username;
        callback.player.team = player.player_data.team;
        callback.player.sign = player.player_data.creation_timestamp_ms.toString().substring(0, 10);
    })
    .then(function() {
        console.log(JSON.stringify(callback));
    })
    .catch(console.error);
