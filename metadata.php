<?php
/**
 * @author   math <math@vkf-renzel.de>
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

$aModule = array(
    'id'          => 'raless',
    'title'       => 'ra less',
    'description' => array(
        'de'    => 'Kompiliert LESS-Dateien',
        'en'    => 'Compiles LESS files',
    ),
    'email'         => 'math@vkf-renzel.de',
    'url'           => 'http://www.renzel-agentur.de/',
    'thumbnail'     => 'picture.jpg',
    'version'       => '1.0',
    'author'        => 'math@vkf-renzel.de',
    'extend' => array(
        'oxutilsview'       => 'ra/less/extend/raless_oxutilsview',
    ),
    'blocks' => array(
        array(
            'template'                  => 'layout/base.tpl',
            'block'                     => 'base_style',
            'file'                      => 'views/ra/blocks/tpl/layout/base.tpl'
        )
    ),
    'files' => array(
        'ralessevents' => 'ra/less/events.php',
        'lessc' => 'ra/less/core/lessc.inc.php'
    ),
    'events' => array(
        'onActivate'    => 'ralessevents::onActivate',
        'onDeactivate'  => 'ralessevents::onDeactivate'
    )
);