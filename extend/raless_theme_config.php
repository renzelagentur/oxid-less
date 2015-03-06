<?php
 /**
  * raless_theme_config.php
  * 
  * @version   GIT: $Id$ PHP5.4 (16.10.2014)
  * @author    Robin Lehrmann <info@renzel-agentur.de>
  * @copyright Copyright (C) 22.10.2014 renzel.agentur GmbH. All rights reserved.
  * @license   http://www.renzel-agentur.de/licenses/raoxid-1.0.txt
  * @link      http://www.renzel-agentur.de/
  *
*/

/**
 * RaLess_Theme_Config
 *
 * @package   raless
 * @version   GIT: $Id$ PHP5.4 (16.10.2014)
 * @author    Robin Lehrmann <info@renzel-agentur.de>
 * @copyright Copyright (C) 22.10.2014 renzel.agentur GmbH. All rights reserved.
 * @license   http://www.renzel-agentur.de/licenses/raoxid-1.0.txt
 * @link      http://www.renzel-agentur.de/
 * @extend    theme_config
 *
 */
class RaLess_Theme_Config extends RaLess_Theme_Config_parent
{

    /**
     * deletes generated css files
     */
    public function saveConfVars()
    {
        foreach (new DirectoryIterator($this->getConfig()->getOutDir() . 'gen/') as $file) {
            if ($file->isFile()) {
                unlink($file->getRealPath());
            }
        }
        parent::saveConfVars();
    }
}
