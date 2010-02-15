(function($)
{
  var idCount = 1;
    
  $('#dm_page div.dm_widget').bind('dmWidgetLaunch', function()
  {
    var $player = $(this).find('.dm_flow_player');

    if(!$player.length)
    {
      return;
    }

    var id = 'dm_widget_content_flow_player_' + (idCount++), options = $player.metadata();

    $player.attr('id', id);

    switch (options.mimeGroup)
    {
      case 'application':
        flashembed(id, $.extend(options.flashConfig, {
          src: options.src
        }), options.flashVars);
        break;
      case 'video':
      case 'audio':
        flowplayer(id, options.player_web_path, {
          clip: {
            url: options.src,
            autoPlay: options.autoplay || false,
            scaling: options.resize_method || 'orig'
          },
          plugins: {
            controls: options.control || null
          }
        });
        break;
      default:
        $.dbg('Unknown mime group : ' + options.mimeGroup);
    }
  });
  
})(jQuery);