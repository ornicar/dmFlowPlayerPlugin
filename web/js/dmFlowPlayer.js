(function($)
{
	$.fn.dmFlowPlayer = function(opt)
  {
    return this.each(function()
    {
      var
      $player = $(this),
      id = 'dm_widget_content_flow_player_' + Math.round(999999*Math.random()),
      options = $player.metadata();

      $player.attr('id', id);

      if(options.mimeGroup == 'application') // pure Flash
      {
        flashembed(id, options.flashConfig, options.flashVars);
      }
      else // playable: video or audio
      {
        flowplayer(id, options.player_web_path, options);
      }
    });
  }
})(jQuery);