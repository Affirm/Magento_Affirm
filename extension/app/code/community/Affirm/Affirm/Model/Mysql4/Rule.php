<?php
class Affirm_Affirm_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('affirm/rule', 'rule_id');
    }
    
    public function massChangeStatus($ids, $status)
    {
        $db = $this->_getWriteAdapter();
        $db->update($this->getMainTable(),
            array('is_active' => $status), 'rule_id IN(' . implode(',', $ids) . ') ');
            
        return true;
    } 

    /**
     * Return codes of all product attributes currently used in promo rules
     *
     * @return array
     */
    public function getAttributes()
    {
        $read = $this->_getReadAdapter();
        $tbl   = $this->getTable('affirm/attribute');
        
        $select = $read->select()->from($tbl, new Zend_Db_Expr('DISTINCT code'));
        return $read->fetchCol($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of the rule
     *
     * @param int $id rule id
     * @param mixed $attributes
     * return Affirm_affirm_Model_Mysql4_Rule
     */
    public function saveAttributes($id, $attributes)
    {
        $write = $this->_getWriteAdapter();
        $tbl   = $this->getTable('affirm/attribute');
        
        $write->delete($tbl, array('rule_id=?' => $id));
        
        $data = array();
        foreach ($attributes as $code){
            $data[] = array(
                'rule_id' => $id,
                'code'    => $code,
            );
        }
        $write->insertMultiple($tbl, $data);
        
        return $this;
    }          
       
}