<?php

if (!file_exists($config = realpath(dirname(__FILE__).'/../../../..') .'/config/ProjectConfiguration.class.php'))
{
  $config = getcwd().'/config/ProjectConfiguration.class.php';
}

require_once $config;
require_once(dm::getDir().'/dmCorePlugin/test/unit/helper/dmUnitTestHelper.php');

$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test();

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

$t->comment('Create a test image media');

$imageFileName = 'test_'.dmString::random().'.jpg';
copy(
  dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
  dmOs::join(sfConfig::get('sf_upload_dir'), $imageFileName)
);
$image = dmDb::create('DmMedia', array(
  'file' => $imageFileName,
  'dm_media_folder_id' => dmDb::table('DmMediaFolder')->checkRoot()->id
))->saveGet();

$t->ok($image->exists(), 'A test media has been created');

dm::loadHelpers(array('Dm'));
$relativeUrlRoot = $helper->get('request')->getRelativeUrlRoot();

$expected = sprintf('<div class="dm_flow_player %s" style="width: auto; height: auto; display: block;"></div>',
  dmString::escape(json_encode(array(
    'autoplay'        => false,
    'control'         => true,
    'mimeGroup'       => 'video',
    'player_web_path' => $helper->get('helper')->getOtherAssetWebPath('dmFlowPlayerPlugin.swfPlayer'),
    'resize_method'   => 'scale',
    'src'             => $relativeUrlRoot.'/'.$media->webPath
  )))
);
$t->is(_media($media)->render(), $expected, $expected);

$expected = sprintf('<div class="dm_flow_player %s" style="width: 300px; height: auto; display: block;"></div>',
  dmString::escape(json_encode(array(
    'autoplay'        => false,
    'control'         => true,
    'mimeGroup'       => 'video',
    'player_web_path' => $helper->get('helper')->getOtherAssetWebPath('dmFlowPlayerPlugin.swfPlayer'),
    'resize_method'   => 'scale',
    'src'             => $relativeUrlRoot.'/'.$media->webPath
  )))
);
$t->is(_media($media)->width(300)->render(), $expected, $expected);

$expected = sprintf('<div class="dm_flow_player %s" style="width: 300px; height: 200px; display: block;"></div>',
  dmString::escape(json_encode(array(
    'autoplay'        => false,
    'control'         => true,
    'mimeGroup'       => 'video',
    'player_web_path' => $helper->get('helper')->getOtherAssetWebPath('dmFlowPlayerPlugin.swfPlayer'),
    'resize_method'   => 'scale',
    'src'             => $relativeUrlRoot.'/'.$media->webPath
  )))
);
$t->is(_media($media)->size(300, 200)->render(), $expected, $expected);

$expected = sprintf('<div class="dm_flow_player %s" style="width: 300px; height: 200px; display: block;">%s</div>',
  dmString::escape(json_encode(array(
    'autoplay'        => false,
    'control'         => true,
    'mimeGroup'       => 'video',
    'player_web_path' => $helper->get('helper')->getOtherAssetWebPath('dmFlowPlayerPlugin.swfPlayer'),
    'resize_method'   => 'scale',
    'src'             => $relativeUrlRoot.'/'.$media->webPath
  ))),
  _media($image)->size(300, 200)
);
$t->is(_media($media)->size(300, 200)->splash(_media($image))->render(), $expected, $expected);

$expected = sprintf('<div class="dm_flow_player %s" style="width: 300px; height: 200px; display: block;">%s</div>',
  dmString::escape(json_encode(array(
    'autoplay'        => false,
    'control'         => true,
    'mimeGroup'       => 'video',
    'player_web_path' => $helper->get('helper')->getOtherAssetWebPath('dmFlowPlayerPlugin.swfPlayer'),
    'resize_method'   => 'scale',
    'src'             => $relativeUrlRoot.'/'.$media->webPath
  ))),
  _media($image)->size(300, 200)->alt('the splash alt')
);
$t->is(_media($media)->size(300, 200)->splash(_media($image)->alt('the splash alt'))->render(), $expected, $expected);

$expected = sprintf('<div class="dm_flow_player %s" style="width: 300px; height: 200px; display: block;">%s</div>',
  dmString::escape(json_encode(array(
    'autoplay'        => true,
    'control'         => false,
    'mimeGroup'       => 'video',
    'player_web_path' => $helper->get('helper')->getOtherAssetWebPath('dmFlowPlayerPlugin.swfPlayer'),
    'resize_method'   => 'fit',
    'src'             => $relativeUrlRoot.'/'.$media->webPath
  ))),
  _media($image)->size(300, 200)
);
$t->is(_media($media)->autoplay(true)->control(false)->method('fit')->size(300, 200)->splash(_media($image))->render(), $expected, $expected);

$t->comment('Test text alternatives');

$alternative = markdown('I am a [markdown](http://diem-project.org) text');
$expected = sprintf('<div class="dm_flow_player %s" style="width: auto; height: auto; display: block;">%s</div>',
  dmString::escape(json_encode(array(
    'autoplay'        => false,
    'control'         => true,
    'mimeGroup'       => 'video',
    'player_web_path' => $helper->get('helper')->getOtherAssetWebPath('dmFlowPlayerPlugin.swfPlayer'),
    'resize_method'   => 'scale',
    'src'             => $relativeUrlRoot.'/'.$media->webPath
  ))),
  $alternative
);
$t->is(_media($media)->splash($alternative)->render(), $expected, $expected);

/*
 * Clear the mess
 */
$media->delete();
$image->delete();

$t->comment('Create a test flash media');

$mediaFileName = 'test_'.dmString::random().'.swf';
copy(
  dmOs::join(realpath(dirname(__FILE__).'/..'), 'data/clock.swf'),
  dmOs::join(sfConfig::get('sf_upload_dir'), $mediaFileName)
);
$media = dmDb::create('DmMedia', array(
  'file' => $mediaFileName,
  'dm_media_folder_id' => dmDb::table('DmMediaFolder')->checkRoot()->id
))->saveGet();

$t->ok($media->exists(), 'A test media has been created');

$expected = sprintf('<div class="dm_flow_player %s" style="width: auto; height: auto; display: block;"></div>',
  dmString::escape(json_encode(array(
    'flashConfig'     => array(),
    'flashVars'       => array(),
    'mimeGroup'       => 'application',
    'src'             => $relativeUrlRoot.'/'.$media->webPath
  )))
);
$t->is(_media($media)->render(), $expected, $expected);

/*
 * Clear the mess
 */
$media->delete();