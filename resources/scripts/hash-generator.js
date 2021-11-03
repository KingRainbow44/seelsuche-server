import { createHash } from "crypto";

// Insert String Here \\
let hash =
    "magix:wordpass!2:1634744178789";
// Insert String Here \\

console.log(createHash('sha256').update(hash).digest('hex'))