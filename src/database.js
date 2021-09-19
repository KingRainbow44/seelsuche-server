import * as config from "resources/config.json";
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
            if(error)
                connected = false;
            else {
                connected = true;
                console.log(`Connected to MySQL database: ${config.database.database}.`)
            }
        });
    }

    checkConnection() {
        return connected && connection.state === 'disconnected';
    }

    userExists(userHash) {
        let exists = false
        connection.connect((error) => {
            if(error) {
                exists = false;
            } else {
                connection.query("SELECT * FROM `player-data` WHERE `userHash`=`" + userHash + "`;", (error, result) => {
                    if(error) {
                        exists = false;
                    } else {
                        exists = result.length > 0;
                    }
                });
            }
        })

        return exists;
    }

    insertUserData(userHash) {
        connection.connect((error) => {
            if(!error) {
                connection.query("INSERT INTO `player-data` (`userHash`, `data`) VALUES ('" + userHash + ", '{}');");
            }
        })
    }
}