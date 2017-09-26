import re
import xml.etree.ElementTree as ET

def version_in_tarball_config(path):
    with open(path, 'r') as f:
        for line in f:
            match = re.match(r"^.*extension_version.*> '(.*)\'", line)
            if match is not None:
                return match.group(1)
        return None

def version(namespace, module):
    path = "extension/app/code/community/{0}/{1}/etc/config.xml".format(namespace, module)
    x = "{0}_{1}".format(namespace, module)

    tree = ET.parse(path)
    config = tree.getroot()
    return config.find("modules").find(x).find("version").text

if version("Affirm", "Affirm") != version_in_tarball_config("build/affirm_tar_to_connect_config.php"):
    print ""
    print "ERROR: version mismatch"
    print "Affirm_Affirm {0}".format(version("Affirm", "Affirm"))
    print "Version in tarball config: {0}".format(version_in_tarball_config("build/affirm_tar_to_connect_config.php"))
    print ""
    exit(1)

print "Versions match! {0}".format(version("Affirm", "Affirm"))
