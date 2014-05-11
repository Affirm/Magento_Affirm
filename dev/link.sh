#!/bin/bash

# Usage: sh /link.sh /absolute/path/to/magento/root/

# Example: sh link.sh /home/bitnami/apps/magento/htdocs/

# given absolute path to magento root directory, create symbolic links to source code
# TODO(brian): make idempotent
# TODO(brian): make it correct when called from any directory
# TODO(brian): de-duplicate finding extension dir 

ln -s -t $*/app/code/community/ $(cd ../extension && pwd)/app/code/community/Affirm/
ln -s -t $*/skin/frontend/base/default/images/ $(cd ../extension && pwd)/skin/frontend/base/default/images/affirm/
ln -s $(cd ../extension && pwd)/app/etc/modules/Affirm_Affirm.xml $*/app/etc/modules 
