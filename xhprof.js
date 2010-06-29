Drupal.behaviors.xhprof = function() {
  $('#xhprof-clear-runs').click(function() {
    if (!confirm(Drupal.t('Are you sure you want to delete all of your XHProf run data?'))) {
      return false;
    }
  });
};
