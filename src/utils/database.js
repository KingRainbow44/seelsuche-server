// noinspection EqualityComparisonWithCoercionJS

import config from "../../resources/config.json";
import mysql from "mysql";

import { generateUserId } from "../utils/utils.js"

let connection; let connected;
export class Database {
    constructor() {
        connection = mysql.createConnection({
            host: config.database.address,
            port: config.database.port,

            user: config.database.username,
            password: config.database.password,

            database: config.database.database
        });

        connection.connect((error) => {
            if(error) {
                connected = false;
                console.log(`Unable to connect to ${config.database.address}:${config.database.port}.`);
                throw error;
            } else {
                connected = true;
                console.log(`Connected to MySQL database: ${config.database.database}.`);
            }
        });
    }

    checkConnection() {
        return connected && connection.state !== 'disconnected';
    }

    query(query, callback) {
        if(!this.checkConnection()) {
            callback(null);
            return;
        }

        connection.query(query, (error, result) => {
            if(error) throw error;
            callback(result);
        });
    }
}

import { database } from "../server.js";
export function insertAccount(userHash, username) {
    let data = {
        "username": username,
        "userId": generateUserId(),
        "accountCreation": Date.now().toString(),
        "lastLogin": Date.now().toString(),

        "statistics": {
            "health": 100,
            "strength": 0,
            "defense": 0,
            "critChance": 10, // TODO: https://discord.com/channels/@me/880273715642921040/900412378926182401
            "critDamage": 100,
            "stamina": 100,
            "speed": 1.0
        }
    }; // These is the default data for new users.
    data = JSON.stringify(data);

    database.query(`INSERT INTO \`player-data\` (\`userHash\`, \`data\`) VALUES ('${userHash}', '${data}');`, result => {});
    database.query(`INSERT INTO \`accounts\` (\`username\`, \`userHash\`) VALUES ('${username}', '${userHash}');`, result => {});
}

export function getAccount(userHash, callback) {
    database.query(`SELECT * FROM \`player-data\` WHERE \`userHash\`='${userHash}';`, result => {
        if(result != null && result.length > 0)
            callback(result[0]);
        else callback(null);
    });
}

export function updateAccount(userHash, updatedData) {
    database.query(`UPDATE \`player-data\` SET \`data\`='${updatedData}' WHERE \`userHash\`='${userHash}';`, result => {});
}