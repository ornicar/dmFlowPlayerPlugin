<?php

echo

$form->renderGlobalErrors(),

_open('div.dm_tabbed_form'),

_tag('ul.tabs',
  _tag('li', _link('#'.$baseTabId.'_media')->text(__('Media'))).
  _tag('li', _link('#'.$baseTabId.'_config')->text(__('Config'))).
  _tag('li', _link('#'.$baseTabId.'_vars')->text(__('Vars')))
),

_tag('div#'.$baseTabId.'_media',
  
  _tag('div.toggle_group',

    $form['mediaId']->render(array('class' => 'dm_media_id')).

    _tag('a.show_media_fields.toggler', __('Change file')).

    _tag('ul.media_fields.none',
      $form['mediaName']->renderRow().
      $form['file']->renderRow()
    )

  ).
  
  _tag('ul',
    _tag('li.dm_form_element.multi_inputs.thumbnail.clearfix',
      $form['width']->renderError().
      $form['height']->renderError().
      _tag('label', __('Dimensions')).
      $form['width']->render().
      'x'.
      $form['height']->render()
    )
  )
),

_tag('div#'.$baseTabId.'_config',
  _tag('ul',
    $form['flashConfig']->renderRow()
  ).
  _tag('div.dm_help.no_margin', __('Yaml format. See available configuration: http://flowplayer.org/tools/flashembed.html'))
),

_tag('div#'.$baseTabId.'_vars',
  _tag('ul',
    $form['flashVars']->renderRow()
  ).
  _tag('div.dm_help.no_margin', __('Yaml format.'))
);

_close('div'); //div.dm_tabbed_form