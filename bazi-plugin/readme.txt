=== Bazi Calculator ===
Contributors: webdiesel
Tags: bazi, chinese astrology, four pillars, destiny, zodiac
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.2
Stable tag: 25.4
License: GPLv2 or later

Accurate Bazi (Four Pillars of Destiny) calculator with precise astronomical solar longitude calculations.

== Description ==

Add a Bazi calculator to your WordPress site using shortcode: `[bazi_calculator]`

Features:
* Accurate solar longitude calculations
* Four Pillars: Year, Month, Day, Hour
* Ten-year major luck cycles
* Geolocation support
* Mobile responsive design

== Installation ==

1. Upload `bazi-plugin` folder to `/wp-content/plugins/`
2. Activate the plugin
3. Add `[bazi_calculator]` to any page

== Changelog ==

= 25.4 =
* Fixed button styles being overridden by WordPress themes (added !important)
* Buttons now display correctly: green Calculate, gray Reset

= 25.3 =
* Completely removed localStorage - form never auto-fills from previous session
* Form always starts empty and returns to empty state on Reset

= 25.2 =
* Fixed: Form no longer auto-loads data from localStorage on page load
* Form always starts empty (except when using shared URL)

= 25.1 =
* Removed pre-filled form values - form now starts empty
* Reset button now fully clears all form fields
* Auto-detect timezone on page load and after reset

= 25.0 =
* Initial release
