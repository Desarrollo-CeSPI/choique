rm config/app.yml
svn copy config/app.yml-default config/app.yml
svn delete config/app.yml-*
svn delete web-backend/*dev.php
svn delete web-frontend/*dev.php
