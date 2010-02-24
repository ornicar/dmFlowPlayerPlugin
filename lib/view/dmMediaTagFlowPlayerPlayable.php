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

  protected function jsonifyAttributes(array $attributes)
  {
    $flowPlayerOptions = $this->getFlowplayerOptions($attributes);

    foreach(array('src', 'mimeGroup', 'autoplay', 'player_web_path', 'resize_method', 'control') as $jsonAttribute)
    {
      unset($attributes[$jsonAttribute]);
    }

    $attributes['class'][] = json_encode($flowPlayerOptions);

    return $attributes;
  }

  protected function getFlowPlayerOptions(array $attributes)
  {
    return $this->filterFlowPlayerOptions(array(
      'clip' => array(
        'url' => $attributes['src'],
        'autoPlay' => $attributes['autoplay'],
        'scaling' => $attributes['resize_method']
      ),
      'plugins' => array(
        'controls' => $attributes['control']
      ),
      'player_web_path' => $attributes['player_web_path'],
      'mimeGroup' => $attributes['mimeGroup']
    ), $attributes);
  }

  public function getAvailableMethods()
  {
    return array('fit', 'scale', 'half', 'orig');
  }
}