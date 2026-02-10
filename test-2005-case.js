// Test specifically for the 2005-05-25 Female GMT-8 case
// Expected results: Age 3, Year 2008, Yang-Water Horse

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

function findSolarTermExact(year, targetLong, approxMonth, approxDay) {
    const approxDate = new Date(year, approxMonth - 1, approxDay, 12, 0, 0);
    let start = new Date(approxDate.getTime() - 5 * 86400000);
    let end = new Date(approxDate.getTime() + 5 * 86400000);
    
    while ((end - start) > 60000) { // Within 1 minute precision
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

console.log("=== Testing 2005-05-25 17:30 Female GMT-8 ===\n");

// Input data
const birthYear = 2005;
const birthMonth = 5;
const birthDay = 25;
const birthHour = 17;
const birthMinute = 30;
const gender = 'female';

// Calculate Year Pillar
const baziYear = (birthMonth < 2 || (birthMonth === 2 && birthDay < 4)) ? birthYear - 1 : birthYear;
const yearStemIndex = ((baziYear - 4) % 10) + 1;
const yearBranchIndex = ((baziYear - 4) % 12) + 1;
console.log(`Bazi Year: ${baziYear}`);
console.log(`Year Stem Index: ${yearStemIndex} (1=Yang-Wood, 2=Yin-Wood)`);
console.log(`Year is ${yearStemIndex % 2 === 1 ? 'Yang' : 'Yin'}`);

// Determine direction
const isYang = (yearStemIndex % 2 === 1);
const isMale = (gender === 'male');
const isForward = (isYang && isMale) || (!isYang && !isMale);
console.log(`\nDirection: ${isForward ? 'Forward' : 'Backward'}`);
console.log(`(${isYang ? 'Yang' : 'Yin'} ${isMale ? 'Male' : 'Female'} = ${isForward ? 'Forward' : 'Backward'})`);

// Calculate solar longitude
const solarLong = calculateSolarLongitude(birthYear, birthMonth, birthDay, birthHour, birthMinute);
console.log(`\nSolar Longitude: ${solarLong.toFixed(2)}° (Snake month: 45°-75°)`);

// Find zodiac month (Snake = 6)
let zodiacMonth;
if (solarLong >= 45 && solarLong < 75) zodiacMonth = 6; // Snake
console.log(`Zodiac Month: ${zodiacMonth} (Snake)`);

// For Forward direction, count days TO the NEXT solar term
// Snake month ends at Horse month start = Mangzhong (75°)
const mangzhong2005 = findSolarTermExact(2005, 75, 6, 6);
console.log(`\nMangzhong 2005 (75°): ${mangzhong2005.toISOString()}`);
console.log(`  = ${mangzhong2005.toLocaleDateString()} ${mangzhong2005.toLocaleTimeString()}`);

// Birth timestamp
const birthTimestamp = new Date(2005, 4, 25, 17, 30, 0); // May 25, 17:30 local time
console.log(`Birth: ${birthTimestamp.toISOString()}`);

// Days calculation
const diffMs = mangzhong2005.getTime() - birthTimestamp.getTime();
const diffDays = diffMs / (1000 * 60 * 60 * 24);
console.log(`\nDays to Mangzhong: ${diffDays.toFixed(4)} days`);
console.log(`Rounded: ${Math.round(diffDays)} days`);

// Starting age
const startingAge = Math.floor(Math.round(diffDays) / 3);
const remainder = Math.round(diffDays) % 3;
console.log(`\nStarting Age: ${startingAge} years (remainder ${remainder})`);

// Expected
console.log("\n=== EXPECTED RESULTS ===");
console.log("From reference calculator:");
console.log("Age 3, Year 2008, Yang-Water Horse");
console.log("");

// Verify
const success = (startingAge === 3);
console.log(`Result: ${success ? '✓ PASS' : '✗ FAIL'} - Starting age is ${startingAge}`);
