/*
 * Imports
 */
import { database } from "../../server.js";
import { HashMap } from "../../utils/hashmap.js";
import * as dbu from "../../utils/data-query.js";
import * as utils from "../../utils/utils.js";
import * as constants from "../../utils/constants.js";
import { Entity, EntityData } from "../entity/entity.js";

/**
 * The server-side player for Seelsuche.
 * Handles movement, actions, and others.
 */
export class Player extends Entity{
    remote = null; id = 0;
    address = null;

    playerData = new PlayerData();

    /*
     * Data variables.
     */
    coordinates = [0, 0, 0];

    constructor(ws, address, id) {
        super(id);

        this.remote = ws;
        this.address = address;
        this.id = id;
    }

    /*
     * @return void
     * methods. Return nothing but modify data.
     */

    login(userHash) {
        // Set user hash. Does nothing yet.
        this.playerData.userHash = userHash;

        // Attempt to fetch ALL user data from database.
        dbu.getUserData(database, userHash, (data) => {
            this.playerData.import(data.data);
        });
    }

    switchScene(sceneId, loadingScreen) {
        let ws = this.remote;

        if(loadingScreen) {
            ws.send(constants.DISPLAY_SCREEN + constants.LOAD_NORMAL);
        }
        ws.send(constants.SWITCH_SCENE + sceneId);
    }

    /*
     * @return mixed
     * methods. Return data stored in variables.
     */

    getId() {
        return this.id;
    }

    getAddress() {
        return this.address;
    }

    getRemote() {
        return this.remote;
    }

    getPlayerData() {
        return this.playerData;
    }
}

export class PlayerData extends EntityData {
    userHash = null; otherStats = [];

    // User statistics.
    health = 1000;
    defense = 0;

    export() {
        let map = new HashMap();

        // User statistics.
        map.add("health", this.health);
        map.add("defense", this.defense);

        return new Buffer(JSON.stringify(map.values)).toString('base64url');
    }

    import(data) {
        data = new Buffer(data).toString('utf-8');
        if(!utils.isJson(data)) return;
        let map = new HashMap(JSON.parse(data));

        map.forEach((key, value) => {
            Reflect.set(this, key, value);
        });
    }
}