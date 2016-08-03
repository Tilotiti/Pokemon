const pogobuf = require('pogobuf');

var lat      = 48.856614;
var lng      = 2.3522219000000177;
var client = new pogobuf.Client();
var login = new pogobuf.GoogleLogin();

var callback = {
    player: {},
    pokedex: []
};

login.login('tilo.thibault@gmail.com', "M9212000134")
    .then(function(token) {
        client.setAuthInfo('google', token);
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
            prev_xp: inventory.player.prev_level_xp.toString(),
            next_xp: inventory.player.next_level_xp.toString()
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
    .then(function(player) {
        callback.player.username = player.player_data.username;
        callback.player.team = player.player_data.team;
        callback.player.sign = player.player_data.creation_timestamp_ms.toString().substring(0, 10);
    })
    .then(function() {
        console.log(JSON.stringify(callback));
    })
    .catch(console.error);
