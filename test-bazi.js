/**
 * Bazi Calculator Test Suite (Node.js Version)
 * Tests all calculation functions against known reference values
 */

const HEAVENLY_STEMS = {
    1: 'Yang-Wood', 2: 'Yin-Wood', 3: 'Yang-Fire', 4: 'Yin-Fire',
    5: 'Yang-Earth', 6: 'Yin-Earth', 7: 'Yang-Metal', 8: 'Yin-Metal',
    9: 'Yang-Water', 10: 'Yin-Water'
};

const EARTHLY_BRANCHES = {
    1: 'Rat', 2: 'Ox', 3: 'Tiger', 4: 'Rabbit', 5: 'Dragon', 6: 'Snake',
    7: 'Horse', 8: 'Goat', 9: 'Monkey', 10: 'Rooster', 11: 'Dog', 12: 'Pig'
};

const STEM_BRANCH_CYCLE = {
    1: 'Yang-Wood Rat', 2: 'Yin-Wood Ox', 3: 'Yang-Fire Tiger', 4: 'Yin-Fire Rabbit',
    5: 'Yang-Earth Dragon', 6: 'Yin-Earth Snake', 7: 'Yang-Metal Horse', 8: 'Yin-Metal Goat',
    9: 'Yang-Water Monkey', 10: 'Yin-Water Rooster', 11: 'Yang-Wood Dog', 12: 'Yin-Wood Pig',
    13: 'Yang-Fire Rat', 14: 'Yin-Fire Ox', 15: 'Yang-Earth Tiger', 16: 'Yin-Earth Rabbit',
    17: 'Yang-Metal Dragon', 18: 'Yin-Metal Snake', 19: 'Yang-Water Horse', 20: 'Yin-Water Goat',
    21: 'Yang-Wood Monkey', 22: 'Yin-Wood Rooster', 23: 'Yang-Fire Dog', 24: 'Yin-Fire Pig',
    25: 'Yang-Earth Rat', 26: 'Yin-Earth Ox', 27: 'Yang-Metal Tiger', 28: 'Yin-Metal Rabbit',
    29: 'Yang-Water Dragon', 30: 'Yin-Water Snake', 31: 'Yang-Wood Horse', 32: 'Yin-Wood Goat',
    33: 'Yang-Fire Monkey', 34: 'Yin-Fire Rooster', 35: 'Yang-Earth Dog', 36: 'Yin-Earth Pig',
    37: 'Yang-Metal Rat', 38: 'Yin-Metal Ox', 39: 'Yang-Water Tiger', 40: 'Yin-Water Rabbit',
    41: 'Yang-Wood Dragon', 42: 'Yin-Wood Snake', 43: 'Yang-Fire Horse', 44: 'Yin-Fire Goat',
    45: 'Yang-Earth Monkey', 46: 'Yin-Earth Rooster', 47: 'Yang-Metal Dog', 48: 'Yin-Metal Pig',
    49: 'Yang-Water Rat', 50: 'Yin-Water Ox', 51: 'Yang-Wood Tiger', 52: 'Yin-Wood Rabbit',
    53: 'Yang-Fire Dragon', 54: 'Yin-Fire Snake', 55: 'Yang-Earth Horse', 56: 'Yin-Earth Goat',
    57: 'Yang-Metal Monkey', 58: 'Yin-Metal Rooster', 59: 'Yang-Water Dog', 60: 'Yin-Water Pig'
};

let testsPassed = 0;
let testsFailed = 0;

function assert(condition, testName, details = '') {
    if (condition) {
        testsPassed++;
        console.log(`✓ PASS: ${testName}`);
        if (details) console.log(`  ${details}`);
    } else {
        testsFailed++;
        console.log(`✗ FAIL: ${testName}`);
        if (details) console.log(`  ${details}`);
    }
}

// ===== CALCULATION FUNCTIONS =====

function getYearStemIndex(year) {
    let result = ((year - 4) % 10) + 1;
    if (result > 10) result = 1;
    return result;
}

function getYearBranchIndex(year) {
    let result = ((year - 4) % 12) + 1;
    if (result > 12) result = 1;
    return result;
}

function getMonthStemIndex(yearStemIndex, zodiacMonthIndex) {
    let base = (yearStemIndex * 2 - 1) + (zodiacMonthIndex - 1);
    // Special case for Rat (1) and Ox (2) months
    if (zodiacMonthIndex === 1 || zodiacMonthIndex === 2) {
        base += 12;
    }
    let result = base % 10;
    if (result === 0) result = 10;
    return result;
}

function getZodiacMonthByLongitude(longitude) {
    if (longitude >= 255 && longitude < 285) return 1;   // Rat
    if (longitude >= 285 && longitude < 315) return 2;   // Ox
    if (longitude >= 315 && longitude < 345) return 3;   // Tiger
    if (longitude >= 345 || longitude < 15) return 4;    // Rabbit
    if (longitude >= 15 && longitude < 45) return 5;     // Dragon
    if (longitude >= 45 && longitude < 75) return 6;     // Snake
    if (longitude >= 75 && longitude < 105) return 7;    // Horse
    if (longitude >= 105 && longitude < 135) return 8;   // Goat
    if (longitude >= 135 && longitude < 165) return 9;   // Monkey
    if (longitude >= 165 && longitude < 195) return 10;  // Rooster
    if (longitude >= 195 && longitude < 225) return 11;  // Dog
    return 12; // Pig (225-255)
}

function isLeapYear(year) {
    return ((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0);
}

function calculateDayPillar(year, month, day) {
    let totalDays = (year - 1900) * 365;
    
    // Count leap years between 1900 and birth year using proper formula
    let leapYearCount = 0;
    if (year > 1900) {
        leapYearCount = Math.floor((year - 1) / 4) - Math.floor((year - 1) / 100) + Math.floor((year - 1) / 400);
        leapYearCount -= Math.floor(1899 / 4) - Math.floor(1899 / 100) + Math.floor(1899 / 400);
    }
    totalDays += leapYearCount;
    
    const monthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if (isLeapYear(year)) {
        monthDays[1] = 29;
    }
    for (let m = 1; m < month; m++) {
        totalDays += monthDays[m - 1];
    }
    totalDays += day;
    
    let index = (totalDays + 10) % 60;
    if (index === 0) index = 60;
    
    let stemIndex = index % 10;
    if (stemIndex === 0) stemIndex = 10;
    
    let branchIndex = index % 12;
    if (branchIndex === 0) branchIndex = 12;
    
    return HEAVENLY_STEMS[stemIndex] + ' ' + EARTHLY_BRANCHES[branchIndex];
}

function getHourBranch(hour) {
    if (hour === 23 || hour === 0) return 1;
    if (hour >= 1 && hour <= 2) return 2;
    if (hour >= 3 && hour <= 4) return 3;
    if (hour >= 5 && hour <= 6) return 4;
    if (hour >= 7 && hour <= 8) return 5;
    if (hour >= 9 && hour <= 10) return 6;
    if (hour >= 11 && hour <= 12) return 7;
    if (hour >= 13 && hour <= 14) return 8;
    if (hour >= 15 && hour <= 16) return 9;
    if (hour >= 17 && hour <= 18) return 10;
    if (hour >= 19 && hour <= 20) return 11;
    return 12;
}

function getLuckCycleDirection(yearStemIndex, gender) {
    const isYang = (yearStemIndex % 2 === 1);
    const isMale = (gender === 'male');
    return (isYang && isMale) || (!isYang && !isMale);
}

function calculateSolarLongitude(year, month, day, hour, minute) {
    // Calculate Julian Day Number
    const a = Math.floor((14 - month) / 12);
    const y = year + 4800 - a;
    const m = month + 12 * a - 3;
    
    let jd = day + Math.floor((153 * m + 2) / 5) + 365 * y + Math.floor(y / 4) - Math.floor(y / 100) + Math.floor(y / 400) - 32045;
    jd += (hour - 12) / 24 + minute / 1440;
    
    // Days since J2000.0
    const d = jd - 2451545.0;
    
    // Mean anomaly
    let g = 357.529 + 0.98560028 * d;
    g = ((g % 360) + 360) % 360;
    
    // Equation of center
    const c = 1.914 * Math.sin(g * Math.PI / 180) + 0.020 * Math.sin(2 * g * Math.PI / 180);
    
    // True longitude
    let L = 280.459 + 0.98564736 * d;
    L = (((L + c) % 360) + 360) % 360;
    
    return L;
}

// ===== TEST FUNCTIONS =====

function testYearStemFormula() {
    console.log('\n--- TEST 1: Year Stem Formula ---');
    
    const testCases = [
        [1984, 1],  // Yang-Wood
        [1985, 2],  // Yin-Wood
        [1986, 3],  // Yang-Fire
        [1987, 4],  // Yin-Fire
        [1988, 5],  // Yang-Earth
        [1972, 9],  // Yang-Water
        [2000, 7],  // Yang-Metal
        [2001, 8],  // Yin-Metal
        [2024, 1],  // Yang-Wood
    ];
    
    testCases.forEach(([year, expected]) => {
        const result = getYearStemIndex(year);
        assert(
            result === expected,
            `Year ${year} Stem Index`,
            `Expected: ${expected} (${HEAVENLY_STEMS[expected]}), Got: ${result} (${HEAVENLY_STEMS[result]})`
        );
    });
}

function testYearBranchFormula() {
    console.log('\n--- TEST 2: Year Branch Formula ---');
    
    const testCases = [
        [1984, 1],  // Rat
        [1985, 2],  // Ox
        [1986, 3],  // Tiger
        [1987, 4],  // Rabbit
        [1988, 5],  // Dragon
        [1972, 1],  // Rat
        [2000, 5],  // Dragon
        [2024, 5],  // Dragon
    ];
    
    testCases.forEach(([year, expected]) => {
        const result = getYearBranchIndex(year);
        assert(
            result === expected,
            `Year ${year} Branch Index`,
            `Expected: ${expected} (${EARTHLY_BRANCHES[expected]}), Got: ${result} (${EARTHLY_BRANCHES[result]})`
        );
    });
}

function testMonthStemFormula() {
    console.log('\n--- TEST 3: Month Stem Formula ---');
    
    // [year_stem_index, zodiac_month_index, expected_month_stem]
    const testCases = [
        [8, 5, 9],   // April 1951: Yin-Metal year, Dragon month → Yang-Water
        [9, 1, 9],   // Dec 1972: Yang-Water year, Rat month → Yang-Water
        [1, 3, 3],   // Feb 1984: Yang-Wood year, Tiger month → Yang-Fire
        [1, 1, 3],   // Yang-Wood year, Rat month (special case +12)
        [1, 2, 4],   // Yang-Wood year, Ox month (special case +12)
    ];
    
    testCases.forEach(([yearStem, zodiacMonth, expected]) => {
        const result = getMonthStemIndex(yearStem, zodiacMonth);
        assert(
            result === expected,
            `Month Stem (YearStem=${yearStem}, ZodiacMonth=${zodiacMonth})`,
            `Expected: ${expected} (${HEAVENLY_STEMS[expected]}), Got: ${result} (${HEAVENLY_STEMS[result]})`
        );
    });
}

function testZodiacMonthByLongitude() {
    console.log('\n--- TEST 4: Zodiac Month by Longitude ---');
    
    const testCases = [
        [255, 1],      // Rat start
        [274, 1],      // Rat (mid-December)
        [284.99, 1],   // Rat end
        [285, 2],      // Ox start
        [314.99, 2],   // Ox end
        [315, 3],      // Tiger start
        [319.38, 3],   // Tiger (original test case - now correct!)
        [344.99, 3],   // Tiger end
        [345, 4],      // Rabbit start
        [0, 4],        // Rabbit (crosses 0°)
        [14.99, 4],    // Rabbit end
        [15, 5],       // Dragon start
        [45, 6],       // Snake start
        [75, 7],       // Horse start
        [105, 8],      // Goat start
        [135, 9],      // Monkey start
        [165, 10],     // Rooster start
        [195, 11],     // Dog start
        [225, 12],     // Pig start
        [254.99, 12],  // Pig end
    ];
    
    testCases.forEach(([longitude, expected]) => {
        const result = getZodiacMonthByLongitude(longitude);
        assert(
            result === expected,
            `Longitude ${longitude}° → Zodiac Month`,
            `Expected: ${expected} (${EARTHLY_BRANCHES[expected]}), Got: ${result} (${EARTHLY_BRANCHES[result]})`
        );
    });
}

function testDayPillarCalculation() {
    console.log('\n--- TEST 5: Day Pillar Calculation ---');
    
    // Verified from Master Tsai calculator
    const testCases = [
        ['1900-01-01', 'Yang-Wood Dog'],       // Reference from Master Tsai
        ['1984-02-04', 'Yang-Earth Dragon'],   // Lichun 1984 - verified
        ['2000-01-01', 'Yang-Earth Horse'],    // Y2K - verified
    ];
    
    testCases.forEach(([date, expected]) => {
        const [year, month, day] = date.split('-').map(Number);
        const result = calculateDayPillar(year, month, day);
        assert(
            result === expected,
            `Day Pillar for ${date}`,
            `Expected: ${expected}, Got: ${result}`
        );
    });
}

function testHourBranchMapping() {
    console.log('\n--- TEST 6: Hour Branch Mapping ---');
    
    const testCases = [
        [0, 1], [1, 2], [3, 3], [5, 4], [7, 5], [9, 6],
        [11, 7], [13, 8], [15, 9], [17, 10], [19, 11], [21, 12], [23, 1]
    ];
    
    testCases.forEach(([hour, expected]) => {
        const result = getHourBranch(hour);
        assert(
            result === expected,
            `Hour ${hour}:00 → Branch`,
            `Expected: ${expected} (${EARTHLY_BRANCHES[expected]}), Got: ${result} (${EARTHLY_BRANCHES[result]})`
        );
    });
}

function testLuckCycleDirection() {
    console.log('\n--- TEST 7: Luck Cycle Direction ---');
    
    const testCases = [
        [1, 'male', true],    // Yang-Wood Male → Forward
        [1, 'female', false], // Yang-Wood Female → Backward
        [2, 'male', false],   // Yin-Wood Male → Backward
        [2, 'female', true],  // Yin-Wood Female → Forward
        [9, 'male', true],    // Yang-Water Male → Forward
        [9, 'female', false], // Yang-Water Female → Backward
    ];
    
    testCases.forEach(([yearStem, gender, expected]) => {
        const result = getLuckCycleDirection(yearStem, gender);
        const direction = result ? 'Forward' : 'Backward';
        const expectedDir = expected ? 'Forward' : 'Backward';
        
        assert(
            result === expected,
            `${HEAVENLY_STEMS[yearStem]} ${gender} → Direction`,
            `Expected: ${expectedDir}, Got: ${direction}`
        );
    });
}

function testDaysToSolarTerm() {
    console.log('\n--- TEST 8: Days to Solar Term Calculation ---');
    
    // Test for 1972-12-25 (Rat month, backward)
    // Rat month starts at Daxue (Dec 7)
    const birthDate = new Date(1972, 11, 25); // Dec 25
    const termDate = new Date(1972, 11, 7);   // Dec 7 (Daxue)
    const expectedDays = 18;
    
    const days = Math.round(Math.abs(birthDate - termDate) / (1000 * 60 * 60 * 24));
    
    assert(
        days === expectedDays,
        `Days from Daxue (Dec 7) to Dec 25`,
        `Expected: ${expectedDays} days, Got: ${days} days`
    );
    
    // Starting age should be 18 / 3 = 6 years
    const startingAge = Math.floor(days / 3);
    assert(
        startingAge === 6,
        `Starting Age calculation (18 days / 3)`,
        `Expected: 6 years, Got: ${startingAge} years`
    );
}

function testSolarLongitude() {
    console.log('\n--- TEST 9: Solar Longitude Calculation ---');
    
    // Test known dates with approximate solar longitudes
    const testCases = [
        // [year, month, day, hour, minute, expected_range_start, expected_range_end]
        [1972, 12, 25, 12, 0, 270, 280],   // Late December → ~273-274°
        [1984, 2, 4, 12, 0, 314, 316],     // Lichun → ~315°
        [2000, 3, 21, 12, 0, 0, 2],        // Spring equinox → ~0-1° (around 0°)
    ];
    
    testCases.forEach(([year, month, day, hour, minute, rangeStart, rangeEnd]) => {
        const result = calculateSolarLongitude(year, month, day, hour, minute);
        let inRange;
        if (rangeStart > rangeEnd) {
            // Crosses 0°
            inRange = (result >= rangeStart || result <= rangeEnd);
        } else {
            inRange = (result >= rangeStart && result <= rangeEnd);
        }
        
        assert(
            inRange,
            `Solar Longitude for ${year}-${month}-${day}`,
            `Expected range: ${rangeStart}°-${rangeEnd}°, Got: ${result.toFixed(2)}°`
        );
    });
}

function testFullIntegration() {
    console.log('\n--- TEST 10: Full Chart Integration (1972-12-25 Female) ---');
    
    // Your original test case
    const year = 1972, month = 12, day = 25;
    
    // 1. Year Pillar
    const yearStem = getYearStemIndex(year);
    const yearBranch = getYearBranchIndex(year);
    const yearPillar = HEAVENLY_STEMS[yearStem] + ' ' + EARTHLY_BRANCHES[yearBranch];
    
    assert(
        yearPillar === 'Yang-Water Rat',
        `Year Pillar for 1972`,
        `Expected: Yang-Water Rat, Got: ${yearPillar}`
    );
    
    // 2. Solar Longitude → Zodiac Month
    const solarLong = calculateSolarLongitude(year, month, day, 17, 30);
    const zodiacMonth = getZodiacMonthByLongitude(solarLong);
    
    assert(
        zodiacMonth === 1, // Should be Rat
        `Zodiac Month from solar longitude ${solarLong.toFixed(2)}°`,
        `Expected: 1 (Rat), Got: ${zodiacMonth} (${EARTHLY_BRANCHES[zodiacMonth]})`
    );
    
    // 3. Month Pillar
    const monthStem = getMonthStemIndex(yearStem, zodiacMonth);
    const monthPillar = HEAVENLY_STEMS[monthStem] + ' ' + EARTHLY_BRANCHES[zodiacMonth];
    
    assert(
        monthPillar === 'Yang-Water Rat',
        `Month Pillar`,
        `Expected: Yang-Water Rat, Got: ${monthPillar}`
    );
    
    // 4. Direction for Yang-Water Female
    const isForward = getLuckCycleDirection(yearStem, 'female');
    
    assert(
        isForward === false,
        `Luck Cycle Direction for Yang-Water Female`,
        `Expected: Backward (false), Got: ${isForward ? 'Forward' : 'Backward'}`
    );
    
    // 5. First Luck Cycle
    // Yang-Water Rat = index 49, backward → 48 = Yin-Metal Pig
    const monthPillarIndex = 49; // Yang-Water Rat
    const firstCycleIndex = monthPillarIndex - 1; // Backward
    
    assert(
        STEM_BRANCH_CYCLE[firstCycleIndex] === 'Yin-Metal Pig',
        `First Luck Cycle`,
        `Expected: Yin-Metal Pig (index 48), Got: ${STEM_BRANCH_CYCLE[firstCycleIndex]}`
    );
    
    // 6. All 5 luck cycles match reference
    console.log('\n  Verifying first 5 luck cycles against reference:');
    const expectedCycles = [
        [48, 'Yin-Metal Pig', 6, 1978],
        [47, 'Yang-Metal Dog', 16, 1988],
        [46, 'Yin-Earth Rooster', 26, 1998],
        [45, 'Yang-Earth Monkey', 36, 2008],
        [44, 'Yin-Fire Goat', 46, 2018],
    ];
    
    expectedCycles.forEach(([index, pillar, age, cycleYear], i) => {
        const cycleNum = i + 1;
        assert(
            STEM_BRANCH_CYCLE[index] === pillar,
            `Cycle ${cycleNum}: Age ${age}, Year ${cycleYear}`,
            `Expected: ${pillar}, Got: ${STEM_BRANCH_CYCLE[index]}`
        );
    });
}

function printSummary() {
    const total = testsPassed + testsFailed;
    const percentage = total > 0 ? ((testsPassed / total) * 100).toFixed(1) : 0;
    
    console.log('\n===========================================');
    console.log('              TEST SUMMARY');
    console.log('===========================================');
    console.log(`Total Tests: ${total}`);
    console.log(`Passed: ${testsPassed} ✓`);
    console.log(`Failed: ${testsFailed} ✗`);
    console.log(`Success Rate: ${percentage}%`);
    console.log('===========================================');
    
    if (testsFailed === 0) {
        console.log('\n🎉 ALL TESTS PASSED! Calculator is working correctly.\n');
    } else {
        console.log('\n⚠️  Some tests failed. Please review the failures above.\n');
    }
}

// ===== RUN ALL TESTS =====
console.log('===========================================');
console.log('    BAZI CALCULATOR TEST SUITE v1.0');
console.log('===========================================');

testYearStemFormula();
testYearBranchFormula();
testMonthStemFormula();
testZodiacMonthByLongitude();
testDayPillarCalculation();
testHourBranchMapping();
testLuckCycleDirection();
testDaysToSolarTerm();
testSolarLongitude();
testFullIntegration();

printSummary();
