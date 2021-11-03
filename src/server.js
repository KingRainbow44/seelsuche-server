// noinspection EqualityComparisonWithCoercionJS

/*
 * Imports
 */
// Configuration Files
import config from "../resources/config.json";

// Classes
import { Database } from "./utils/database.js";
import { Socket } from "./utils/socket.js";

// Managers
import { PacketManager } from "./network/packet-manager.js";
import { CommandManager } from "./commands/command-manager.js";

// Modules
import http from "http";
import {PlayerManager} from "./player/player-manager.js";

log("Starting Seelsuche server...");

/*
 * Database
 */
export const database = new Database();

/*
 * Multiplayer Socket
 */
export const multiplayer = new Socket(http.createServer().listen(8443));
multiplayer.listen();

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
export const packetManager = new PacketManager();
export const commandManager = new CommandManager();
export const playerManager = new PlayerManager();

/*
 * Start reading console commands.
 */
let stdin = process.openStdin();
stdin.addListener('data', data => {
    commandManager.handle(data.toString());
});

/**
 * A faster way of logging data.
 * @param msg
 */
function log(msg) {
    console.log(msg);
}