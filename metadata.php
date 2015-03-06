<?php

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

$aModule = array(
    'id'          => 'raless',
    'title'       => 'ra less',
    'description' => array(
        'de'    => 'Kompiliert LESS-Dateien in Themes und Modulen',
        'en'    => 'Compiles LESS files in themes and modules',
    ),
    'email'         => 'info@renzel-agentur.de',
    'url'           => 'http://www.renzel-agentur.de/',
    'thumbnail'     => 'picture.jpg',
    'version'       => '1.0',
    'author'        => 'role@vkf-renzel.de',
    'extend' => array(
        'oxutilsview'       => 'ra/less/extend/raless_oxutilsview',
        'theme_config'      => 'ra/less/extend/raless_theme_config'
    ),
    'blocks' => array(
        array(
            'template'                  => 'layout/base.tpl',
            'block'                     => 'base_style',
            'file'                      => 'views/ra/blocks/tpl/layout/base.tpl'
        ),
        array(
            'template'                  => 'theme_config.tpl',
            'block'                     => 'admin_theme_config_form',
            'file'                      => 'views/admin/blocks/tpl/theme_config.tpl'
        )
    ),
    'files' => array(),
    'settings' => array(
        array(
            'group' => 'main',
            'name'  => 'sVariables',
            'type'  => 'str',
            'value' => ''
        )
    ),
    'events' => array(
        'onActivate'    => 'ralessevents::onActivate',
        'onDeactivate'  => 'ralessevents::onDeactivate'
    )
);
