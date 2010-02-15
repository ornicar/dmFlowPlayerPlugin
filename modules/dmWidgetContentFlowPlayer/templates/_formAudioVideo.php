<?php

echo

$form->renderGlobalErrors(),

_open('div.dm_tabbed_form'),

_tag('ul.tabs',
  _tag('li', _link('#'.$baseTabId.'_media')->text(__('Media'))).
  _tag('li', _link('#'.$baseTabId.'_options')->text(__('Options'))).
  _tag('li', _link('#'.$baseTabId.'_splash')->text(__('Splash')))
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
    ).
    $form['method']->label(null, array('class' => 'ml10 mr10 fnone'))->field('.dm_media_method')->error()
  )
),

_tag('div#'.$baseTabId.'_options',
  _tag('ul',
    _tag('li.dm_form_element.autoplay.clearfix',
      $form['autoplay']->label(null, '.big')->field()->error()
    ).
    _tag('li.dm_form_element.control.clearfix',
      $form['control']->label(null, '.big')->field()->error()
    )
  )
),

_tag('div#'.$baseTabId.'_splash',

  _tag('div.dm_help.no_margin', __('Use a clickable image to launch the player')).
  _tag('div.toggle_group',
    $form['splashMediaId']->render(array('class' => 'dm_splash_media_id')).
  
    _tag('ul.media_fields',
      $form['splashMediaName']->renderRow().
      $form['splashFile']->renderRow()
    )
  ).
  ($hasSplashMedia
  ? _tag('ul',
      _tag('li.dm_form_element.splash_alt.clearfix',
        $form['splashAlt']->label()->field()->error()
      ).
      _tag('li.dm_form_element.remove_media.clearfix',
        $form['removeSplash']->label(null, '.big')->field()->error()
      )
    )
  : '')
);

_close('div'); //div.dm_tabbed_form