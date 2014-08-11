<?php

class ralessevents extends oxI18n {

    public static function onActivate() {

        $cfg = oxRegistry::getConfig();
        
        $dir = $cfg->getConfigParam("sCompileDir") . "*";
        foreach (glob($dir) as $item) {
            if (!is_dir($item)) {
                @unlink($item);
            }
        }
        
        oxRegistry::get("oxUtilsView")->getSmarty(true);
    }

    public static function onDeactivate() {
        
        $cfg = oxRegistry::getConfig();
        $dir = $cfg->getConfigParam("sCompileDir") . "*";
        foreach (glob($dir) as $item) {
            if (!is_dir($item)) {
                @unlink($item);
            }
        }
    }

}

?>