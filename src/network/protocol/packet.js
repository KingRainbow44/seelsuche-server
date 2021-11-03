import { json2array } from "../../utils/utils.js";

export class Packet {
    toDecode = [];
    toEncode = [];
    readNext = 0;
    writeNext = 0;

    constructor(data) {
        this.toDecode = data;
    }

    pid() {
        return "0x00";
    }

    next() {
        let read = json2array(
                json2array(this.toDecode[this.readNext]
        )[1])[1];
        this.readNext++;

        return read;
    }
}

export class InboundPacket extends Packet {
    constructor(data) {
        super(data);
    }

    decode() { }

    handle(client) { }
}

export class OutboundPacket extends Packet {
    constructor() {
        super({});
    }

    writeInt(data) {
        this.toEncode[this.writeNext] = {
            "type": "int",
            "raw": data
        }; this.writeNext++;
    }

    writeString(data) {
        this.toEncode[this.writeNext] = {
            "type": "string",
            "raw": data
        }; this.writeNext++;
    }

    writeBoolean(data) {
        this.toEncode[this.writeNext] = {
            "type": "boolean",
            "raw": data
        }; this.writeNext++;
    }

    prepare() {
        return {
            "request": "packet",
            "data": this.toEncode,
            "id": this.pid()
        };
    }

    encode() { }
}