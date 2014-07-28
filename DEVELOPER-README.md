Contribute
----------

Read the Developer Notes

1. Fork the repo
2. Create your feature branch (```git checkout -b my-new-feature```).
3. Commit your changes (```git commit -am 'Added some feature'```)
4. Push to the branch (```git push origin my-new-feature```)
5. Create a Pull Request

**To run the tests:**

```
make dependencies
make test
```

Versioning
----------

This project uses semantic versioning. When updating the extension, bump the
version in the following locations:

1. extension/app/code/community/Affirm/Affirm/etc/config.xml
1. extension/app/code/community/Affirm/AffirmPromo/etc/config.xml
1. build/affirm_tar_to_connect_config.php

To avoid drift, validate by executing `make validate_version`
