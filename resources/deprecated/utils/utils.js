import crypto from "crypto";
import { HashMap } from "./hashmap.js";

export function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

export function json2array(json){
    let result = [];
    let keys = Object.keys(json);
    keys.forEach(function(key){
        result.push(json[key]);
    });

    return result;
}

export function sha256(string) {
    return Buffer.from(crypto.createHash('sha256').update(string).digest('base64'), 'base64');
}

export function hashToArray(hash) {
    let string = new Buffer(hash, 'base64')
        .toString('ascii');
    return string.split(':');
}

export function clientParse(data) {
    if(typeof data != 'object') return {};
    let sendToClient = new HashMap();

    sendToClient.add('')

    return sendToClient.values();
}