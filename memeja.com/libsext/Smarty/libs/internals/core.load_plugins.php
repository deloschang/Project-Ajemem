<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Load requested plugins
 *
 * @param array $plugins
 */

// $plugins

function smarty_core_load_plugins($params, &$smarty)
{

    foreach ($params['plugins'] as $_plugin_info) {
        list($_type, $_name, $_tpl_file, $_tpl_line, $_delayed_loading) = $_plugin_info;
        $_plugin = &$smarty->_plugins[$_type][$_name];

        /*
         * We do not load plugin more than once for each instance of Smarty.
         * The following code checks for that. The plugin can also be
         * registered dynamically at runtime, in which case template file
         * and line number will be unknown, so we fill them in.
         *
         * The final element of the info array is a flag that indicates
         * whether the dynamically registered plugin function has been
         * checked for existence yet or not.
         */
        if (isset($_plugin)) {
            if (empty($_plugin[3])) {
                if (!is_callable($_plugin[0])) {
                    $smarty->_trigger_fatal_error("[plugin] $_type '$_name' is not implemented", $_tpl_file, $_tpl_line, __FILE__, __LINE__);
                } else {
                    $_plugin[1] = $_tpl_file;
                    $_plugin[2] = $_tpl_line;
                    $_plugin[3] = true;
                    if (!isset($_plugin[4])) $_plugin[4] = true; /* cacheable */