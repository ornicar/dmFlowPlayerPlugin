(function($)
{
  // front
  $('#dm_page div.dm_widget').bind('dmWidgetLaunch', function()
  {
    $(this).find('.dm_flow_player').dmFlowPlayer();
  });

  // admin
  $(function()
  {
    $('#dm_admin_content .dm_flow_player').dmFlowPlayer();
  });
  
})(jQuery);