function multimedia_player_toggle()
{
  if ($('multimedia_uses_a_player').getValue())
  {
    $('multimedia_player_id').up('.form-row').show();
    $('multimedia_flv_params').up('fieldset').show();
  }
  else
  {
    $('multimedia_player_id').up('.form-row').hide();
    $('multimedia_flv_params').up('fieldset').hide();
  }
}
