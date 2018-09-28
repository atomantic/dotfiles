#!/bin/sh
/bin/sh /opt/local/share/phpDoc/phpdoc -ed /Users/rrehm/Projekte/plist/examples -d /Users/rrehm/Projekte/plist/classes -ti "CFPropertyList" -t /Users/rrehm/Projekte/plist/docs/ -ue on

cd /Users/rrehm/Projekte/plist/
tar --exclude=".git|*.sh" -czf ../CFPropertyList.tgz . 
