// noinspection EqualityComparisonWithCoercionJS

import config from "../../resources/config.json";
import mysql from "mysql";

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
            callback(result);
        });
    }
}