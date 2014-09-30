<?php
class Affirm_AffirmPromo_Model_Observer
{
    /**
     * Update page layout, add banner code.
     *
     * @param Varien_Event_Observer $observer
     * @return Leiribits_Invoicexpress_Model_Observer
     */
    public function layoutGenerateBlocksBefore(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig('affirmpromo/settings/active')) {
            return '';
        }

        if (Mage::app()->getStore()->isAdmin()) {
            return;
        }
        $config = Mage::helper('affirmpromo')->getSectionConfig();

        if ($config->getDisplay()) {
            switch ($config->getPositionHorizontal()) {
                case 'left': $name = 'left'; break;
                case 'right': $name = 'right'; break;
                default: $name = 'content'; break;
            }
            $vertical = ('top'==$config->getPositionVertical() ? 'before="-"' : 'after="+"');
            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addUpdate(
                '<reference name="'.$name.'">
                    <block type="affirmpromo/promo" name="affirmpromo" template="affirmpromo/snippet.phtml" '.$vertical.'/>
                </reference>');
        }

        return $this;
    }
}

