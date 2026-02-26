<?php
// Uninstall Bazi Calculator
if (!defined('WP_UNINSTALL_PLUGIN')) { exit; }
delete_option('bazi_calculator_version');
delete_transient('bazi_calculator_cache');
