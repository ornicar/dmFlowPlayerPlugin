<?php

class dmMediaTagFlowPlayerAudio extends dmMediaTagFlowPlayerPlayable
{
  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'mimeGroup' => 'audio'
    ));
  }
}