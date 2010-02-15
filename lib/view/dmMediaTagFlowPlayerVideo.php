<?php

class dmMediaTagFlowPlayerVideo extends dmMediaTagFlowPlayerPlayable
{
  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'mimeGroup' => 'video'
    ));
  }
}