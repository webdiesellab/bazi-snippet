/**
 * Bazi Calculator Frontend Scripts
 * @package Bazi_Calculator
 * @version 25.3
 */
(function($) {
    'use strict';
    
    var elementEmojis = { 'Wood': '🌲', 'Fire': '🔥', 'Earth': '🏔️', 'Metal': '⚙️', 'Water': '💧' };
    var animalEmojis = { 'Rat': '🐀', 'Ox': '🐂', 'Tiger': '🐅', 'Rabbit': '🐇', 'Dragon': '🐉', 'Snake': '🐍', 'Horse': '🐴', 'Goat': '🐐', 'Monkey': '🐒', 'Rooster': '🐓', 'Dog': '🐕', 'Pig': '🐖' };
    var elementChars = { 'Wood': '木', 'Fire': '火', 'Earth': '土', 'Metal': '金', 'Water': '水' };
    var animalChars = { 'Rat': '子', 'Ox': '丑', 'Tiger': '寅', 'Rabbit': '卯', 'Dragon': '辰', 'Snake': '巳', 'Horse': '午', 'Goat': '未', 'Monkey': '申', 'Rooster': '酉', 'Dog': '戌', 'Pig': '亥' };
    var animalImages = {
        'Rat': 'https://zodipet.com/wp-content/uploads/2025/12/rat-687x1024.png',
        'Ox': 'https://zodipet.com/wp-content/uploads/2025/12/ox.png',
        'Tiger': 'https://zodipet.com/wp-content/uploads/2025/12/tiger2.png',
        'Rabbit': 'https://zodipet.com/wp-content/uploads/2025/12/rabbit-687x1024.png',
        'Dragon': 'https://zodipet.com/wp-content/uploads/2025/12/dragon-687x1024.png',
        'Snake': 'https://zodipet.com/wp-content/uploads/2025/12/snake-687x1024.png',
        'Horse': 'https://zodipet.com/wp-content/uploads/2025/12/horse-687x1024.png',
        'Goat': 'https://zodipet.com/wp-content/uploads/2025/12/goat-687x1024.png',
        'Monkey': 'https://zodipet.com/wp-content/uploads/2025/12/monkey3-687x1024.png',
        'Rooster': 'https://zodipet.com/wp-content/uploads/2025/12/Rooster2-687x1024.png',
        'Dog': 'https://zodipet.com/wp-content/uploads/2025/12/dog2-687x1024.png',
        'Pig': 'https://zodipet.com/wp-content/uploads/2025/12/pig-687x1024.png'
    };
    
    var longitudeFromGeo = false;
    
    function getEmojisForPillar(pillar) {
        var elementEmoji = '', animalEmoji = '';
        for (var e in elementEmojis) { if (pillar.indexOf(e) !== -1) { elementEmoji = elementEmojis[e]; break; } }
        for (var a in animalEmojis) { if (pillar.indexOf(a) !== -1) { animalEmoji = animalEmojis[a]; break; } }
        return elementEmoji + animalEmoji;
    }
    
    function getChineseCharsForPillar(pillar) {
        var elementChar = '', animalChar = '';
        for (var e in elementChars) { if (pillar.indexOf(e) !== -1) { elementChar = elementChars[e]; break; } }
        for (var a in animalChars) { if (pillar.indexOf(a) !== -1) { animalChar = animalChars[a]; break; } }
        return elementChar + animalChar;
    }
    
    function getElementClass(pillar) {
        if (pillar.indexOf('Wood') !== -1) return 'element-wood';
        if (pillar.indexOf('Fire') !== -1) return 'element-fire';
        if (pillar.indexOf('Earth') !== -1) return 'element-earth';
        if (pillar.indexOf('Metal') !== -1) return 'element-metal';
        if (pillar.indexOf('Water') !== -1) return 'element-water';
        return '';
    }
    
    function getAnimalFromPillar(pillar) {
        var parts = pillar.split(' ');
        return parts.length > 1 ? parts[1] : '';
    }
    
    function updatePillar(elementId, pillarValue) {
        var $pillar = $('#' + elementId).closest('.pillar');
        var animal = getAnimalFromPillar(pillarValue);
        var elementClass = getElementClass(pillarValue);
        var chineseChars = getChineseCharsForPillar(pillarValue);
        
        $pillar.removeClass('element-wood element-fire element-earth element-metal element-water').addClass(elementClass);
        
        var $img = $pillar.find('.pillar-animal');
        if ($img.length === 0) {
            $img = $('<img class="pillar-animal" alt="">');
            $pillar.find('.pillar-label').after($img);
        }
        $img.attr('src', animalImages[animal] || '');
        
        $('#' + elementId).text(pillarValue);
        
        var $chars = $pillar.find('.pillar-chinese');
        if ($chars.length === 0) {
            $chars = $('<div class="pillar-chinese">');
            $('#' + elementId).after($chars);
        }
        $chars.text(chineseChars);
    }
    
    function displayBaziResults(data) {
        $('#result-birthdate').text(data.birth_date);
        $('#result-gender').text(data.gender);
        $('#result-timezone').text('GMT' + (data.timezone >= 0 ? '+' + data.timezone : data.timezone));
        $('#result-longitude').text(data.longitude_display);
        $('#result-solar-time').text(data.local_solar_time);
        $('#result-solar-longitude').text(data.solar_longitude.toFixed(2));
        $('#result-zodiac-month').text(data.zodiac_month);
        
        updatePillar('year-pillar', data.year_pillar);
        updatePillar('month-pillar', data.month_pillar);
        updatePillar('day-pillar', data.day_pillar);
        updatePillar('hour-pillar', data.hour_pillar);
        
        $('#cycle-direction').text(data.cycle_direction);
        $('#first-cycle-age').text(data.first_cycle_age);
        $('#days-to-term').text(data.days_to_term + ' days');
        $('#solar-term').text(data.solar_term);
        
        $('#luck-cycles-body').empty();
        if (data.luck_cycles && data.luck_cycles.length > 0) {
            $.each(data.luck_cycles, function(index, cycle) {
                var emojis = getEmojisForPillar(cycle.pillar);
                var chineseChars = getChineseCharsForPillar(cycle.pillar);
                var row = $('<tr>');
                row.append($('<td>').text('Cycle ' + cycle.cycle));
                row.append($('<td>').text(cycle.age + ' years'));
                row.append($('<td>').text(cycle.year));
                row.append($('<td>').text(cycle.pillar + ' ' + emojis + ' ' + chineseChars));
                $('#luck-cycles-body').append(row);
            });
        }
        
        $('#verification-url').attr('href', data.verification_url);
        $('#bazi-results').show();
        
        $('html, body').animate({ scrollTop: $('#bazi-results').offset().top - 20 }, 500);
    }
    
    function isValidDate(year, month, day) {
        if (year < 1900 || year > 2100) return false;
        if (month < 1 || month > 12) return false;
        if (day < 1 || day > 31) return false;
        var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        if (month === 2) {
            var isLeap = (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
            daysInMonth[1] = isLeap ? 29 : 28;
        }
        return day <= daysInMonth[month - 1];
    }
    
    function detectTimezone() {
        try {
            var offset = -(new Date().getTimezoneOffset() / 60);
            offset = Math.round(offset);
            if (offset >= -12 && offset <= 14) { $('#timezone').val(offset); }
        } catch(e) { console.log('Could not detect timezone:', e); }
    }
    
    function loadFromUrl() {
        var params = new URLSearchParams(window.location.search);
        var hasParams = false;
        if (params.has('year')) { $('#birth_year').val(params.get('year')); hasParams = true; }
        if (params.has('month')) { $('#birth_month').val(params.get('month')); hasParams = true; }
        if (params.has('day')) { $('#birth_day').val(params.get('day')); hasParams = true; }
        if (params.has('hour')) { $('#birth_hour').val(params.get('hour')); hasParams = true; }
        if (params.has('minute')) { $('#birth_minute').val(params.get('minute')); hasParams = true; }
        if (params.has('tz')) { $('#timezone').val(params.get('tz')); hasParams = true; }
        if (params.has('gender')) { $('#gender').val(params.get('gender')); hasParams = true; }
        if (params.has('lon')) { $('#longitude').val(params.get('lon')); hasParams = true; }
        return hasParams;
    }
    
    $(document).ready(function() {
        if ($('#bazi-form').length === 0) return;
        
        // Reset button handler - clear form and hide results
        $('#bazi-form').on('reset', function(e) {
            e.preventDefault();
            // Clear all inputs
            $('#birth_year').val('');
            $('#birth_month').val('');
            $('#birth_day').val('');
            $('#birth_hour').val('');
            $('#birth_minute').val('');
            $('#timezone').val('');
            $('#gender').val('');
            $('#longitude').val('');
            // Hide results and errors
            $('#bazi-results').hide();
            $('#bazi-error').hide();
            $('#location-status').empty();
            // Auto-detect timezone again
            detectTimezone();
        });
        
        $('#bazi-form').on('submit', function(e) {
            e.preventDefault();
            $('#bazi-loading').show(); $('#bazi-results').hide(); $('#bazi-error').hide();
            
            var year = parseInt($('#birth_year').val());
            var month = parseInt($('#birth_month').val());
            var day = parseInt($('#birth_day').val());
            
            if (!isValidDate(year, month, day)) {
                $('#bazi-loading').hide();
                $('#bazi-error').show().find('p').text('Invalid date: ' + year + '-' + month + '-' + day + ' does not exist.');
                return;
            }
            
            var formData = $(this).serialize() + '&action=calculate_bazi';
            
            $.ajax({
                url: baziAjax.ajaxurl,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    $('#bazi-loading').hide();
                    if (response.success) { displayBaziResults(response.data); }
                    else { $('#bazi-error').show().find('p').text('Error: ' + response.data); }
                },
                error: function(xhr, status, error) {
                    $('#bazi-loading').hide();
                    $('#bazi-error').show().find('p').text('Server error: ' + error);
                }
            });
        });
        
        $('#get-location-btn').on('click', function() {
            var $btn = $(this), $status = $('#location-status');
            if (!navigator.geolocation) { $status.removeClass('success loading').addClass('error').text('Geolocation is not supported'); return; }
            $btn.prop('disabled', true);
            $status.removeClass('success error').addClass('loading').text('Getting your location...');
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var lon = position.coords.longitude.toFixed(4);
                    $('#longitude').val(lon);
                    var estimatedTz = Math.round(position.coords.longitude / 15);
                    if (estimatedTz >= -12 && estimatedTz <= 14) { $('#timezone').val(estimatedTz); }
                    longitudeFromGeo = true;
                    $btn.prop('disabled', false);
                    $status.removeClass('loading error').addClass('success').text('Location detected: ' + lon + '°');
                },
                function(error) {
                    $btn.prop('disabled', false);
                    var errorMsg = 'Could not get location: ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED: errorMsg += 'Permission denied'; break;
                        case error.POSITION_UNAVAILABLE: errorMsg += 'Position unavailable'; break;
                        case error.TIMEOUT: errorMsg += 'Request timeout'; break;
                        default: errorMsg += 'Unknown error';
                    }
                    $status.removeClass('success loading').addClass('error').text(errorMsg);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });
        
        $('#longitude').on('change blur', function() {
            var lonVal = parseFloat($(this).val());
            if (!isNaN(lonVal) && lonVal >= -180 && lonVal <= 180) {
                var estimatedTz = Math.round(lonVal / 15);
                if (estimatedTz >= -12 && estimatedTz <= 14) { $('#timezone').val(estimatedTz); }
                longitudeFromGeo = true;
                $('#location-status').removeClass('error loading').addClass('success').text('Timezone adjusted to GMT' + (estimatedTz >= 0 ? '+' : '') + estimatedTz);
            }
        });
        
        $('#timezone').on('change', function() {
            if (longitudeFromGeo && $('#longitude').val()) {
                $('#longitude').val('');
                $('#location-status').removeClass('success error loading').addClass('error').text('Longitude cleared - timezone changed.');
                longitudeFromGeo = false;
            }
        });
        
        var loadedFromUrl = loadFromUrl();
        if (loadedFromUrl) {
            setTimeout(function() { $('#bazi-form').submit(); }, 500);
        } else {
            detectTimezone();
        }
    });
})(jQuery);
