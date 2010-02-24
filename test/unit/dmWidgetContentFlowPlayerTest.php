<?php

if (!file_exists($config = realpath(dirname(__FILE__).'/../../../..') .'/config/ProjectConfiguration.class.php'))
{
  $config = getcwd().'/config/ProjectConfiguration.class.php';
}

require_once($config);

require_once(dm::getDir().'/dmCorePlugin/test/unit/helper/dmUnitTestHelper.php');

$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(53);

$t->comment('Create a test video media');

$mediaFileName = 'test_'.dmString::random().'.flv';
copy(
  dmOs::join(realpath(dirname(__FILE__).'/..'), 'data/flowplayer-700.flv'),
  dmOs::join(sfConfig::get('sf_upload_dir'), $mediaFileName)
);
$media = dmDb::create('DmMedia', array(
  'file' => $mediaFileName,
  'dm_media_folder_id' => dmDb::table('DmMediaFolder')->checkRoot()->id
))->saveGet();

$t->ok($media->exists(), 'A test media has been created');

$wtm = $helper->get('widget_type_manager');

$widgetType = $wtm->getWidgetType('dmWidgetContent', 'flowPlayer');

$formClass = $widgetType->getOption('form_class');

$t->comment('Create a test page');

$testPage = dmDb::create('DmPage', array(
  'module'  => dmString::random(),
  'action'  => dmString::random(),
  'name'    => dmString::random(),
  'slug'    => dmString::random()
));

$testPage->Node->insertAsFirstChildOf(dmDb::table('DmPage')->getTree()->fetchRoot());

$t->comment('Create a test widget');

$widget = dmDb::create('DmWidget', array(
  'module' => $widgetType->getModule(),
  'action' => $widgetType->getAction(),
  'value'  => '[]',
  'dm_zone_id' => $testPage->PageView->Area->Zones[0]
));

$t->comment('Create a '.$formClass.' instance');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$html = $form->render();
$t->like($html, '_^<form\s(.|\n)*</form>$_', 'Successfully obtained and rendered a '.$formClass.' instance');

$t->isnt($form->getStylesheets(), array(), 'This widget form requires additional stylesheet');
$t->isnt($form->getJavascripts(), array(), 'This widget form requires additional javascript');

$t->comment('Submit an empty form');

$form->bind($form->getDefaults(), array());
$t->is($form->isValid(), false, 'The form is not valid');

$t->comment('Use a bad media id');

$form->bind(array_merge($form->getDefaults(), array('mediaId' => 9999999999999)), array());
$t->is($form->isValid(), false, 'The form is not valid');

$t->comment('Use a good media id : '.$media->id);

$form->bind(array_merge($form->getDefaults(), array('mediaId' => $media->id)), array());
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$expected = 
$t->is_deeply($helper->ksort($widget->values), $helper->ksort(array(
  'mediaId'     => $media->id,
  'width'       => 300,
  'height'      => 200,
  'autoplay'    => false,
  'method'      => 'scale',
  'flashConfig' => '',
  'flashVars'   => '',
  'splashMediaId' => null,
  'splashAlt'   => '',
  'control'     => false
)), 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->is($form->getDefault('mediaId'), $media->id, 'The form default mediaId is correct');

$t->comment('Submit form without additional data');
$form->bind($form->getDefaults(), array());
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Change widget options');
$form->bind(array_merge($form->getDefaults(), array(
  'width'  => 600,
  'height' => 400,
  'cssClass' => 'test css_class',
  'autoplay' => true,
  'method'      => 'orig',
  'flashConfig' => '',
  'flashVars'   => '',
  'splashMediaId' => null,
  'splashAlt' => ''
)), array());
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$t->is_deeply($helper->ksort($widget->values), $helper->ksort(array(
  'mediaId'     => $media->id,
  'width'       => '600',
  'height'      => '400',
  'autoplay'    => true,
  'method'      => 'orig',
  'flashConfig' => '',
  'flashVars'   => '',
  'splashMediaId' => null,
  'splashAlt'   => '',
  'control'     => false
)), 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->comment('Bind with an uploaded file');

$media2FileName = 'test_'.dmString::random().'_'.$media->file;
$media2FullPath = sys_get_temp_dir().'/'.$media2FileName;
copy($media->fullPath, $media2FullPath);

$form->bind($form->getDefaults(), array(
  'file' => array(
    'name' => $media2FileName,
    'type' => $helper->get('mime_type_resolver')->getByFilename($media2FullPath),
    'tmp_name' => $media2FullPath,
    'error' => 0,
    'size' => filesize($media2FullPath)
  )
));
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');

$t->isnt($widget->values['mediaId'], $media->id, 'The widget mediaId value has changed');

$media2 = dmDb::table('DmMedia')->find($widget->values['mediaId']);

$t->ok($media2->exists(), 'A new DmMedia record has been created');

$t->is_deeply($helper->ksort($widget->values), $helper->ksort(array(
  'mediaId'     => $media2->id,
  'width'       => '600',
  'height'      => '400',
  'autoplay'    => true,
  'method'      => 'orig',
  'flashConfig' => '',
  'flashVars'   => '',
  'splashMediaId' => null,
  'splashAlt'   => '',
  'control'     => false
)), 'Widget values are correct');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->comment('Bind with an uploaded splash file');

$media3FileName = 'test_'.dmString::random().'.jpg';
$media3FullPath = sys_get_temp_dir().'/'.$media3FileName;
copy(dmOs::join(realpath(dirname(__FILE__).'/..'), 'data/flow_eye.jpg'), $media3FullPath);

$form->bind($form->getDefaults(), array(
  'splashFile' => array(
    'name' => $media3FileName,
    'type' => $helper->get('mime_type_resolver')->getByFilename($media3FullPath),
    'tmp_name' => $media3FullPath,
    'error' => 0,
    'size' => filesize($media3FullPath)
  )
));
$t->is($form->isValid(), true, 'The form is valid');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');

$t->isnt($widget->values['splashMediaId'], null, 'The widget splashMediaId value has changed');

$media3 = dmDb::table('DmMedia')->find($widget->values['splashMediaId']);

$t->ok($media3->exists(), 'A new DmMedia record has been created');

$t->is_deeply($helper->ksort($widget->values), $helper->ksort(array(
  'mediaId'     => $media2->id,
  'width'       => '600',
  'height'      => '400',
  'autoplay'    => true,
  'method'      => 'orig',
  'flashConfig' => '',
  'flashVars'   => '',
  'splashMediaId' => $media3->id,
  'splashAlt'   => '',
  'control'     => false
)), 'Widget values are correct');

$t->comment('Now display the widget');

$widgetArray = $widget->toArrayWithMappedValue();

$t->is_deeply($helper->ksort($widgetArray), $helper->ksort(array(
  'id' => $widget->id,
  'dm_zone_id' => $widget->Zone->id,
  'module' => 'dmWidgetContent',
  'action' => 'flowPlayer',
  'css_class' => 'test css_class',
  'position' => $widget->position,
  'updated_at' => $widget->updatedAt,
  'value' => json_encode(array(
    'mediaId'     => $media2->id,
    'width'       => '600',
    'height'      => '400',
    'autoplay'    => true,
    'control'     => false,
    'method'      => 'orig',
    'flashConfig' => '',
    'flashVars'   => '',
    'splashMediaId' => $media3->id,
    'splashAlt'   => ''
  ))
)), 'Widget array with mapped value is correct');

$helper->get('service_container')->setParameter('widget_renderer.widget', $widgetArray);

$widgetRenderer = $helper->get('service_container')->getService('widget_renderer');

$t->is($widgetRenderer->getStylesheets(), array(), 'This widget view requires additional stylesheet');
$t->is($widgetRenderer->getJavascripts(), array('dmFlowPlayerPlugin.flowPlayer', 'dmFlowPlayerPlugin.dmFlowPlayer', 'dmFlowPlayerPlugin.launcher'), 'This widget requires 2 additional javascripts');

$t->ok($widgetRenderer, 'The widget has been rendered');

$widgetView = $widgetRenderer->getWidgetView();

$t->isa_ok($widgetView, $widgetType->getOption('view_class'), 'The widget view is a '.$widgetType->getOption('view_class'));

$t->ok($widgetView->isRequiredVar('mediaId'), 'mediaId is a view required var');
$t->ok($widgetView->isRequiredVar('method'), 'method is a view required var');

$viewVars = $widgetView->getViewVars();
$mediaTag = $viewVars['mediaTag'];

$t->isa_ok($mediaTag, 'dmMediaTagFlowPlayerVideo', 'The media tag is a dmMediaTagFlowPlayerVideo');

$t->is(
  $mediaTag->get('player_web_path'),
  $path = $helper->get('helper')->getOtherAssetWebPath('dmFlowPlayerPlugin.swfPlayer'),
  'media tag player_web_path is '.$path
);

$t->is($mediaTag->get('autoplay'), true, 'media tag autoplay is true');

$t->is($mediaTag->get('resize_method'), 'orig', 'media tag resize_method is orig');

$t->isa_ok($mediaTag->get('splash'), 'dmMediaTagImage', 'media tag splash is a dmMediaTagImage');

$t->is($mediaTag->get('width'), '600', 'media tag width is 600');

$t->is($mediaTag->get('height'), '400', 'media tag height is 400');

$t->is_deeply(
  $mediaTag->get('class'),
  $class = array('dm_flow_player', 'dm_widget_content_flow_player'),
  'media tag css_class is '.implode(', ', $class)
);

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->comment('Remove splash image');

$form->bind(array_merge($form->getDefaults(), array(
  'removeSplash' => true
)), array());
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$t->is_deeply($helper->ksort($widget->values), $helper->ksort(array(
  'mediaId'     => $media2->id,
  'width'       => '600',
  'height'      => '400',
  'autoplay'    => true,
  'method'      => 'orig',
  'flashConfig' => '',
  'flashVars'   => '',
  'splashMediaId' => null,
  'splashAlt'   => '',
  'control'     => false
)), 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->comment('Add controls');

$form->bind(array_merge($form->getDefaults(), array(
  'control' => true
)), array());
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$t->is_deeply($helper->ksort($widget->values), $helper->ksort(array(
  'mediaId'     => $media2->id,
  'width'       => '600',
  'height'      => '400',
  'autoplay'    => true,
  'method'      => 'orig',
  'flashConfig' => '',
  'flashVars'   => '',
  'splashMediaId' => null,
  'splashAlt'   => '',
  'control'     => true
)), 'Widget values are correct');

$t->comment('Now display the widget');

$widgetArray = $widget->toArrayWithMappedValue();

$t->is_deeply($helper->ksort($widgetArray), $helper->ksort(array(
  'id' => $widget->id,
  'dm_zone_id' => $widget->Zone->id,
  'module' => 'dmWidgetContent',
  'action' => 'flowPlayer',
  'css_class' => 'test css_class',
  'position' => $widget->position,
  'updated_at' => $widget->updatedAt,
  'value' => json_encode(array(
    'mediaId'     => $media2->id,
    'width'       => '600',
    'height'      => '400',
    'autoplay'    => true,
    'control'     => true,
    'method'      => 'orig',
    'flashConfig' => '',
    'flashVars'   => '',
    'splashMediaId' => null,
    'splashAlt' => ''
  ))
)), 'Widget array with mapped value is correct');

$helper->get('service_container')->setParameter('widget_renderer.widget', $widgetArray);

$widgetRenderer = $helper->get('service_container')->getService('widget_renderer');

$t->ok($widgetRenderer->getHtml(), 'The widget has been rendered');

$widgetView = $widgetRenderer->getWidgetView();

$viewVars = $widgetView->getViewVars();
$mediaTag = $viewVars['mediaTag'];

$t->isa_ok($mediaTag, 'dmMediaTagFlowPlayerVideo', 'The media tag is a dmMediaTagFlowPlayerVideo');

$t->is($mediaTag->get('splash'), null, 'media tag splash is null');

$t->is($mediaTag->get('width'), '600', 'media tag width is 600');

$t->is($mediaTag->get('height'), '400', 'media tag height is 400');

$t->is_deeply(
  $mediaTag->get('class'),
  $class = array('dm_flow_player', 'dm_widget_content_flow_player'),
  'media tag css_class is '.implode(', ', $class)
);

/*
 * Clear the mess
 */
$testPage->PageView->delete();
$testPage->Node->delete();
$widget->delete();
$media->delete();
$media2->delete();
$media3->delete();