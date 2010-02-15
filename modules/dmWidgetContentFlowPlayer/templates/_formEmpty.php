<?php

echo

$form->renderGlobalErrors(),

_open('div.dm_tabbed_form'),

_tag('ul.tabs',
  _tag('li', _link('#'.$baseTabId.'_media')->text(__('Media')))
),

_tag('div#'.$baseTabId.'_media',
  
  _tag('div.toggle_group',

    $form['mediaId']->render(array('class' => 'dm_media_id')).

    _tag('ul.media_fields',
      $form['mediaName']->renderRow().
      $form['file']->renderRow()
    )
  )
);

_close('div'); //div.dm_tabbed_form