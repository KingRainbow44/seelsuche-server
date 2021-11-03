// noinspection EqualityComparisonWithCoercionJS

/*
 * Imports
 */
// Configuration Files
import config from "../resources/config.json";

// Classes
import { MultiplayerServer } from "./multiplayer/server.js";
import { DataServer } from "./data/server.js";
import { Database } from "./utils/database.js";

// Managers
import { EntityManager } from "./multiplayer/entity/entity-manager.js";
import { PlayerManager } from "./multiplayer/player/player-manager.js";

log("Starting Seelsuche server...");

/*
 * Database
 */
export const database = new Database();

/*
 * Multiplayer Server
 */
export const multiplayer = new MultiplayerServer(config.multiplayer.port);
multiplayer.listen();

/*
 * Data Server
 */
export const data = new DataServer(config.data.port);
data.listen();

/*
 * Run after all processes have fired.
 */
setTimeout(() => {
    // Empty callback.
    database.query("CREATE TABLE IF NOT EXISTS `player-data` ( `userHash` TEXT NOT NULL , `data` LONGTEXT NOT NULL )", () => {});
    // Empty callback.
    database.query("CREATE TABLE IF NOT EXISTS `accounts` ( `username` TEXT NOT NULL , `userHash` TEXT NOT NULL )", () => {});
}, 5000);

/*
 * Create managers.
 */
export const entityManager = new EntityManager();
export const playerManager = new PlayerManager();

/**
 * A faster way of logging data.
 * @param msg
 */
function log(msg) {
    console.log(msg);
}