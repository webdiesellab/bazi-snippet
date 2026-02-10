<?php
// Test the exact solar term date calculation
function calculate_solar_longitude($year, $month, $day, $hour, $minute) {
    $a = floor((14 - $month) / 12);
    $y = $year + 4800 - $a;
    $m = $month + 12 * $a - 3;
    $jd = $day + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - floor($y / 100) + floor($y / 400) - 32045;
    $jd += ($hour - 12) / 24 + $minute / 1440;
    $d = $jd - 2451545.0;
    $g = 357.529 + 0.98560028 * $d;
    $g = fmod($g, 360);
    if ($g < 0) $g += 360;
    $c = 1.914 * sin(deg2rad($g)) + 0.020 * sin(deg2rad(2 * $g));
    $L = 280.459 + 0.98564736 * $d;
    $L = fmod($L + $c, 360);
    if ($L < 0) $L += 360;
    return $L;
}

// Find when sun reaches 75 degrees (Mangzhong - start of Horse month)
echo "Finding Mangzhong 2005 (75 degrees):\n";
for ($d = 1; $d <= 10; $d++) {
    $long = calculate_solar_longitude(2005, 6, $d, 12, 0);
    echo "June $d: " . number_format($long, 2) . " degrees\n";
}

echo "\n25 May 2005 longitude: " . number_format(calculate_solar_longitude(2005, 5, 25, 12, 0), 2) . " degrees\n";

// Calculate days from May 25 to exact Mangzhong date
echo "\n--- Testing Days Calculation ---\n";

// Find the exact day when longitude crosses 75
for ($d = 4; $d <= 7; $d++) {
    for ($h = 0; $h <= 23; $h++) {
        $long = calculate_solar_longitude(2005, 6, $d, $h, 0);
        if ($long >= 74.9 && $long <= 75.1) {
            echo "75 degrees at June $d, hour $h: $long\n";
        }
    }
}

// Days calculation
$birth = strtotime("2005-05-25");
$term_june5 = strtotime("2005-06-05");
$term_june6 = strtotime("2005-06-06");

echo "\nDays from May 25 to June 5: " . (($term_june5 - $birth) / 86400) . " days\n";
echo "Days from May 25 to June 6: " . (($term_june6 - $birth) / 86400) . " days\n";

echo "\nIf 11 days: " . floor(11/3) . " years (remainder " . (11 % 3) . ")\n";
echo "If 12 days: " . floor(12/3) . " years (remainder " . (12 % 3) . ")\n";
