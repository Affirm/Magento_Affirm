<?php
class Affirm_Affirm_Adminhtml_RuleController extends Mage_Adminhtml_Controller_Action
{
    protected $_title     = 'Affirm Payment Restriction';
    protected $_modelName = 'rule';
    
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        $this->_title($this->__('Sales'))->_title($this->__($this->_title));	 
        return $this;
    } 
    
    public function indexAction()
    {
	    $this->loadLayout(); 
        $this->_setActiveMenu('affirm');
        $this->_addContent($this->getLayout()->createBlock('affirm/adminhtml_' . $this->_modelName));
 	    $this->renderLayout();
    }

	public function newAction() 
	{
        $this->editAction();
	}
	
    public function editAction() 
    {
		$id     = (int) $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affirm/' . $this->_modelName)->load($id);

		if ($id && !$model->getId()) {
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affirm')->__('Record does not exist'));
			$this->_redirect('*/*/');
			return;
		}
		
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
		else {
		    $this->prepareForEdit($model);
		}
		
		Mage::register('affirm_' . $this->_modelName, $model);

		$this->loadLayout();
		
		$this->_setActiveMenu('sales/affirm/' . $this->_modelName . 's');
		$this->_title($this->__('Edit'));
		
		$head = $this->getLayout()->getBlock('head');
		$head->setCanLoadExtJs(1);
		$head->setCanLoadRulesJs(1);
		
        $this->_addContent($this->getLayout()->createBlock('affirm/adminhtml_' . $this->_modelName . '_edit'));
        $this->_addLeft($this->getLayout()->createBlock('affirm/adminhtml_' . $this->_modelName . '_edit_tabs'));
        
		$this->renderLayout();
	}

	public function saveAction() 
	{
	    $id     = $this->getRequest()->getParam('id');
	    $model  = Mage::getModel('affirm/' . $this->_modelName);
	    $data = $this->getRequest()->getPost();
		if ($data) {
		    
            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            unset($data['rule']);
			$model->setData($data);  // common fields
			$model->loadPost($data); // rules
			
			$model->setId($id);
			try {
			    $this->prepareForSave($model);
			    
				$model->save();
				
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				$msg = Mage::helper('affirm')->__($this->_title . ' has been successfully saved');
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                if ($this->getRequest()->getParam('continue')){
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                else {
                    $this->_redirect('*/*');
                }
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }	
            return;
        }
        
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affirm')->__('Unable to find a record to save'));
        $this->_redirect('*/*');
	} 
	
    public function deleteAction()
    {
		$id     = (int) $this->getRequest()->getParam('id');
		$model  = Mage::getModel('affirm/' . $this->_modelName)->load($id);

		if ($id && !$model->getId()) {
    		Mage::getSingleton('adminhtml/session')->addError($this->__('Record does not exist'));
			$this->_redirect('*/*/');
			return;
		}
         
        try {
            $model->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__($this->_title . ' has been successfully deleted'));
        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
    }	
    
    public function duplicateAction()
    {
        $id = $this->getRequest()->getParam('rule_id');
        if (!$id) {
            $this->_getSession()->addError($this->__('Please select a rule to duplicate.'));
            return $this->_redirect('*/*');
        }
        
        try {
            $model  = Mage::getModel('affirm/' . $this->_modelName)->load($id);
            if (!$model->getId()){
                $this->_getSession()->addError($this->__('Please select a rule to duplicate.'));
                return $this->_redirect('*/*');
            }

            $rule = clone $model;
            $rule->setIsActive(0);
            $rule->setId(null);
            $rule->save();
            
            $this->_getSession()->addSuccess(
                $this->__('The rule has been duplicated. Please feel free to activate it.')
            );
            return $this->_redirect('*/*/edit', array('id' => $rule->getId()));            
        } 
        catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            return $this->_redirect('*/*');
        } 
        
        //unreachable 
        return $this->_redirect('*/*'); 
    }       
		
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam($this->_modelName . 's');
        if (!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affirm')->__('Please select records'));
             $this->_redirect('*/*/');
             return;
        }
         
        try {
            foreach ($ids as $id) {
                $model = Mage::getModel('affirm/' . $this->_modelName)->load($id);
                $model->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully deleted', count($ids)
                )
            );
        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
        
    }
    
    public function massActivateAction()
    {
        return $this->_modifyStatus(1);
    }
    
    public function massInactivateAction()
    {
        return $this->_modifyStatus(0);
    }     
    
    protected function _modifyStatus($status)
    {
        $ids = $this->getRequest()->getParam('rules');
        if ($ids && is_array($ids)){
            try {
                Mage::getModel('affirm/' . $this->_modelName)->massChangeStatus($ids, $status);
                $message = $this->__('Total of %d record(s) have been updated.', count($ids));
                $this->_getSession()->addSuccess($message);
            } 
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        else {
            $this->_getSession()->addError($this->__('Please select rule(s).'));
        }
        
        return $this->_redirect('*/*');
    }     
    
    public function prepareForSave($model)
    {
        $fields = array('stores', 'cust_groups', 'methods');
        foreach ($fields as $f){
            // convert data from array to string
            $val = $model->getData($f);
            $model->setData($f, '');
            if (is_array($val)){
                // need commas to simplify sql query
                $model->setData($f, ',' . implode(',', $val) . ',');    
            } 
        }
        
        return true;
    }
    
    public function prepareForEdit($model)
    {
        $fields = array('stores', 'cust_groups', 'methods');
        foreach ($fields as $f){
            $val = $model->getData($f);
            if (!is_array($val)){
                $model->setData($f, explode(',', $val));    
            }        
        }
        
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        return true;
    }
    
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel($this->_modelName . '/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
    
    /**
     * Chooser source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $chooserBlock = $this->getLayout()->createBlock('adminhtml/promo_widget_chooser', '', array(
            'id' => $uniqId
        ));
        $this->getResponse()->setBody($chooserBlock->toHtml());
    }       
    
    protected function _title($text = null, $resetIfExists = true)
    {
        return parent::_title($text, $resetIfExists);
    }     
}