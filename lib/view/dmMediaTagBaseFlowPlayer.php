<?php

abstract class dmMediaTagBaseFlowPlayer extends dmMediaTag
{
  protected function initialize(array $options = array())
  {
    parent::initialize($options);
    
    $this->addClass('dm_flow_player');

    $this->addJavascript(array(
      'dmFlowPlayerPlugin.flowPlayer',
      'dmFlowPlayerPlugin.dmFlowPlayer',
      'dmFlowPlayerPlugin.launcher'
    ));
  }
  
  public function getDefaultOptions()
  {
    return array_merge(parent::getDefaultOptions(), array(
      'splash'          => null
    ));
  }
  
  /*
   * Change the splash
   * Can be an instance of dmMediaTagImage or a string
   */
  public function splash($splash)
  {
    if(!$splash instanceof dmMediaTagImage && !is_string($splash))
    {
      throw new dmException('The flow player splash must be a instance of dmMediaTagImage or a string');
    }
    
    return $this->setOption('splash', $splash);
  }
  
  public function render()
  {
    $preparedAttributes = $this->prepareAttributesForHtml($this->options);
    
    $splash = $preparedAttributes['splash'];
    unset($preparedAttributes['splash']);
    
    $tag = '<div'.$this->convertAttributesToHtml($preparedAttributes).'>'.$splash.'</div>';

    return $tag;
  }

  protected function prepareAttributesForHtml(array $attributes)
  {
    $attributes = parent::prepareAttributesForHtml($attributes);
    
    $attributes = $this->prepareSplash($attributes);

    $attributes = $this->jsonifyAttributes($attributes);
    
    $attributes['style'] = sprintf('width: %s; height: %s; display: block;',
      $this->cleanDimension(dmArray::get($attributes, 'width', 'auto')),
      $this->cleanDimension(dmArray::get($attributes, 'height', 'auto'))
    );
    unset($attributes['width'], $attributes['height']);
    
    return $attributes;
  }
  
  protected function prepareSplash(array $attributes)
  {
    $splash = dmArray::get($attributes, 'splash', '');
    
    if ($splash instanceof dmMediaTagImage)
    {
      if (!$splash->hasSize())
      {
        if ($attributes['width'])
        {
          $splash->width($attributes['width']);
        }
        if ($attributes['height'])
        {
          $splash->height($attributes['height']);
        }
      }
    }
    
    $attributes['splash'] = $splash;
    
    return $attributes;
  }
  
  protected function cleanDimension($dimension)
  {
    return is_numeric($dimension) ? $dimension.'px' : $dimension;
  }

  protected function filterFlowPlayerOptions(array $options, array $attributes)
  {
    $event = new sfEvent($this, 'dm_flow_player.filter_options', array(
      'attributes'  => $attributes,
      'media_id'    => $this->resource->isType('media') ? $this->resource->getSource()->id : null
    ));

    return $this->context->getEventDispatcher()->filter($event, $options)->getReturnValue();
  }
}