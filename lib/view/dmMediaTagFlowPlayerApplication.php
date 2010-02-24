<?php

class dmMediaTagFlowPlayerApplication extends dmMediaTagBaseFlowPlayer
{
  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'mimeGroup' => 'application',
      'flashConfig' => array(),
      'flashVars' => array()
    ));
  }
  
  /*
   * Assign flash config: http://flowplayer.org/tools/flashembed.html
   */
  public function flashConfig(array $config)
  {
    return $this->setOption('flashConfig', sfToolkit::arrayDeepMerge($this->get('flashConfig'), $config));
  }
  
  /*
   * Assign flash vars: http://flowplayer.org/tools/flashembed.html
   */
  public function flashVars(array $vars)
  {
    return $this->setOption('flashVars', sfToolkit::arrayDeepMerge($this->get('flashVars'), $vars));
  }

  protected function jsonifyAttributes(array $attributes)
  {
    $flowPlayerOptions = $this->getFlowPlayerOptions($attributes);

    foreach(array('src', 'mimeGroup', 'flashConfig', 'flashVars') as $jsonAttribute)
    {
      unset($attributes[$jsonAttribute]);
    }

    $attributes['class'][] = json_encode($flowPlayerOptions);

    return $attributes;
  }

  protected function getFlowPlayerOptions(array $attributes)
  {
    return $this->filterFlowPlayerOptions(array(
      'flashConfig' => array_merge(array('src' => $attributes['src']), $attributes['flashConfig']),
      'flashVars' => $attributes['flashVars'],
      'mimeGroup' => $attributes['mimeGroup']
    ), $attributes);
  }
}