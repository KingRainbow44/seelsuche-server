// noinspection EqualityComparisonWithCoercionJS

import config from "../resources/config.json";
import mysql from "mysql";

let connection = null;
let connected = false;
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
                console.log(`Unable to connect to ${config.database.address}:${config.database.port}.`)
            } else {
                connected = true;
                console.log(`Connected to MySQL database: ${config.database.database}.`)

                connection.query("CREATE TABLE IF NOT EXISTS `player-data` ( `userHash` TEXT NOT NULL , `data` LONGTEXT NOT NULL )");
            }
        });
    }

    checkConnection() {
        return connected && connection.state !== 'disconnected';
    }

    userExists(userHash, callback) {
        if(!this.checkConnection()) return false;

        connection.query("SELECT * FROM `player-data` WHERE `userHash`='" + userHash + "';", (error, result) => {
            if(error) throw error;

            let exists = result.length != 0;
            callback(exists);
        });
    }

    insertUserData(userHash) {
        if(!this.checkConnection()) return;
        connection.query("INSERT INTO `player-data` (`userHash`, `data`) VALUES ('" + userHash + "', '{}');", (error) => {
            if(error) throw error;
        });
    }
}