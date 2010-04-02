if (Drupal.jsEnabled) {
  $(function() {
    $('#xhprof-clear-runs').click(function() {
      if (!confirm(Drupal.t('Are you sure you want to delete all of your xhprof run data?'))) {
        return false;
      }
    });
  });
}
