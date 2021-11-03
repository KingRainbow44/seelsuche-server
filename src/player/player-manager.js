import { HashMap } from "../utils/hashmap.js";
import { Player } from "./player.js";

export class PlayerManager {
    players = new HashMap();
    nextId = 0;

    addPlayer(socket, address) {
        let player = new Player(socket, address, this.nextId);
        this.players.add(this.nextId, player);

        this.nextId++; return player;
    }

    getPlayer(value) {
        switch(typeof value) {
            default:
                return null;
            case "number":
                return this.players.get(value);
            case "string":
                let player = null;
                this.players.values().forEach(arrVal => {
                    if(arrVal[1].getAddress() === value)
                        player = arrVal[1];
                });
                return player;
        }
    }

    getPlayerById(userId) {
        let player = null;
        this.players.values().forEach(arrVal => {
            if(arrVal[1].getUserId() === userId)
                player = arrVal[1];
        });
        return player;
    }

    removePlayer(id) {
        this.players.remove(id);
    }
}