$.fn.extend({
  
  dmWidgetContentFlowPlayerForm: function(widget)
  {
    var self = this,
		
		$form = self.find('form:first'),
		
		$tabs = $form.find('div.dm_tabbed_form')
		  .dmCoreTabForm()
		;
    
    self.dmWidgetContentBaseMediaForm(widget, {
      accept: '#dm_media_bar li.file.video, #dm_media_bar li.file.audio, #dm_media_bar li.file.application'
		});
		
    $('input.dm_splash_media_receiver', $form).droppable({
      accept:       '#dm_media_bar li.file.image',
      activeClass:  'droppable_active',
      hoverClass:   'droppable_hover',
      tolerance:    'touch',
      drop:         function(event, ui) {
        $('input.dm_splash_media_id', $form).val(ui.draggable.attr('id').replace(/dmm/, ''));
        $form.submit();
      }
    });
  }
});