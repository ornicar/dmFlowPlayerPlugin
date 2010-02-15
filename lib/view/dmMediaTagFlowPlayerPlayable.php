<?php

/*
 * Abstract class for audio and video
 */
abstract class dmMediaTagFlowPlayerPlayable extends dmMediaTagBaseFlowPlayer
{
  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'autoplay'        => false,
      'player_web_path' => $this->context->getHelper()->getOtherAssetWebPath('dmFlowPlayerPlugin.swfPlayer'),
      'resize_method'   => 'scale',
      'control'         => true
    ));
  }

  /*
   * Wether or not the video will start automatically on page load
   */
  public function autoplay($val)
  {
    return $this->setOption('autoplay', (bool) $val);
  }
  
  /*
   * Wether or not to show the player controls
   */
  public function control($val)
  {
    return $this->setOption('control', (bool) $val);
  }
  
  /*
   * Change the player web path
   */
  public function player($val)
  {
    return $this->setOption('player_web_path', (string) $val);
  }
  
  /*
   * Change the scaling method
   */
  public function method($method)
  {
    if (!in_array($method, $this->getAvailableMethods()))
    {
      throw new dmException(sprintf('%s is not a valid method. These are : %s',
      $method,
      implode(', ', $this->getAvailableMethods())
      ));
    }

    return $this->setOption('resize_method', $method);
  }
  
  protected function getJsonAttributes()
  {
    return array_merge(
      parent::getJsonAttributes(),
      array('autoplay', 'player_web_path', 'resize_method', 'control')
    );
  }

  public function getAvailableMethods()
  {
    return array('fit', 'scale', 'half', 'orig');
  }
}