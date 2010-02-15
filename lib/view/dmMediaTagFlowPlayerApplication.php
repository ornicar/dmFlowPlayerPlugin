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
  
  protected function getJsonAttributes()
  {
    return  array_merge(
      parent::getJsonAttributes(),
      array('flashConfig', 'flashVars')
    );
  }
}