// Test solar term date calculation for 2005-05-25 case

function calculateSolarLongitude(year, month, day, hour, minute) {
    const a = Math.floor((14 - month) / 12);
    const y = year + 4800 - a;
    const m = month + 12 * a - 3;
    
    let jd = day + Math.floor((153 * m + 2) / 5) + 365 * y + Math.floor(y / 4) - Math.floor(y / 100) + Math.floor(y / 400) - 32045;
    jd += (hour - 12) / 24 + minute / 1440;
    
    const d = jd - 2451545.0;
    
    let g = 357.529 + 0.98560028 * d;
    g = g % 360;
    if (g < 0) g += 360;
    
    const c = 1.914 * Math.sin(g * Math.PI / 180) + 0.020 * Math.sin(2 * g * Math.PI / 180);
    
    let L = 280.459 + 0.98564736 * d;
    L = (L + c) % 360;
    if (L < 0) L += 360;
    
    return L;
}

console.log("=== Testing 2005-05-25 Female GMT-8 Case ===\n");

console.log("Finding Mangzhong 2005 (75 degrees - starts Horse month):");
for (let d = 3; d <= 8; d++) {
    const long = calculateSolarLongitude(2005, 6, d, 12, 0);
    console.log(`June ${d}: ${long.toFixed(2)}°`);
}

console.log("\n25 May 2005 longitude:", calculateSolarLongitude(2005, 5, 25, 12, 0).toFixed(2) + "°");

// Find exact hour when 75° is crossed
console.log("\n--- Finding exact crossing of 75° ---");
for (let d = 4; d <= 6; d++) {
    for (let h = 0; h <= 23; h++) {
        const long = calculateSolarLongitude(2005, 6, d, h, 0);
        if (long >= 74.5 && long <= 75.5) {
            console.log(`June ${d}, ${h}:00 -> ${long.toFixed(3)}°`);
        }
    }
}

// Days calculation
console.log("\n--- Days Calculation ---");
const birth = new Date(2005, 4, 25); // May 25, 2005
const june5 = new Date(2005, 5, 5);
const june6 = new Date(2005, 5, 6);

const daysToJune5 = Math.round((june5 - birth) / (1000 * 60 * 60 * 24));
const daysToJune6 = Math.round((june6 - birth) / (1000 * 60 * 60 * 24));

console.log(`Days from May 25 to June 5: ${daysToJune5} days`);
console.log(`Days from May 25 to June 6: ${daysToJune6} days`);

console.log(`\nIf ${daysToJune5} days: ${Math.floor(daysToJune5/3)} years (remainder ${daysToJune5 % 3})`);
console.log(`If ${daysToJune6} days: ${Math.floor(daysToJune6/3)} years (remainder ${daysToJune6 % 3})`);

// Expected: 3 years (age 3, year 2008)
console.log("\n--- Expected Result ---");
console.log("Reference shows: Age 3, Year 2008");
console.log("This means: 9-11 days to solar term (9/3=3, 10/3=3, 11/3=3)");

// Binary search to find exact date when sun reaches 75°
console.log("\n--- Binary Search for Exact 75° Date ---");

function findSolarTermDate(year, targetLong, approxMonth, approxDay) {
    const approxDate = new Date(year, approxMonth - 1, approxDay);
    let start = new Date(approxDate.getTime() - 5 * 86400000);
    let end = new Date(approxDate.getTime() + 5 * 86400000);
    
    while ((end - start) > 3600000) { // Within 1 hour precision
        const mid = new Date((start.getTime() + end.getTime()) / 2);
        const longitude = calculateSolarLongitude(
            mid.getFullYear(), mid.getMonth() + 1, mid.getDate(),
            mid.getHours(), mid.getMinutes()
        );
        
        let diff = longitude - targetLong;
        if (diff > 180) diff -= 360;
        if (diff < -180) diff += 360;
        
        if (diff < 0) {
            start = mid;
        } else {
            end = mid;
        }
    }
    
    return end;
}

const mangzhong2005 = findSolarTermDate(2005, 75, 6, 6);
console.log(`Mangzhong 2005 exact date: ${mangzhong2005.toISOString()}`);
console.log(`That's: June ${mangzhong2005.getDate()}, ${mangzhong2005.getHours()}:${String(mangzhong2005.getMinutes()).padStart(2, '0')}`);

const exactDays = Math.round((mangzhong2005 - birth) / (1000 * 60 * 60 * 24));
console.log(`\nDays from May 25 to Mangzhong: ${exactDays} days`);
console.log(`Starting age: ${Math.floor(exactDays/3)} years (remainder ${exactDays % 3})`);
