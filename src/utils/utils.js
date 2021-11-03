import { createHash } from "crypto";

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
    return createHash('sha256').update(string).digest('hex');
}

export function base64(string) {
    return Buffer.from(string).toString('base64');
}

export function fromBase64(base64) {
    return Buffer.from(base64, 'base64').toString('ascii');
}

export function generateUserId(userId) {

}