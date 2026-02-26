<?php
/**
 * Bazi Calculator Class
 * 
 * @package Bazi_Calculator
 * @since 25.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bazi_Calculator {
    
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
    
    private $zodiac_month_terms = array(
        1 => array('name' => 'Daxue', 'month' => 12, 'day' => 7, 'longitude' => 255),
        2 => array('name' => 'Xiaohan', 'month' => 1, 'day' => 6, 'longitude' => 285),
        3 => array('name' => 'Lichun', 'month' => 2, 'day' => 4, 'longitude' => 315),
        4 => array('name' => 'Jingzhe', 'month' => 3, 'day' => 6, 'longitude' => 345),
        5 => array('name' => 'Qingming', 'month' => 4, 'day' => 5, 'longitude' => 15),
        6 => array('name' => 'Lixia', 'month' => 5, 'day' => 6, 'longitude' => 45),
        7 => array('name' => 'Mangzhong', 'month' => 6, 'day' => 6, 'longitude' => 75),
        8 => array('name' => 'Xiaoshu', 'month' => 7, 'day' => 7, 'longitude' => 105),
        9 => array('name' => 'Liqiu', 'month' => 8, 'day' => 8, 'longitude' => 135),
        10 => array('name' => 'Bailu', 'month' => 9, 'day' => 8, 'longitude' => 165),
        11 => array('name' => 'Hanlu', 'month' => 10, 'day' => 8, 'longitude' => 195),
        12 => array('name' => 'Lidong', 'month' => 11, 'day' => 7, 'longitude' => 225)
    );
    
    private $solar_term_cache = array();
    
    public function __construct() {
        add_shortcode('bazi_calculator', array($this, 'render_calculator'));
        add_action('wp_ajax_calculate_bazi', array($this, 'ajax_calculate_bazi'));
        add_action('wp_ajax_nopriv_calculate_bazi', array($this, 'ajax_calculate_bazi'));
    }
    
    public function render_calculator($atts) {
        $atts = shortcode_atts(array(), $atts, 'bazi_calculator');
        
        ob_start();
        ?>
        <div class="bazi-calculator-container">
            
            <form id="bazi-form" class="bazi-form">
                <?php wp_nonce_field('bazi_calculator_nonce', 'bazi_nonce'); ?>
                
                <div class="form-section">
                    <h3><?php _e('Birth Information', 'bazi-calculator'); ?></h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="birth_year"><?php _e('Birth Year', 'bazi-calculator'); ?> *</label>
                            <input type="number" id="birth_year" name="birth_year" min="1900" max="2100" placeholder="<?php esc_attr_e('e.g., 1990', 'bazi-calculator'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="birth_month"><?php _e('Birth Month', 'bazi-calculator'); ?> *</label>
                            <select id="birth_month" name="birth_month" required>
                                <option value="">-- <?php _e('Select Month', 'bazi-calculator'); ?> --</option>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>">
                                        <?php echo date_i18n('F', mktime(0,0,0,$i,1)); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="birth_day"><?php _e('Birth Day', 'bazi-calculator'); ?> *</label>
                            <input type="number" id="birth_day" name="birth_day" min="1" max="31" placeholder="1-31" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="birth_hour"><?php _e('Birth Hour (24h)', 'bazi-calculator'); ?> *</label>
                            <input type="number" id="birth_hour" name="birth_hour" min="0" max="23" placeholder="0-23" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="birth_minute"><?php _e('Birth Minute', 'bazi-calculator'); ?> *</label>
                            <input type="number" id="birth_minute" name="birth_minute" min="0" max="59" placeholder="0-59" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="timezone"><?php _e('Timezone (GMT)', 'bazi-calculator'); ?> *</label>
                            <select id="timezone" name="timezone" required>
                                <option value="">-- <?php _e('Select Timezone', 'bazi-calculator'); ?> --</option>
                                <?php for($i = -12; $i <= 14; $i++): ?>
                                    <option value="<?php echo $i; ?>">
                                        GMT<?php echo $i >= 0 ? '+' . $i : $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender"><?php _e('Gender', 'bazi-calculator'); ?> *</label>
                            <select id="gender" name="gender" required>
                                <option value="">-- <?php _e('Select Gender', 'bazi-calculator'); ?> --</option>
                                <option value="male"><?php _e('Male', 'bazi-calculator'); ?></option>
                                <option value="female"><?php _e('Female', 'bazi-calculator'); ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="longitude"><?php _e('Longitude (for precise results)', 'bazi-calculator'); ?></label>
                            <div class="longitude-input-group">
                                <input type="text" id="longitude" name="longitude" placeholder="<?php esc_attr_e('e.g., -118.24 or 121.47', 'bazi-calculator'); ?>">
                                <button type="button" id="get-location-btn" class="location-btn" title="<?php esc_attr_e('Get my current location', 'bazi-calculator'); ?>">📍</button>
                            </div>
                            <small><?php _e('For accurate results, enter your birthplace longitude.', 'bazi-calculator'); ?> <a href="https://www.latlong.net/" target="_blank" rel="noopener"><?php _e('Find your coordinates here ↗', 'bazi-calculator'); ?></a></small>
                            <div id="location-status" class="location-status"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="bazi-button calculate-btn"><?php _e('Calculate Bazi Chart', 'bazi-calculator'); ?></button>
                    <button type="reset" class="bazi-button reset-btn"><?php _e('Reset Form', 'bazi-calculator'); ?></button>
                </div>
            </form>
            
            <div id="bazi-results" class="bazi-results" style="display: none;">
                <div class="results-header">
                    <h3><?php _e('YOUR BAZI CHART RESULTS', 'bazi-calculator'); ?></h3>
                </div>
                
                <div class="personal-info">
                    <p><strong><?php _e('Birth Date:', 'bazi-calculator'); ?></strong> <span id="result-birthdate"></span></p>
                    <p><strong><?php _e('Gender:', 'bazi-calculator'); ?></strong> <span id="result-gender"></span></p>
                    <p><strong><?php _e('Timezone:', 'bazi-calculator'); ?></strong> <span id="result-timezone"></span></p>
                    <p><strong><?php _e('Longitude:', 'bazi-calculator'); ?></strong> <span id="result-longitude"></span></p>
                    <p><strong><?php _e('Local Solar Time:', 'bazi-calculator'); ?></strong> <span id="result-solar-time"></span></p>
                    <p><strong><?php _e('Solar Longitude:', 'bazi-calculator'); ?></strong> <span id="result-solar-longitude"></span>°</p>
                    <p><strong><?php _e('Zodiac Month:', 'bazi-calculator'); ?></strong> <span id="result-zodiac-month"></span></p>
                </div>
                
                <div class="four-pillars">
                    <h4><?php _e('FOUR PILLARS OF DESTINY', 'bazi-calculator'); ?></h4>
                    <div class="pillars-grid">
                        <div class="pillar">
                            <div class="pillar-label"><?php _e('Year Pillar', 'bazi-calculator'); ?></div>
                            <div class="pillar-value" id="year-pillar">-</div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-label"><?php _e('Month Pillar', 'bazi-calculator'); ?></div>
                            <div class="pillar-value" id="month-pillar">-</div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-label"><?php _e('Day Pillar', 'bazi-calculator'); ?></div>
                            <div class="pillar-value" id="day-pillar">-</div>
                        </div>
                        <div class="pillar">
                            <div class="pillar-label"><?php _e('Hour Pillar', 'bazi-calculator'); ?></div>
                            <div class="pillar-value" id="hour-pillar">-</div>
                        </div>
                    </div>
                </div>
                
                <div class="luck-cycles-section">
                    <h4><?php _e('TEN-YEAR MAJOR LUCK CYCLES', 'bazi-calculator'); ?></h4>
                    <div class="luck-cycles-info">
                        <p><strong><?php _e('Direction:', 'bazi-calculator'); ?></strong> <span id="cycle-direction"></span></p>
                        <p><strong><?php _e('First Cycle Starts at Age:', 'bazi-calculator'); ?></strong> <span id="first-cycle-age"></span></p>
                        <p><strong><?php _e('Days to Solar Term:', 'bazi-calculator'); ?></strong> <span id="days-to-term"></span></p>
                        <p><strong><?php _e('Solar Term Used:', 'bazi-calculator'); ?></strong> <span id="solar-term"></span></p>
                    </div>
                    
                    <div class="cycles-table-container">
                        <table class="cycles-table">
                            <thead>
                                <tr>
                                    <th><?php _e('Cycle', 'bazi-calculator'); ?></th>
                                    <th><?php _e('Age', 'bazi-calculator'); ?></th>
                                    <th><?php _e('Year', 'bazi-calculator'); ?></th>
                                    <th><?php _e('Luck Pillar', 'bazi-calculator'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="luck-cycles-body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div id="bazi-loading" class="bazi-loading" style="display: none;">
                <div class="loading-spinner"></div>
                <p><?php _e('Calculating Bazi chart with solar time correction...', 'bazi-calculator'); ?></p>
            </div>
            
            <div id="bazi-error" class="bazi-error" style="display: none;">
                <p><?php _e('Error in calculation. Please check your inputs and try again.', 'bazi-calculator'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function ajax_calculate_bazi() {
        if (!isset($_POST['bazi_nonce']) || !wp_verify_nonce($_POST['bazi_nonce'], 'bazi_calculator_nonce')) {
            wp_send_json(array('success' => false, 'data' => __('Security check failed.', 'bazi-calculator')));
            return;
        }
        
        $birth_year = intval($_POST['birth_year']);
        $birth_month = intval($_POST['birth_month']);
        $birth_day = intval($_POST['birth_day']);
        $birth_hour = intval($_POST['birth_hour']);
        $birth_minute = intval($_POST['birth_minute']);
        $timezone = intval($_POST['timezone']);
        $gender = sanitize_text_field($_POST['gender']);
        $longitude = isset($_POST['longitude']) && !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
        
        try {
            $solar_time_data = $this->calculate_local_solar_time(
                $birth_year, $birth_month, $birth_day, $birth_hour, $birth_minute,
                $timezone, $longitude
            );
            
            if ($longitude !== null) {
                $longitude_display = $longitude . '° ' . __('(user-provided)', 'bazi-calculator');
            } else {
                $calculated_longitude = $timezone * 15;
                $longitude_display = $calculated_longitude . '° ' . __('(auto from timezone)', 'bazi-calculator');
            }
            
            $solar_longitude = $this->calculate_solar_longitude(
                $solar_time_data['year'],
                $solar_time_data['month'],
                $solar_time_data['day'],
                $solar_time_data['hour'],
                $solar_time_data['minute']
            );
            
            $zodiac_month_index = $this->get_zodiac_month_by_longitude($solar_longitude);
            $bazi_year = $this->calculate_bazi_year($birth_year, $birth_month, $birth_day);
            
            $year_pillar = $this->calculate_correct_year_pillar($bazi_year);
            $month_pillar = $this->calculate_month_pillar_by_longitude($bazi_year, $solar_longitude, $year_pillar);
            $day_pillar = $this->calculate_day_pillar($birth_year, $birth_month, $birth_day);
            $hour_pillar = $this->calculate_hour_pillar($birth_hour, $day_pillar);
            
            $luck_cycles_data = $this->calculate_complete_luck_cycles(
                $year_pillar, $month_pillar, 
                $birth_year, $birth_month, $birth_day,
                $birth_hour, $birth_minute,
                $zodiac_month_index, $gender
            );
            
            $verification_url = $this->create_verification_url(
                $birth_year, $birth_month, $birth_day, $birth_hour, $birth_minute, $timezone, $gender
            );
            
            $response = array(
                'success' => true,
                'data' => array(
                    'birth_date' => sprintf('%04d-%02d-%02d %02d:%02d', $birth_year, $birth_month, $birth_day, $birth_hour, $birth_minute),
                    'gender' => $gender == 'male' ? __('Male', 'bazi-calculator') : __('Female', 'bazi-calculator'),
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
    
    private function calculate_local_solar_time($year, $month, $day, $hour, $minute, $timezone, $longitude) {
        $local_timestamp = mktime($hour, $minute, 0, $month, $day, $year);
        
        if ($longitude === null) {
            $longitude = $timezone * 15;
        }
        
        $utc_timestamp = $local_timestamp - ($timezone * 3600);
        $day_of_year = date('z', $local_timestamp) + 1;
        
        $B = deg2rad((360/365) * ($day_of_year - 81));
        $equation_of_time_minutes = 9.87 * sin(2*$B) - 7.53 * cos($B) - 1.5 * sin($B);
        
        $longitude_correction_minutes = ($longitude - ($timezone * 15)) * 4;
        $total_correction_minutes = $equation_of_time_minutes + $longitude_correction_minutes;
        $correction_seconds = (int)round($total_correction_minutes * 60);
        $solar_timestamp = $utc_timestamp + $correction_seconds;
        
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
            )
        );
    }
    
    private function calculate_solar_longitude($year, $month, $day, $hour, $minute) {
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
    
    private function get_zodiac_month_by_longitude($longitude) {
        if ($longitude >= 255 && $longitude < 285) return 1;
        if ($longitude >= 285 && $longitude < 315) return 2;
        if ($longitude >= 315 && $longitude < 345) return 3;
        if ($longitude >= 345 || $longitude < 15) return 4;
        if ($longitude >= 15 && $longitude < 45) return 5;
        if ($longitude >= 45 && $longitude < 75) return 6;
        if ($longitude >= 75 && $longitude < 105) return 7;
        if ($longitude >= 105 && $longitude < 135) return 8;
        if ($longitude >= 135 && $longitude < 165) return 9;
        if ($longitude >= 165 && $longitude < 195) return 10;
        if ($longitude >= 195 && $longitude < 225) return 11;
        return 12;
    }
    
    private function calculate_bazi_year($year, $month, $day) {
        if ($month < 2 || ($month == 2 && $day < 4)) {
            return $year - 1;
        }
        return $year;
    }
    
    private function calculate_correct_year_pillar($year) {
        $year_stem_index = (($year - 4) % 10) + 1;
        if ($year_stem_index > 10) $year_stem_index = 1;
        
        $year_branch_index = (($year - 4) % 12) + 1;
        if ($year_branch_index > 12) $year_branch_index = 1;
        
        return $this->heavenly_stems[$year_stem_index] . ' ' . $this->earthly_branches[$year_branch_index];
    }
    
    private function calculate_month_pillar_by_longitude($year, $solar_longitude, $year_pillar) {
        $zodiac_month_index = $this->get_zodiac_month_by_longitude($solar_longitude);
        
        $year_stem_name = explode(' ', $year_pillar)[0];
        $year_stem_index = array_search($year_stem_name, $this->heavenly_stems);
        
        $base = ($year_stem_index * 2 - 1) + ($zodiac_month_index - 1);
        
        if ($zodiac_month_index == 1 || $zodiac_month_index == 2) {
            $base += 12;
        }
        
        $month_stem_index = $base % 10;
        if ($month_stem_index == 0) {
            $month_stem_index = 10;
        }
        
        return $this->heavenly_stems[$month_stem_index] . ' ' . $this->earthly_branches[$zodiac_month_index];
    }
    
    private function calculate_day_pillar($year, $month, $day) {
        $total_days = ($year - 1900) * 365;
        
        if ($year > 1900) {
            $leap_year_count = floor(($year - 1) / 4) - floor(($year - 1) / 100) + floor(($year - 1) / 400);
            $leap_year_count -= floor(1899 / 4) - floor(1899 / 100) + floor(1899 / 400);
        } else {
            $leap_year_count = 0;
        }
        $total_days += $leap_year_count;
        
        $month_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        if ($this->is_leap_year($year)) {
            $month_days[1] = 29;
        }
        for ($m = 1; $m < $month; $m++) {
            $total_days += $month_days[$m - 1];
        }
        $total_days += $day;
        
        $index = ($total_days + 10) % 60;
        if ($index == 0) $index = 60;
        
        $stem_index = $index % 10;
        if ($stem_index == 0) $stem_index = 10;
        
        $branch_index = $index % 12;
        if ($branch_index == 0) $branch_index = 12;
        
        return $this->heavenly_stems[$stem_index] . ' ' . $this->earthly_branches[$branch_index];
    }
    
    private function calculate_hour_pillar($hour, $day_pillar) {
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
        
        if ($hour == 23) {
            $hour_stem_index = (($day_stem_index * 2 - 1) + ($hour_branch - 1) + 12) % 10;
        } else {
            $hour_stem_index = (($day_stem_index * 2 - 1) + ($hour_branch - 1)) % 10;
        }
        
        if ($hour_stem_index == 0) $hour_stem_index = 10;
        
        return $this->heavenly_stems[$hour_stem_index] . ' ' . $this->earthly_branches[$hour_branch];
    }
    
    private function calculate_complete_luck_cycles($year_pillar, $month_pillar, $birth_year, $birth_month, $birth_day, $birth_hour, $birth_minute, $zodiac_month_index, $gender) {
        $year_stem_name = explode(' ', $year_pillar)[0];
        $year_stem_index = array_search($year_stem_name, $this->heavenly_stems);
        
        $is_yang = ($year_stem_index % 2 == 1);
        $is_male = ($gender == 'male');
        
        if (($is_yang && $is_male) || (!$is_yang && !$is_male)) {
            $direction = __('Forward (Yang Male / Yin Female)', 'bazi-calculator');
            $is_forward = true;
        } else {
            $direction = __('Backward (Yang Female / Yin Male)', 'bazi-calculator');
            $is_forward = false;
        }
        
        $month_index = $this->find_pillar_index($month_pillar);
        
        if ($is_forward) {
            $first_index = $month_index + 1;
            if ($first_index > 60) $first_index = 1;
        } else {
            $first_index = $month_index - 1;
            if ($first_index < 1) $first_index = 60;
        }
        
        $days_info = $this->calculate_days_to_solar_term(
            $birth_year, $birth_month, $birth_day, $birth_hour, $birth_minute,
            $zodiac_month_index, $is_forward
        );
        
        $days_to_term = $days_info['days'];
        $solar_term = $days_info['term'];
        
        $starting_age_years = floor($days_to_term / 3);
        $remaining_days = $days_to_term % 3;
        
        $age_display = $starting_age_years . ' ' . __('years', 'bazi-calculator');
        if ($remaining_days == 2) {
            $age_display .= ' 8 ' . __('months', 'bazi-calculator');
        } elseif ($remaining_days == 1) {
            $age_display .= ' 4 ' . __('months', 'bazi-calculator');
        }
        
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
        
        return 13;
    }
    
    private function calculate_days_to_solar_term($birth_year, $birth_month, $birth_day, $birth_hour, $birth_minute, $zodiac_month_index, $is_forward) {
        $current_term = $this->zodiac_month_terms[$zodiac_month_index];
        $current_term_month = $current_term['month'];
        $current_term_day = $current_term['day'];
        $current_term_name = $current_term['name'];
        $current_term_longitude = $current_term['longitude'];
        
        $next_month_index = $zodiac_month_index + 1;
        if ($next_month_index > 12) $next_month_index = 1;
        $next_term = $this->zodiac_month_terms[$next_month_index];
        $next_term_month = $next_term['month'];
        $next_term_day = $next_term['day'];
        $next_term_name = $next_term['name'];
        $next_term_longitude = $next_term['longitude'];
        
        $birth_timestamp = mktime($birth_hour, $birth_minute, 0, $birth_month, $birth_day, $birth_year);
        
        if ($is_forward) {
            $next_term_year = $birth_year;
            
            if ($next_term_month < $birth_month || 
                ($next_term_month == $birth_month && $next_term_day <= $birth_day)) {
                $next_term_year = $birth_year + 1;
            }
            
            $next_term_timestamp = $this->find_solar_term_exact(
                $next_term_year, $next_term_longitude, $next_term_month, $next_term_day
            );
            
            $days = ($next_term_timestamp - $birth_timestamp) / (60 * 60 * 24);
            $term_name = $next_term_name;
            
        } else {
            $current_term_year = $birth_year;
            
            if ($current_term_month > $birth_month || 
                ($current_term_month == $birth_month && $current_term_day > $birth_day)) {
                $current_term_year = $birth_year - 1;
            }
            
            $current_term_timestamp = $this->find_solar_term_exact(
                $current_term_year, $current_term_longitude, $current_term_month, $current_term_day
            );
            
            $days = ($birth_timestamp - $current_term_timestamp) / (60 * 60 * 24);
            $term_name = $current_term_name;
        }
        
        return array(
            'days' => floor(abs($days)),
            'term' => $term_name
        );
    }
    
    private function find_solar_term_exact($year, $target_longitude, $approx_month, $approx_day) {
        $cache_key = $year . '_' . $target_longitude;
        if (isset($this->solar_term_cache[$cache_key])) {
            return $this->solar_term_cache[$cache_key];
        }
        
        $approx_timestamp = strtotime("$year-$approx_month-$approx_day 12:00:00");
        $start_timestamp = $approx_timestamp - (5 * 86400);
        $end_timestamp = $approx_timestamp + (5 * 86400);
        
        while (($end_timestamp - $start_timestamp) > 60) {
            $mid_timestamp = intval(($start_timestamp + $end_timestamp) / 2);
            $mid_date = getdate($mid_timestamp);
            
            $longitude = $this->calculate_solar_longitude(
                $mid_date['year'], $mid_date['mon'], $mid_date['mday'],
                $mid_date['hours'], $mid_date['minutes']
            );
            
            $diff = $longitude - $target_longitude;
            if ($diff > 180) $diff -= 360;
            if ($diff < -180) $diff += 360;
            
            if ($diff < 0) {
                $start_timestamp = $mid_timestamp;
            } else {
                $end_timestamp = $mid_timestamp;
            }
        }
        
        $this->solar_term_cache[$cache_key] = $end_timestamp;
        
        return $end_timestamp;
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
