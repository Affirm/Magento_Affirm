#!/bin/bash

# Usage: sh /link.sh /absolute/path/to/magento/root/

# Example: sh link.sh /home/bitnami/apps/magento/htdocs/

# given absolute path to magento root directory, create symbolic links to source code
# TODO(brian): make idempotent
# TODO(brian): make it correct when called from any directory
ln -s -t $*/app/code/community/ $(cd .. && pwd)/app/code/community/Affirm/
ln -s -t $*/skin/frontend/base/default/images/ $(cd .. && pwd)/skin/frontend/base/default/images/affirm/
ln -s $(cd .. && pwd)/app/etc/modules/Affirm_Affirm.xml $*/app/etc/modules 
