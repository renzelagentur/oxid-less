<?php

/**
 * RaLess_oxUtilsView
 *
 * @package   raless
 * @version   GIT: $Id$ PHP5.4 (16.10.2014)
 * @author    Robin Lehrmann <info@renzel-agentur.de>
 * @copyright Copyright (C) 22.10.2014 renzel.agentur GmbH. All rights reserved.
 * @license   http://www.renzel-agentur.de/licenses/raoxid-1.0.txt
 * @link      http://www.renzel-agentur.de/
 * @extend    oxUtilsView
 */
class RaLess_oxUtilsView extends RaLess_oxUtilsView_parent
{
    /**
     * loading smarty less load plugin
     *
     * @param string $oSmarty smarty object
     */
    protected function _fillCommonSmartyProperties($oSmarty)
    {
        parent::_fillCommonSmartyProperties($oSmarty);
        $aPluginsDir = $oSmarty->plugins_dir;
        $aPluginsDir[] = oxRegistry::getConfig()->getModulesDir() . 'ra/less/smarty/plugins';
        $oSmarty->plugins_dir = $aPluginsDir;
    }
}
