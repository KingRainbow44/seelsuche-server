import crypto from "crypto";

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