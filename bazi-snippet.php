/**
 * Plugin Name: Bazi Calculator
 * Description: Accurate Bazi calculator with precise astronomical calculations
 * Version: 21.6
 * Author: Web Diesel
 * License: GPL v2 or later
 * Text Domain: web-diesel.com
 */

defined('ABSPATH') or die('No direct access allowed!');

class MasterTsaiBaziCalculatorComplete {
    
    private $heavenly_stems = array(
        1 => 'Yang-Wood',
        2 => 'Yin-Wood', 
        3 => 'Yang-Fire',
        4 => 'Yin-Fire',
        5 => 'Yang-Earth',
        6 => 'Yin-Earth',
        7 => 'Yang-Metal',
        8 => 'Yin-Metal',
        9 => 'Yang-Water',
        10 => 'Yin-Water'
    );
    
    private $earthly_branches = array(
        1 => 'Rat',
        2 => 'Ox',
        3 => 'Tiger',
        4 => 'Rabbit',
        5 => 'Dragon',
        6 => 'Snake',
        7 => 'Horse',
        8 => 'Goat',
        9 => 'Monkey',
        10 => 'Rooster',
        11 => 'Dog',
        12 => 'Pig'
    );
    
    private $stem_branch_cycle = array(
        1 => 'Yang-Wood Rat', 2 => 'Yin-Wood Ox', 3 => 'Yang-Fire Tiger', 4 => 'Yin-Fire Rabbit',
        5 => 'Yang-Earth Dragon', 6 => 'Yin-Earth Snake', 7 => 'Yang-Metal Horse', 8 => 'Yin-Metal Goat',
        9 => 'Yang-Water Monkey', 10 => 'Yin-Water Rooster', 11 => 'Yang-Wood Dog', 12 => 'Yin-Wood Pig',
        13 => 'Yang-Fire Rat', 14 => 'Yin-Fire Ox', 15 => 'Yang-Earth Tiger', 16 => 'Yin-Earth Rabbit',
        17 => 'Yang-Metal Dragon', 18 => 'Yin-Metal Snake', 19 => 'Yang-Water Horse', 20 => 'Yin-Water Goat',
        21 => 'Yang-Wood Monkey', 22 => 'Yin-Wood Rooster', 23 => 'Yang-Fire Dog', 24 => 'Yin-Fire Pig',
        25 => 'Yang-Earth Rat', 26 => 'Yin-Earth Ox', 27 => 'Yang-Metal Tiger', 28 => 'Yin-Metal Rabbit',
        29 => 'Yang-Water Dragon', 30 => 'Yin-Water Snake', 31 => 'Yang-Wood Horse', 32 => 'Yin-Wood Goat',
        33 => 'Yang-Fire Monkey', 34 => 'Yin-Fire Rooster', 35 => 'Yang-Earth Dog', 36 => 'Yin-Earth Pig',
        37 => 'Yang-Metal Rat', 38 => 'Yin-Metal Ox', 39 => 'Yang-Water Tiger', 40 => 'Yin-Water Rabbit',
        41 => 'Yang-Wood Dragon', 42 => 'Yin-Wood Snake', 43 => 'Yang-Fire Horse', 44 => 'Yin-Fire Goat',
        45 => 'Yang-Earth Monkey', 46 => 'Yin-Earth Rooster', 47 => 'Yang-Metal Dog', 48 => 'Yin-Metal Pig',
        49 => 'Yang-Water Rat', 50 => 'Yin-Water Ox', 51 => 'Yang-Wood Tiger', 52 => 'Yin-Wood Rabbit',
        53 => 'Yang-Fire Dragon', 54 => 'Yin-Fire Snake', 55 => 'Yang-Earth Horse', 56 => 'Yin-Earth Goat',
        57 => 'Yang-Metal Monkey', 58 => 'Yin-Metal Rooster', 59 => 'Yang-Water Dog', 60 => 'Yin-Water Pig'
    );
    
    private $tiger_index_table = array(
        'Yang-Wood' => 3,   // Tiger Month Stem: Yang-Fire
        'Yin-Wood' => 5,    // Tiger Month Stem: Yang-Earth
        'Yang-Fire' => 7,   // Tiger Month Stem: Yang-Metal
        'Yin-Fire' => 9,    // Tiger Month Stem: Yang-Water
        'Yang-Earth' => 1,  // Tiger Month Stem: Yang-Wood
        'Yin-Earth' => 3,   // Tiger Month Stem: Yang-Fire
        'Yang-Metal' => 5,  // Tiger Month Stem: Yang-Earth
        'Yin-Metal' => 7,   // Tiger Month Stem: Yang-Metal
        'Yang-Water' => 9,  // Tiger Month Stem: Yang-Water
        'Yin-Water' => 1    // Tiger Month Stem: Yang-Wood
    );
    
    // Solar terms for zodiac month starts with improved accuracy
    private $zodiac_month_terms = array(
        1 => array('name' => 'Xiaohan', 'month' => 1, 'day' => 6, 'longitude' => 285),
        2 => array('name' => 'Lichun', 'month' => 2, 'day' => 4, 'longitude' => 315),
        3 => array('name' => 'Jingzhe', 'month' => 3, 'day' => 5, 'longitude' => 345),
        4 => array('name' => 'Qingming', 'month' => 4, 'day' => 4, 'longitude' => 15),
        5 => array('name' => 'Lixia', 'month' => 5, 'day' => 5, 'longitude' => 45),
        6 => array('name' => 'Mangzhong', 'month' => 6, 'day' => 6, 'longitude' => 75),
        7 => array('name' => 'Xiaoshu', 'month' => 7, 'day' => 7, 'longitude' => 105),
        8 => array('name' => 'Liqiu', 'month' => 8, 'day' => 7, 'longitude' => 135),
        9 => array('name' => 'Bailu', 'month' => 9, 'day' => 7, 'longitude' => 165),
        10 => array('name' => 'Hanlu', 'month' => 10, 'day' => 8, 'longitude' => 195),
        11 => array('name' => 'Lidong', 'month' => 11, 'day' => 7, 'longitude' => 225),
        12 => array('name' => 'Daxue', 'month' => 12, 'day' => 7, 'longitude' => 255)
    );
    
    public function __construct() {
        add_shortcode('bazi_calculator', array($this, 'render_calculator'));
        add_action('wp_ajax_calculate_bazi', array($this, 'ajax_calculate_bazi'));
        add_action('wp_ajax_nopriv_calculate_bazi', array($this, 'ajax_calculate_bazi'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
    }
    
    public function render_calculator($atts) {
        ob_start();
        ?>
        <div class="bazi-calculator-container">
            <div class="bazi-header">
                <h1>Bazi Calculator</h1>
                <p class="bazi-subtitle">Complete and accurate Bazi calculator with solar time correction</p>
                <p class="bazi-version"></p>
            </div>
            
            <form id="bazi-form" class="bazi-form">
                <div class="form-section">
                    <h3>Birth Information</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="birth_year">Birth Year *</label>
                            <input type="number" id="birth_year" name="birth_year" min="1900" max="2100" value="2001" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="birth_month">Birth Month *</label>
                            <select id="birth_month" name="birth_month" required>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i == 10 ? 'selected' : ''; ?>>
                                        <?php echo date('F', mktime(0,0,0,$i,1)); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="birth_day">Birth Day *</label>
                            <input type="number" id="birth_day" name="birth_day" min="1" max="31" value="15" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="birth_hour">Birth Hour (24h) *</label>
                            <input type="number" id="birth_hour" name="birth_hour" min="0" max="23" value="17" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="birth_minute">Birth Minute *</label>
                            <input type="number" id="birth_minute" name="birth_minute" min="0" max="59" value="30" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="timezone">Timezone (GMT) *</label>
                            <select id="timezone" name="timezone" required>
                                <?php for($i = -12; $i <= 14; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i == -8 ? 'selected' : ''; ?>>
                                        GMT<?php echo $i >= 0 ? '+' . $i : $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select id="gender" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female" selected>Female</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="longitude">Longitude (Optional, e.g., -118.24)</label>
                            <input type="text" id="longitude" name="longitude" placeholder="e.g., -118.24 or 121.47" >
                            <small>Decimal degrees. Positive for East, negative for West. If empty, uses timezone center longitude.</small>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="bazi-button calculate-btn">Calculate Bazi Chart</button>
                    <button type="reset" class="bazi-button reset-btn">Reset Form</button>
                </div>
            </form>
            
            <div id="bazi-results" class="bazi-results" style="display: none;">
                <div class="results-header">
                    <h3>Your Bazi Chart Results</h3>
                </div>
                
                <div class="personal-info">
                    <p><strong>Birth Date:</strong> <span id="result-birthdate"></span></p>
                    <p><strong>Gender:</strong> <span id="result-gender"></span></p>
                    <p><strong>Timezone:</strong> <span id="result-timezone"></span></p>
                    <p><strong>Longitude:</strong> <span id="result-longitude"></span></p>
                    <p><strong>Local Solar Time:</strong> <span id="result-solar-time"></span></p>
                    <p><strong>Solar Longitude:</strong> <span id="result-solar-longitude"></span>°</p>
                    <p><strong>Zodiac Month:</strong> <span id="result-zodiac-month"></span></p>
                </div>
                
                <div class="four-pillars">
                    <h4>Four Pillars of Destiny</h4>
                    <div class="pillars-grid">
                        <div class="pillar">
                            <div class="pillar-label">Year Pillar</div>
                            <div class="pillar-value" id="year-pillar">-</div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-label">Month Pillar</div>
                            <div class="pillar-value" id="month-pillar">-</div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-label">Day Pillar</div>
                            <div class="pillar-value" id="day-pillar">-</div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-label">Hour Pillar</div>
                            <div class="pillar-value" id="hour-pillar">-</div>
                        </div>
                    </div>
                </div>
                
                <div class="luck-cycles-section">
                    <h4>Ten-Year Major Luck Cycles</h4>
                    <div class="luck-cycles-info">
                        <p><strong>Direction:</strong> <span id="cycle-direction"></span></p>
                        <p><strong>First Cycle Starts at Age:</strong> <span id="first-cycle-age"></span></p>
                        <p><strong>Days to Solar Term:</strong> <span id="days-to-term"></span></p>
                        <p><strong>Solar Term Used:</strong> <span id="solar-term"></span></p>
                    </div>
                    
                    <div class="cycles-table-container">
                        <table class="cycles-table">
                            <thead>
                                <tr>
                                    <th>Cycle</th>
                                    <th>Age</th>
                                    <th>Year</th>
                                    <th>Luck Pillar</th>
                                </tr>
                            </thead>
                            <tbody id="luck-cycles-body">
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="verification-section">
                    <h4>Verify Your Results</h4>
                    <p>For comparison with the official calculator:</p>
                    <div class="verification-link">
                        <a href="#" id="verification-url" target="_blank" class="verify-button">Verify Online at ChineseFortuneCalendar.com</a>
                    </div>
                </div>
            </div>
            
            <div id="bazi-loading" class="bazi-loading" style="display: none;">
                <div class="loading-spinner"></div>
                <p>Calculating Bazi chart with solar time correction...</p>
            </div>
            
            <div id="bazi-error" class="bazi-error" style="display: none;">
                <p>Error in calculation. Please check your inputs and try again.</p>
            </div>
        </div>
        
        <style>
        .bazi-calculator-container { max-width: 1000px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif; }
        .bazi-header { text-align: center; margin-bottom: 30px; }
        .bazi-header h1 { color: #2c3e50; }
        .bazi-version { color: #4CAF50; font-weight: bold; font-size: 14px; margin-top: 5px; }
        .form-section { background: #f5f5f5; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .form-actions { text-align: center; margin: 30px 0; }
        .bazi-button { padding: 12px 30px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 0 10px; }
        .reset-btn { background: #757575; }
        .bazi-results { background: white; padding: 20px; border-radius: 5px; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .personal-info { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .personal-info p { margin: 5px 0; }
        .pillars-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin: 20px 0; }
        .pillar { border: 2px solid #4CAF50; border-radius: 5px; padding: 15px; text-align: center; }
        .pillar-label { font-size: 14px; color: #666; margin-bottom: 5px; }
        .pillar-value { font-size: 16px; font-weight: bold; color: #2c3e50; }
        .luck-cycles-section { margin: 30px 0; padding: 20px; background: #f9f9f9; border-radius: 5px; }
        .luck-cycles-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .cycles-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .cycles-table th { background: #4CAF50; color: white; padding: 12px; text-align: center; }
        .cycles-table td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        .cycles-table tr:nth-child(even) { background: #f5f5f5; }
        .verification-section { margin-top: 30px; padding: 20px; background: #f0f7ff; border-radius: 5px; }
        .verify-button { display: inline-block; padding: 12px 25px; background: #ff9800; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .bazi-loading { text-align: center; padding: 40px; }
        .loading-spinner { border: 5px solid #f3f3f3; border-top: 5px solid #4CAF50; border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .bazi-error { background: #ffebee; border: 1px solid #ef5350; color: #d32f2f; padding: 20px; border-radius: 5px; margin-top: 20px; text-align: center; }
        
        @media (max-width: 768px) {
            .pillars-grid { grid-template-columns: repeat(2, 1fr); }
            .luck-cycles-info { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .pillars-grid { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('#bazi-form').on('submit', function(e) {
                e.preventDefault();
                
                $('#bazi-loading').show();
                $('#bazi-results').hide();
                $('#bazi-error').hide();
                
                var formData = $(this).serialize() + '&action=calculate_bazi';
                
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        $('#bazi-loading').hide();
                        
                        if (response.success) {
                            displayBaziResults(response.data);
                        } else {
                            $('#bazi-error').show().find('p').text('Error: ' + response.data);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#bazi-loading').hide();
                        $('#bazi-error').show().find('p').text('Server error: ' + error);
                    }
                });
            });
            
            function displayBaziResults(data) {
                $('#result-birthdate').text(data.birth_date);
                $('#result-gender').text(data.gender);
                $('#result-timezone').text('GMT' + (data.timezone >= 0 ? '+' + data.timezone : data.timezone));
                $('#result-longitude').text(data.longitude_display);
                $('#result-solar-time').text(data.local_solar_time);
                $('#result-solar-longitude').text(data.solar_longitude.toFixed(2));
                $('#result-zodiac-month').text(data.zodiac_month);
                
                $('#year-pillar').text(data.year_pillar);
                $('#month-pillar').text(data.month_pillar);
                $('#day-pillar').text(data.day_pillar);
                $('#hour-pillar').text(data.hour_pillar);
                
                $('#cycle-direction').text(data.cycle_direction);
                $('#first-cycle-age').text(data.first_cycle_age);
                $('#days-to-term').text(data.days_to_term + ' days');
                $('#solar-term').text(data.solar_term);
                
                $('#luck-cycles-body').empty();
                if (data.luck_cycles && data.luck_cycles.length > 0) {
                    $.each(data.luck_cycles, function(index, cycle) {
                        var row = $('<tr>');
                        row.append($('<td>').text('Cycle ' + cycle.cycle));
                        row.append($('<td>').text(cycle.age + ' years'));
                        row.append($('<td>').text(cycle.year));
                        row.append($('<td>').text(cycle.pillar));
                        $('#luck-cycles-body').append(row);
                    });
                }
                
                $('#verification-url').attr('href', data.verification_url);
                $('#bazi-results').show();
                
                $('html, body').animate({
                    scrollTop: $('#bazi-results').offset().top - 20
                }, 500);
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    public function ajax_calculate_bazi() {
        $birth_year = intval($_POST['birth_year']);
        $birth_month = intval($_POST['birth_month']);
        $birth_day = intval($_POST['birth_day']);
        $birth_hour = intval($_POST['birth_hour']);
        $birth_minute = intval($_POST['birth_minute']);
        $timezone = intval($_POST['timezone']);
        $gender = sanitize_text_field($_POST['gender']);
        $longitude = isset($_POST['longitude']) && !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
        
        try {
            // Calculate local solar time with longitude correction - только для Month Pillar и солнечной долготы
            $solar_time_data = $this->calculate_local_solar_time(
                $birth_year, $birth_month, $birth_day, $birth_hour, $birth_minute,
                $timezone, $longitude
            );
            
            // Determine longitude display text
            if ($longitude !== null) {
                $longitude_display = $longitude . '° (user-provided)';
            } else {
                $calculated_longitude = $timezone * 15;
                $longitude_display = $calculated_longitude . '° (auto from timezone)';
            }
            
            // Get solar longitude for Month Pillar calculation
            $solar_longitude = $this->calculate_solar_longitude(
                $solar_time_data['year'],
                $solar_time_data['month'],
                $solar_time_data['day'],
                $solar_time_data['hour'],
                $solar_time_data['minute']
            );
            
            // Get zodiac month based on solar longitude
            $zodiac_month_index = $this->get_zodiac_month_by_longitude($solar_longitude);
            
            // 1. Calculate Bazi Year using ORIGINAL birth date (not solar corrected)
            $bazi_year = $this->calculate_bazi_year($birth_year, $birth_month, $birth_day);
            
            // 2. Calculate Four Pillars
            // Year Pillar: based on Bazi year
            $year_pillar = $this->calculate_correct_year_pillar($bazi_year);
            
            // Month Pillar: based on solar longitude (uses solar corrected date)
            $month_pillar = $this->calculate_month_pillar_by_longitude(
                $bazi_year, $solar_longitude, $year_pillar
            );
            
            // Day Pillar: based on ORIGINAL birth date (local time), NOT solar corrected
            $day_pillar = $this->calculate_day_pillar($birth_year, $birth_month, $birth_day);
            
            // Hour Pillar: based on ORIGINAL birth hour and Day Pillar
            $hour_pillar = $this->calculate_hour_pillar($birth_hour, $day_pillar);
            
            // 3. Calculate Luck Cycles (10 cycles = 100 years)
            $luck_cycles_data = $this->calculate_complete_luck_cycles(
                $year_pillar, 
                $month_pillar, 
                $birth_year, $birth_month, $birth_day,
                $zodiac_month_index,
                $gender
            );
            
            // 4. Create verification URL
            $verification_url = $this->create_verification_url(
                $birth_year, $birth_month, $birth_day, $birth_hour, $birth_minute, $timezone, $gender
            );
            
            $response = array(
                'success' => true,
                'data' => array(
                    'birth_date' => sprintf('%04d-%02d-%02d %02d:%02d', $birth_year, $birth_month, $birth_day, $birth_hour, $birth_minute),
                    'gender' => $gender == 'male' ? 'Male' : 'Female',
                    'timezone' => $timezone,
                    'longitude_display' => $longitude_display,
                    'local_solar_time' => $solar_time_data['display'],
                    'solar_longitude' => $solar_longitude,
                    'zodiac_month' => $this->earthly_branches[$zodiac_month_index],
                    'year_pillar' => $year_pillar,
                    'month_pillar' => $month_pillar,
                    'day_pillar' => $day_pillar,
                    'hour_pillar' => $hour_pillar,
                    'cycle_direction' => $luck_cycles_data['direction'],
                    'first_cycle_age' => $luck_cycles_data['first_cycle_age'],
                    'days_to_term' => $luck_cycles_data['days_to_term'],
                    'solar_term' => $luck_cycles_data['solar_term'],
                    'luck_cycles' => $luck_cycles_data['cycles'],
                    'verification_url' => $verification_url
                )
            );
            
        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'data' => $e->getMessage()
            );
        }
        
        wp_send_json($response);
    }
    
    /**
     * Calculate local solar time with longitude correction (for Month Pillar only)
     */
    private function calculate_local_solar_time($year, $month, $day, $hour, $minute, $timezone, $longitude) {
        // Use mktime to create timestamp (returns integer)
        $local_timestamp = mktime($hour, $minute, 0, $month, $day, $year);
        
        // Если долгота не указана, используем среднюю долготу часового пояса
        // Каждый часовой пояс имеет ширину 15°, средняя долгота = timezone * 15
        // GMT-8 = -120°, GMT-6 = -90°, GMT+8 = 120°, и т.д.
        if ($longitude === null) {
            $longitude = $timezone * 15;
            $longitude_source = 'auto (timezone-based)';
        } else {
            $longitude_source = 'user-provided';
        }
        
        // Convert to UTC (timezone is in hours)
        $utc_timestamp = $local_timestamp - ($timezone * 3600);
        
        // Calculate day of year (1-366)
        $day_of_year = date('z', $local_timestamp) + 1;
        
        // Calculate equation of time (simplified formula) - in minutes
        $B = deg2rad((360/365) * ($day_of_year - 81));
        $equation_of_time_minutes = 9.87 * sin(2*$B) - 7.53 * cos($B) - 1.5 * sin($B);
        
        // Longitude correction (1 degree = 4 minutes)
        $longitude_correction_minutes = ($longitude - ($timezone * 15)) * 4;
        
        // Total time correction in minutes
        $total_correction_minutes = $equation_of_time_minutes + $longitude_correction_minutes;
        
        // Convert correction to seconds and round to nearest integer
        $correction_seconds = (int)round($total_correction_minutes * 60);
        
        // Apply correction to UTC timestamp - получаем целое число
        $solar_timestamp = $utc_timestamp + $correction_seconds;
        
        // Get date components from corrected timestamp
        $solar_time = getdate($solar_timestamp);
        
        return array(
            'year' => $solar_time['year'],
            'month' => $solar_time['mon'],
            'day' => $solar_time['mday'],
            'hour' => $solar_time['hours'],
            'minute' => $solar_time['minutes'],
            'display' => sprintf('%04d-%02d-%02d %02d:%02d', 
                $solar_time['year'], $solar_time['mon'], $solar_time['mday'],
                $solar_time['hours'], $solar_time['minutes']
            ),
            'used_longitude' => $longitude,
            'longitude_source' => $longitude_source
        );
    }
    
    /**
     * Calculate solar longitude (simplified calculation)
     */
    private function calculate_solar_longitude($year, $month, $day, $hour, $minute) {
        // Calculate Julian Day Number (simplified)
        $a = floor((14 - $month) / 12);
        $y = $year + 4800 - $a;
        $m = $month + 12 * $a - 3;
        
        $jd = $day + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - floor($y / 100) + floor($y / 400) - 32045;
        
        // Add fractional day
        $jd += ($hour - 12) / 24 + $minute / 1440;
        
        // Calculate days since J2000.0
        $d = $jd - 2451545.0;
        
        // Mean anomaly (degrees)
        $g = 357.529 + 0.98560028 * $d;
        $g = fmod($g, 360);
        if ($g < 0) $g += 360;
        
        // Equation of center
        $c = 1.914 * sin(deg2rad($g)) + 0.020 * sin(deg2rad(2 * $g));
        
        // True longitude
        $L = 280.459 + 0.98564736 * $d;
        $L = fmod($L + $c, 360);
        if ($L < 0) $L += 360;
        
        // Adjust to Chinese system (0° at 315°)
        $chinese_longitude = $L + 45;
        $chinese_longitude = fmod($chinese_longitude, 360);
        if ($chinese_longitude < 0) $chinese_longitude += 360;
        
        return $chinese_longitude;
    }
    
    /**
     * Get zodiac month by solar longitude
     */
    private function get_zodiac_month_by_longitude($longitude) {
        if ($longitude >= 285 && $longitude < 315) return 1;   // Rat
        if ($longitude >= 315 && $longitude < 345) return 2;   // Ox
        if ($longitude >= 345 || $longitude < 15) return 3;    // Tiger
        if ($longitude >= 15 && $longitude < 45) return 4;     // Rabbit
        if ($longitude >= 45 && $longitude < 75) return 5;     // Dragon
        if ($longitude >= 75 && $longitude < 105) return 6;    // Snake
        if ($longitude >= 105 && $longitude < 135) return 7;   // Horse
        if ($longitude >= 135 && $longitude < 165) return 8;   // Goat
        if ($longitude >= 165 && $longitude < 195) return 9;   // Monkey
        if ($longitude >= 195 && $longitude < 225) return 10;  // Rooster
        if ($longitude >= 225 && $longitude < 255) return 11;  // Dog
        return 12; // Pig (255-285)
    }
    
    // CORRECTED Four Pillars Calculation Methods
    
    private function calculate_bazi_year($year, $month, $day) {
        // Chinese astrological year starts at LiChun (February 4th)
        if ($month < 2 || ($month == 2 && $day < 4)) {
            return $year - 1;
        }
        return $year;
    }
    
    private function calculate_correct_year_pillar($year) {
        // CORRECTED FORMULA according to Master Tsai AI Model:
        // Year Stem Index = (Year − 4) mod 10 + 1
        // Year Branch Index = (Year − 4) mod 12 + 1
        
        // Calculate stem index (1-10)
        $year_stem_index = (($year - 4) % 10) + 1;
        if ($year_stem_index > 10) $year_stem_index = 1;
        
        // Calculate branch index (1-12)
        $year_branch_index = (($year - 4) % 12) + 1;
        if ($year_branch_index > 12) $year_branch_index = 1;
        
        return $this->heavenly_stems[$year_stem_index] . ' ' . $this->earthly_branches[$year_branch_index];
    }
    
    private function calculate_month_pillar_by_longitude($year, $solar_longitude, $year_pillar) {
        $zodiac_month_index = $this->get_zodiac_month_by_longitude($solar_longitude);
        
        $year_stem_name = explode(' ', $year_pillar)[0];
        $tiger_index = $this->tiger_index_table[$year_stem_name];
        
        // Formula: Month Stem Index = Zodiac Month Index - 3 + Tiger Index Number
        $month_stem_index = $zodiac_month_index - 3 + $tiger_index;
        
        // Adjust if result < 1
        if ($month_stem_index < 1) {
            $month_stem_index += 10;
        }
        
        // If > 10, take modulo 10
        $month_stem_index = $month_stem_index % 10;
        if ($month_stem_index == 0) {
            $month_stem_index = 10;
        }
        
        return $this->heavenly_stems[$month_stem_index] . ' ' . $this->earthly_branches[$zodiac_month_index];
    }
    
    private function get_zodiac_month_by_date($month, $day) {
        $date_val = $month * 100 + $day;
        
        if ($date_val >= 107 && $date_val <= 203) return 2;   // Ox
        elseif ($date_val >= 204 && $date_val <= 305) return 3;  // Tiger
        elseif ($date_val >= 306 && $date_val <= 404) return 4;  // Rabbit
        elseif ($date_val >= 405 && $date_val <= 505) return 5;  // Dragon
        elseif ($date_val >= 506 && $date_val <= 605) return 6;  // Snake
        elseif ($date_val >= 606 && $date_val <= 706) return 7;  // Horse
        elseif ($date_val >= 707 && $date_val <= 807) return 8;  // Goat
        elseif ($date_val >= 808 && $date_val <= 907) return 9;  // Monkey
        elseif ($date_val >= 908 && $date_val <= 1007) return 10; // Rooster
        elseif ($date_val >= 1008 && $date_val <= 1107) return 11; // Dog
        elseif ($date_val >= 1108 && $date_val <= 1206) return 12; // Pig
        else return 1; // Rat
    }
    
    private function calculate_day_pillar($year, $month, $day) {
        $total_days = ($year - 1900) * 365;
        $leap_year_count = floor(($year - 1901) / 4);
        $total_days += $leap_year_count;
        
        $month_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        if ($this->is_leap_year($year)) {
            $month_days[1] = 29;
        }
        for ($m = 1; $m < $month; $m++) {
            $total_days += $month_days[$m - 1];
        }
        $total_days += $day;
        
        // Formula: Index = (Total Days + 10) mod 60
        $index = ($total_days + 10) % 60;
        if ($index == 0) $index = 60;
        
        $stem_index = $index % 10;
        if ($stem_index == 0) $stem_index = 10;
        
        $branch_index = $index % 12;
        if ($branch_index == 0) $branch_index = 12;
        
        return $this->heavenly_stems[$stem_index] . ' ' . $this->earthly_branches[$branch_index];
    }
    
    private function calculate_hour_pillar($hour, $day_pillar) {
        // Determine hour branch
        if ($hour == 23 || $hour == 0) $hour_branch = 1;
        elseif ($hour >= 1 && $hour <= 2) $hour_branch = 2;
        elseif ($hour >= 3 && $hour <= 4) $hour_branch = 3;
        elseif ($hour >= 5 && $hour <= 6) $hour_branch = 4;
        elseif ($hour >= 7 && $hour <= 8) $hour_branch = 5;
        elseif ($hour >= 9 && $hour <= 10) $hour_branch = 6;
        elseif ($hour >= 11 && $hour <= 12) $hour_branch = 7;
        elseif ($hour >= 13 && $hour <= 14) $hour_branch = 8;
        elseif ($hour >= 15 && $hour <= 16) $hour_branch = 9;
        elseif ($hour >= 17 && $hour <= 18) $hour_branch = 10;
        elseif ($hour >= 19 && $hour <= 20) $hour_branch = 11;
        else $hour_branch = 12;
        
        $day_stem_name = explode(' ', $day_pillar)[0];
        $day_stem_index = array_search($day_stem_name, $this->heavenly_stems);
        
        // Calculate hour stem
        if ($hour == 23) {
            // Late Rat special case
            $hour_stem_index = (($day_stem_index * 2 - 1) + ($hour_branch - 1) + 12) % 10;
        } else {
            $hour_stem_index = (($day_stem_index * 2 - 1) + ($hour_branch - 1)) % 10;
        }
        
        if ($hour_stem_index == 0) $hour_stem_index = 10;
        
        return $this->heavenly_stems[$hour_stem_index] . ' ' . $this->earthly_branches[$hour_branch];
    }
    
    // Complete Luck Cycles Calculation (10 cycles = 100 years)
    private function calculate_complete_luck_cycles($year_pillar, $month_pillar, $birth_year, $birth_month, $birth_day, $zodiac_month_index, $gender) {
        // 1. Determine cycle direction
        $year_stem_name = explode(' ', $year_pillar)[0];
        $year_stem_index = array_search($year_stem_name, $this->heavenly_stems);
        
        $is_yang = ($year_stem_index % 2 == 1);
        $is_male = ($gender == 'male');
        
        // Master Tsai AI Model rules:
        // Yang Male / Yin Female = Forward
        // Yang Female / Yin Male = Backward
        if (($is_yang && $is_male) || (!$is_yang && !$is_male)) {
            $direction = 'Forward (Yang Male / Yin Female)';
            $is_forward = true;
        } else {
            $direction = 'Backward (Yang Female / Yin Male)';
            $is_forward = false;
        }
        
        // 2. Find month pillar index in 60-year cycle
        $month_index = $this->find_pillar_index($month_pillar);
        
        // 3. Determine first luck pillar (not the month pillar itself)
        if ($is_forward) {
            $first_index = $month_index + 1;
            if ($first_index > 60) $first_index = 1;
        } else {
            $first_index = $month_index - 1;
            if ($first_index < 1) $first_index = 60;
        }
        
        // 4. Calculate days to solar term
        $days_info = $this->calculate_days_to_solar_term(
            $birth_year, $birth_month, $birth_day,
            $zodiac_month_index, $is_forward
        );
        
        $days_to_term = $days_info['days'];
        $solar_term = $days_info['term'];
        
        // 5. Convert days to starting age (3 days = 1 year)
        $starting_age_years = floor($days_to_term / 3);
        $remaining_days = $days_to_term % 3;
        
        $age_display = $starting_age_years . ' years';
        if ($remaining_days == 2) {
            $age_display .= ' 8 months';
        } elseif ($remaining_days == 1) {
            $age_display .= ' 4 months';
        }
        
        // 6. Build 10 cycles (100 years)
        $cycles = [];
        $current_age = $starting_age_years;
        $current_year = $birth_year + $starting_age_years;
        $current_index = $first_index;
        
        for ($i = 1; $i <= 10; $i++) {
            $cycles[] = [
                'cycle' => $i,
                'age' => $current_age,
                'year' => $current_year,
                'pillar' => $this->stem_branch_cycle[$current_index]
            ];
            
            $current_age += 10;
            $current_year += 10;
            
            // Move through the 60-year cycle
            if ($is_forward) {
                $current_index++;
                if ($current_index > 60) $current_index = 1;
            } else {
                $current_index--;
                if ($current_index < 1) $current_index = 60;
            }
        }
        
        return [
            'direction' => $direction,
            'first_cycle_age' => $age_display,
            'days_to_term' => $days_to_term,
            'solar_term' => $solar_term,
            'cycles' => $cycles
        ];
    }
    
    private function find_pillar_index($pillar) {
        $search = trim($pillar);
        foreach ($this->stem_branch_cycle as $index => $value) {
            if (strcasecmp(trim($value), $search) == 0) {
                return $index;
            }
        }
        
        $parts = explode(' ', $search);
        if (count($parts) >= 2) {
            $stem = $parts[0];
            $branch = $parts[1];
            
            foreach ($this->stem_branch_cycle as $index => $value) {
                $value_parts = explode(' ', $value);
                if (count($value_parts) >= 2) {
                    if (strcasecmp($stem, $value_parts[0]) == 0 && 
                        strcasecmp($branch, $value_parts[1]) == 0) {
                        return $index;
                    }
                }
            }
        }
        
        return 13; // Default: Yang-Fire Rat
    }
    
    private function calculate_days_to_solar_term($birth_year, $birth_month, $birth_day, $zodiac_month_index, $is_forward) {
        if (!isset($this->zodiac_month_terms[$zodiac_month_index])) {
            return array('days' => 0, 'term' => 'Unknown');
        }
        
        $term = $this->zodiac_month_terms[$zodiac_month_index];
        $term_month = $term['month'];
        $term_day = $term['day'];
        $term_name = $term['name'];
        
        $birth_timestamp = strtotime("$birth_year-$birth_month-$birth_day");
        
        if ($is_forward) {
            // Для вперед: считаем дни до следующего термина в этом же году
            $term_timestamp = strtotime("$birth_year-$term_month-$term_day");
            
            // Если термин уже прошел в этом году, берем следующий год
            if ($term_timestamp <= $birth_timestamp) {
                $term_timestamp = strtotime(($birth_year + 1) . "-$term_month-$term_day");
            }
            
            $days = ($term_timestamp - $birth_timestamp) / (60 * 60 * 24);
        } else {
            // Для назад: считаем дни ДО ТЕКУЩЕГО ТЕРМИНА В ЭТОМ ЖЕ ГОДУ
            // То есть до термина, который уже наступил в этом году и начал текущий месяц
            $term_timestamp = strtotime("$birth_year-$term_month-$term_day");
            
            // Если термин еще не наступил в этом году, берем ПРЕДЫДУЩИЙ ТЕРМИН ЭТОГО ЖЕ ТИПА
            // Но в текущем году! Нужно найти предыдущий месяц по солнечному календарю
            
            // Определяем индекс предыдущего месяца
            $prev_month_index = $zodiac_month_index - 1;
            if ($prev_month_index < 1) {
                $prev_month_index = 12;
            }
            
            // Берем термин предыдущего месяца
            $prev_term = $this->zodiac_month_terms[$prev_month_index];
            $prev_term_month = $prev_term['month'];
            $prev_term_day = $prev_term['day'];
            $prev_term_name = $prev_term['name'];
            
            // Создаем timestamp для предыдущего термина В ТЕКУЩЕМ ГОДУ
            $prev_term_timestamp = strtotime("$birth_year-$prev_term_month-$prev_term_day");
            
            // Если текущий термин уже прошел, используем его
            // Если текущий термин еще не наступил, используем предыдущий термин
            if ($term_timestamp <= $birth_timestamp) {
                // Текущий термин уже прошел - используем его
                $days = ($birth_timestamp - $term_timestamp) / (60 * 60 * 24);
                $term_name = $term_name; // текущий термин
            } else {
                // Текущий термин еще не наступил - используем предыдущий
                $days = ($birth_timestamp - $prev_term_timestamp) / (60 * 60 * 24);
                $term_name = $prev_term_name; // предыдущий термин
                
                // Проверяем, не получилась ли отрицательная разница
                // (если день рождения раньше предыдущего термина в этом году)
                if ($days < 0) {
                    // Тогда берем предыдущий термин из предыдущего года
                    $prev_term_timestamp = strtotime(($birth_year - 1) . "-$prev_term_month-$prev_term_day");
                    $days = ($birth_timestamp - $prev_term_timestamp) / (60 * 60 * 24);
                }
            }
        }
        
        return array(
            'days' => round($days),
            'term' => $term_name
        );
    }
    
    private function create_verification_url($year, $month, $day, $hour, $minute, $timezone, $gender) {
        $gender_code = ($gender == 'male') ? 'V3' : 'V4';
        
        return 'https://www.chinesefortunecalendar.com/TDB/ChineseAstrologyChk.asp?' . 
               'AstroCalendar=V1&' .
               'TimeZone=' . $timezone . '&' .
               'SunYear=' . $year . '&' .
               'SunMonth=' . $month . '&' .
               'SunDay=' . $day . '&' .
               'SunHour=' . $hour . '&' .
               'SunMin=' . $minute . '&' .
               'Gender=' . $gender_code;
    }
    
    private function is_leap_year($year) {
        return (($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0);
    }
}

new MasterTsaiBaziCalculatorComplete();
