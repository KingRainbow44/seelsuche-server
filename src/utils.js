import crypto from "crypto";

export function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

export async function sha256(string) {
    let hash = crypto.createHash('sha256'); let hashed = string;
    hash.update(hashed);
    hash.digest(hashed);
    return hashed;
}