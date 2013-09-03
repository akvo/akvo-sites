#!/bin/bash

RUN_PS_UPDATES_SCRIPT="http://akvofoundation.org/runPSUpdates.php?id_organisation"
LOG_FILE="/var/log/akvo/ps_updates_log.html"
LOG_FILE_URL="http://akvofoundation.org/log/ps_updates_log.html"

for organisation_id in 272 275 539 912 1060 1061 1093
do
    echo "Updating organisation $organisation_id"
    printf "<br />[`date --rfc-3339=seconds`]<br />" >> $LOG_FILE
    curl $RUN_PS_UPDATES_SCRIPT=$organisation_id 2>&1 >> $LOG_FILE
    printf "<br />" >> $LOG_FILE
done

echo $LOG_FILE_URL | mail -s '[wp.akvo.org] Partner sites project updates refreshed' devops@akvo.org
