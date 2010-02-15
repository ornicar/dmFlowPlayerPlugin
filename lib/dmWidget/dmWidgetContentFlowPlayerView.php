<?php

class dmWidgetContentFlowPlayerView extends dmWidgetContentBaseMediaView
{
  public function configure()
  {
    parent::configure();

    $this->addRequiredVar('method');

    $this->addJavascript(array(
      'dmFlowPlayerPlugin.flowPlayer',
      'dmFlowPlayerPlugin.widgetView'
    ));
  }

  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    $vars['mediaTag']->addClass('dm_widget_content_flow_player');
    
    if ($vars['mediaTag'] instanceof dmMediaTagFlowPlayerPlayable)
    {
      if ($vars['splashMediaId'])
      {
        $splashMedia = dmDb::table('DmMedia')->findOneByIdWithFolder($vars['splashMediaId']);
        
        if (!$splashMedia instanceof DmMedia)
        {
          throw new dmException('No DmMedia found for media id : '.$vars['splashMediaId']);
        }
        
        $splashTag = $this->getHelper()->media($splashMedia)->alt($vars['splashAlt']);
        
        $vars['mediaTag']->splash($splashTag);
      }
      
      $vars['mediaTag']
      ->autoplay($vars['autoplay'])
      ->method($vars['method'])
      ->control($vars['control']);
    }
    
    if ($vars['mediaTag'] instanceof dmMediaTagFlowPlayerApplication)
    {
      foreach(array('flashConfig', 'flashVars') as $key)
      {
        $array = empty($vars[$key]) ? array() : sfYaml::load($vars[$key]);
        
        if (!empty($array))
        {
          $vars['mediaTag']->$key($array);
        }
      }
    }
    
    unset($vars['autoplay'], $vars['method'], $vars['legend'], $vars['splashMediaId'], $vars['splashAlt'], $vars['flashConfig'], $vars['flashVars']);
    
    return $vars;
  }
}