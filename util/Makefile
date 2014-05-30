#          __   __  _
#         / _| / _|(_)
#   __ _ | |_ | |_  _  _ __  _ __ ___
#  / _` ||  _||  _|| || '__|| '_ ` _ \
# | (_| || |  | |  | || |   | | | | | |
#  \__,_||_|  |_|  |_||_|   |_| |_| |_|
#
#  Deploy script for Affirm's Payment Method Extension for the Magento eCommerce Platform
#  --------------------------------------------------------------------------------------
#
#  Place this script in your Magento root and commit it to your version control system.
#

NAME = Magento_Affirm

REPO = git@github.com:Affirm/Magento_Affirm.git
BRANCH = master

SRC = extension/
DEST = .

SCRIPT = aff-modgit
CMD = ./$(SCRIPT)

.modgit: $(SCRIPT)
	$(CMD) init

install: $(SCRIPT) 
	$(CMD) -i $(SRC):$(DEST) -b $(BRANCH) add $(NAME) $(REPO) 

update: $(SCRIPT)
	$(CMD) up $(NAME)

remove: $(SCRIPT)
	$(CMD) rm $(NAME)

$(SCRIPT):
	wget -O $(SCRIPT) https://raw.githubusercontent.com/Affirm/modgit/master/modgit
