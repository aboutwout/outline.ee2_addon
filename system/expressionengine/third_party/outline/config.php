<?php

if ( ! defined('OUTLINE_VERSION'))
{
  define('OUTLINE_VERSION', '0.1');
  define('OUTLINE_NAME', 'Outline');
  define('OUTLINE_DESCRIPTION', 'Generate navigation for the Pages module.');  
  define('OUTLINE_DOCUMENTATION', 'http://support.baseworks.nl/discussions/outline');
  define('OUTLINE_DEBUG', FALSE);
}

$config['name'] = OUTLINE_NAME;
$config['version'] = OUTLINE_VERSION;
$config['description'] = OUTLINE_DESCRIPTION;
$config['nsm_addon_updater']['versions_xml'] = '';