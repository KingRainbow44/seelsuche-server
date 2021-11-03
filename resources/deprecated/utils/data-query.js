// noinspection EqualityComparisonWithCoercionJS

import { Database } from "./database.js";

export function userExists(database, username, callback) {
    if(!database instanceof Database)
        return;
    if(!database.checkConnection())
        return;

    database.query("SELECT * FROM `accounts` WHERE `username`='" + username + "';", result => {
        let exists = result.length != 0;
        callback(exists);
    });
}

export function insertUserData(database, userHash) {
    if(!database instanceof Database)
        return;
    if(!database.checkConnection())
        return;

    database.query("INSERT INTO `player-data` (`userHash`, `data`) VALUES ('" + userHash + "', '{}');", () => {});
}

export function getUserData(database, userHash, callback) {
    if(!database instanceof Database)
        return;
    if(!database.checkConnection())
        return;

    database.query("SELECT * FROM `player-data` WHERE `userHash`='" + userHash + "';", (result) => {
        callback(result[0]);
    });
}