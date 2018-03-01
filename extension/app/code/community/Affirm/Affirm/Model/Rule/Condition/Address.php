<?php
class Affirm_Affirm_Model_Rule_Condition_Address extends Mage_SalesRule_Model_Rule_Condition_Address
{
    public function loadAttributeOptions()
    {
        parent::loadAttributeOptions();
        
        $attributes = $this->getAttributeOption();
        unset($attributes['payment_method']);
        $attributes['street'] = Mage::helper('salesrule')->__('Address Line');
        $attributes['city'] = Mage::helper('salesrule')->__('City');

        
        $this->setAttributeOption($attributes);

        return $this;
    }
    
    public function getOperatorSelectOptions()
    {
        $operators = $this->getOperatorOption();
        if ($this->getAttribute() == 'street') {
             $operators = array(
                '{}'  => Mage::helper('rule')->__('contains'),
                '!{}' => Mage::helper('rule')->__('does not contain'),             
             );
        }
        
        $type = $this->getInputType();
        $opt = array();
        $operatorByType = $this->getOperatorByInputType();
        foreach ($operators as $k => $v) {
            if (!$operatorByType || in_array($k, $operatorByType[$type])) {
                $opt[] = array('value' => $k, 'label' => $v);
            }
        }
        return $opt;
    }
    
    public function getDefaultOperatorInputByType()
    {
        $op = parent::getDefaultOperatorInputByType();
        $op['string'][] = '{%';
        $op['string'][] = '%}';
        return $op;
    }

    public function getDefaultOperatorOptions()
    {
        $op = parent::getDefaultOperatorOptions();
        $op['{%'] = Mage::helper('rule')->__('starts from');
        $op['%}'] = Mage::helper('rule')->__('ends with');        

        return $op;
    }    
    
    public function validateAttribute($validatedValue)
    {
        if (is_object($validatedValue)) {
            return false;
        }

        /**
         * Condition attribute value
         */
        $value = $this->getValueParsed();

        /**
         * Comparison operator
         */
        $op = $this->getOperatorForValidate();

        // if operator requires array and it is not, or on opposite, return false
        if ($this->_isArrayOperatorType() xor is_array($value)) {
            return false;
        }

        $result = false;
        switch ($op) {
            case '{%':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    Mage::log(substr($validatedValue,0,strlen($value)));
                    Mage::log($value);
                    $result = substr($validatedValue,0,strlen($value)) == $value;
                }             
                break;
             case '%}':
                if (!is_scalar($validatedValue)) {
                    return false;
                } else {
                    $result = substr($validatedValue,-strlen($value)) == $value;
                }               
                break;  
             default:
                return parent::validateAttribute($validatedValue);
                break;        
        }
        return $result;        
                
    }
    
    /**
     * Check if value should be array
     *
     * Depends on operator input type
     *
     * @return bool
     */
    protected function _isArrayOperatorType()
    {
        $ret = false;
        if (method_exists($this, 'isArrayOperatorType')){
            $ret = $this->isArrayOperatorType();
        } else {
            $op  = $this->getOperator();
            $ret = ($op === '()' || $op === '!()');
        }
         
        return $ret;
    }
}