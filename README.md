
# Installation

### 1. Install xhprof PHP extension
Follow the directions [here](http://mirror.facebook.net/facebook/xhprof/doc.html#installation "xhprof PHP extension") to install the xhprof PHP extension.  You will need to either install the extension using pecl, or alternatively, compile it yourself.

### 2. Install xhprof Drupal extension
Download drupal-xhprof and enable it in the drupal administration panel.  Visit the xhprof settings page to setup URLs for profiling inclusion/exclusion.  Once you have some URLs set up to profile, xhprof will start profiling those URLs and storing the results so you can view their details.  You can also force profiling by adding XHPROF_ENABLED to the query string of the url you want to profile.  Alternatively, you can force XHProf to *not* profile by adding XHPROF_DISABLED to the query string if the url.

