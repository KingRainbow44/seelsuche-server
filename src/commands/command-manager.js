import {PushChatCommand} from "./types/Debugging.js";

export class CommandManager {
    commands = {};

    constructor() {
        this.registerCommands();
    }

    registerCommands() {
        this.registerCommand(new PushChatCommand());
    }

    registerCommand(command) {
        this.commands[command.getLabel()] = command;
    }

    getCommand(label) {
        return this.commands[label] ?? null;
    }

    handle(command) {
        command = command.trim();
        if(command === "") return;

        let parts = command.split(" "); let args = [];
        if(parts.length >= 1) {
            parts.forEach(value => {
                if(!value.match(parts[0]))
                    args.push(value);
            });
        }

        let cmd = this.getCommand(parts[0].toLowerCase());
        if(cmd == null)
            console.log("Unknown command, try '/help' for a list of commands.");
        else cmd.execute(args);
    }
}