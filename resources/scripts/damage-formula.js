// noinspection EqualityComparisonWithCoercionJS

/*
 * This is the testing zone for creating a damage formula.
 * Change the numbers in the variables to create a damage formula.
 */

let baseWeaponDamage = 100;
let baseWeaponStrength = 15;

let strength = 10;
let criticalChance = 30;
let criticalDamageMultiplier = 1.001;

/*
 * Maximum velocity is a percentage of the player's speed.
 * If the speed is 100, the maximum velocity is 1.0.
 * If the speed is 210, the maximum velocity is 2.1.
 * The standard walking velocity is 25% of the maximum velocity.
 * Example: 100, walking = 0.2
 * The standard sprinting velocity is 50% of the maximum velocity.
 * Example: 100, sprinting = 0.5
 */
let currentVelocity = 0.2;

/*
 * Damage Formula:
 * (pre-multiplier) DMG = Weapon Damage + (Strength / 10)
 * (post-multiplier) DMG = ((pre-multiplier) + (pre-multiplier * (1 || Critical Damage Multiplier))) * (Current Velocity + 1)
 */

// Calculate the result.
let isCriticalHit = random(criticalChance, 100) >= 100;

let preMultiplier = baseWeaponDamage + ((strength + baseWeaponStrength) / 10);
let addToDamage = isCriticalHit ? (preMultiplier * criticalDamageMultiplier) : 0;
let postMultiplier = (preMultiplier + addToDamage) * (currentVelocity + 1);

console.log(
    preMultiplier
);
console.log(
    postMultiplier
);

// Functions
function random(min, max) {
    return Math.floor(Math.random() * (max - min) + min);
}