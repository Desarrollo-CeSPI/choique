function multimedia_external_toggle()
{
  if ($('multimedia_is_external').getValue())
  {
    $('multimedia_large_uri').up('.form-row').hide();
    $('multimedia_medium_uri').up('.form-row').hide();
    //$('multimedia_small_uri').up('.form-row').hide();
    $('multimedia_external_uri').up('.form-row').show();
  }
  else
  {
    $('multimedia_large_uri').up('.form-row').show();
    $('multimedia_medium_uri').up('.form-row').show();
    //$('multimedia_small_uri').up('.form-row').show();
    $('multimedia_external_uri').up('.form-row').hide();
  }
}
