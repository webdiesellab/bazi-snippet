<?php
/**
 * Standalone test for Bazi Calculator Plugin
 * Run: php test-plugin.php
 */

// Mock WordPress functions for testing
if (!function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback) {}
    function add_action($hook, $callback) {}
    function shortcode_atts($defaults, $atts, $tag) { return $defaults; }
    function wp_nonce_field($action, $name) {}
    function _e($text, $domain) { echo $text; }
    function __($text, $domain) { return $text; }
    function selected($selected, $current) { return $selected == $current ? ' selected' : ''; }
    function esc_attr($text) { return htmlspecialchars($text); }
    function esc_attr_e($text, $domain) { echo htmlspecialchars($text); }
    function date_i18n($format, $timestamp) { return date($format, $timestamp); }
    function sanitize_text_field($str) { return trim(strip_tags($str)); }
    function wp_send_json($response) { echo json_encode($response, JSON_PRETTY_PRINT); }
    function wp_verify_nonce($nonce, $action) { return true; }
    define('ABSPATH', true);
}

// Include the calculator class
require_once __DIR__ . '/bazi-plugin/includes/class-bazi-calculator.php';

/**
 * Test class that extends Bazi_Calculator to access private methods
 */
class Bazi_Calculator_Test extends Bazi_Calculator {
    
    public function test_calculate($year, $month, $day, $hour, $minute, $timezone, $gender, $longitude = null) {
        // Calculate local solar time
        $solar_time_data = $this->call_private('calculate_local_solar_time', 
            $year, $month, $day, $hour, $minute, $timezone, $longitude);
        
        // Get solar longitude
        $solar_longitude = $this->call_private('calculate_solar_longitude',
            $solar_time_data['year'], $solar_time_data['month'], $solar_time_data['day'],
            $solar_time_data['hour'], $solar_time_data['minute']);
        
        // Get zodiac month
        $zodiac_month_index = $this->call_private('get_zodiac_month_by_longitude', $solar_longitude);
        
        // Calculate Bazi year
        $bazi_year = $this->call_private('calculate_bazi_year', $year, $month, $day);
        
        // Calculate pillars
        $year_pillar = $this->call_private('calculate_correct_year_pillar', $bazi_year);
        $month_pillar = $this->call_private('calculate_month_pillar_by_longitude', $bazi_year, $solar_longitude, $year_pillar);
        $day_pillar = $this->call_private('calculate_day_pillar', $year, $month, $day);
        $hour_pillar = $this->call_private('calculate_hour_pillar', $hour, $day_pillar);
        
        // Get earthly branches for display
        $earthly_branches = array(
            1 => 'Rat', 2 => 'Ox', 3 => 'Tiger', 4 => 'Rabbit', 5 => 'Dragon', 6 => 'Snake',
            7 => 'Horse', 8 => 'Goat', 9 => 'Monkey', 10 => 'Rooster', 11 => 'Dog', 12 => 'Pig'
        );
        
        return array(
            'input' => array(
                'date' => "$year-$month-$day $hour:$minute",
                'timezone' => "GMT" . ($timezone >= 0 ? "+$timezone" : $timezone),
                'gender' => $gender,
                'longitude' => $longitude ?? ($timezone * 15) . '° (auto)'
            ),
            'solar_time' => $solar_time_data['display'],
            'solar_longitude' => round($solar_longitude, 2) . '°',
            'zodiac_month' => $earthly_branches[$zodiac_month_index],
            'pillars' => array(
                'year' => $year_pillar,
                'month' => $month_pillar,
                'day' => $day_pillar,
                'hour' => $hour_pillar
            )
        );
    }
    
    private function call_private($method, ...$args) {
        $reflection = new ReflectionMethod($this, $method);
        $reflection->setAccessible(true);
        return $reflection->invoke($this, ...$args);
    }
}

// Run tests
echo "========================================\n";
echo "   BAZI CALCULATOR PLUGIN TEST\n";
echo "========================================\n\n";

$calculator = new Bazi_Calculator_Test();

// Test Case 1: Original test case from snippet
echo "TEST 1: 2001-10-15 17:30 GMT-8 Female\n";
echo "----------------------------------------\n";
$result = $calculator->test_calculate(2001, 10, 15, 17, 30, -8, 'female');
print_results($result);

echo "\nExpected (from original code):\n";
echo "  Year:  Yin-Metal Snake\n";
echo "  Month: Yang-Earth Dog\n";
echo "  Day:   Yang-Metal Rat\n";
echo "  Hour:  Yin-Metal Rooster\n";

// Test Case 2: Different date
echo "\n\nTEST 2: 1990-03-15 12:00 GMT+8 Male\n";
echo "----------------------------------------\n";
$result = $calculator->test_calculate(1990, 3, 15, 12, 0, 8, 'male');
print_results($result);

// Test Case 3: Edge case - near Chinese New Year
echo "\n\nTEST 3: 2000-02-03 10:00 GMT+8 Male (before Lichun)\n";
echo "----------------------------------------\n";
$result = $calculator->test_calculate(2000, 2, 3, 10, 0, 8, 'male');
print_results($result);

// Test Case 4: After Lichun
echo "\n\nTEST 4: 2000-02-05 10:00 GMT+8 Male (after Lichun)\n";
echo "----------------------------------------\n";
$result = $calculator->test_calculate(2000, 2, 5, 10, 0, 8, 'male');
print_results($result);

echo "\n========================================\n";
echo "   ALL TESTS COMPLETED!\n";
echo "========================================\n";

function print_results($result) {
    echo "Input: {$result['input']['date']} {$result['input']['timezone']} {$result['input']['gender']}\n";
    echo "Longitude: {$result['input']['longitude']}\n";
    echo "Solar Time: {$result['solar_time']}\n";
    echo "Solar Longitude: {$result['solar_longitude']}\n";
    echo "Zodiac Month: {$result['zodiac_month']}\n";
    echo "Pillars:\n";
    echo "  Year:  {$result['pillars']['year']}\n";
    echo "  Month: {$result['pillars']['month']}\n";
    echo "  Day:   {$result['pillars']['day']}\n";
    echo "  Hour:  {$result['pillars']['hour']}\n";
}
