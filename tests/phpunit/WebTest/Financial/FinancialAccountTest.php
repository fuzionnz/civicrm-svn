<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once 'CiviTest/CiviSeleniumTestCase.php';
class WebTest_Financial_FinancialAccountTest extends CiviSeleniumTestCase {

  function testFinancialAccount() {
    // To Add Financial Account 
    // class attributes.
    $this->open( $this->sboxPath );
    
    // Log in using webtestLogin() method
    $this->webtestLogin();
    
    // Add new Financial Account
    $orgName = 'Alberta '.substr(sha1(rand()), 0, 7);
    $financialAccountTitle = 'Financial Account '.substr(sha1(rand()), 0, 4);
    $financialAccountDescription = "{$financialAccountTitle} Description";
    $accountingCode = 1033;
    $financialAccountType = 'Expenses';
    $taxDeductible = FALSE;
    $isActive = TRUE;
    $isTax = TRUE;
    $taxRate = 9.99999999;
    $isDefault = FALSE;
        
    //Add new organisation
    if($orgName) {
      $this->webtestAddOrganization($orgName);
    }
        
    $this->_testAddFinancialAccount($financialAccountTitle,
      $financialAccountDescription,
      $accountingCode,
      $orgName,
      $financialAccountType,
      $taxDeductible,
      $isActive,
      $isTax,
      $taxRate,
      $isDefault
    );
        
    $this->waitForElementPresent("xpath=//table/tbody//tr/td[1][text()='{$financialAccountTitle}']/../td[9]/span/a[text()='Edit']");
        
    $this->click("xpath=//table/tbody//tr/td[1][text()='{$financialAccountTitle}']/../td[9]/span/a[text()='Edit']");
    $this->waitForPageToLoad($this->getTimeoutMsec());
    $this->waitForElementPresent('_qf_FinancialAccount_cancel-botttom');
    //Varify Data after Adding new Financial Account
    $verifyData = array('name' => $financialAccountTitle,
      'description' => $financialAccountDescription,
      'accounting_code' => $accountingCode,
      'contact_name' => $orgName,
      'tax_rate'   => $taxRate,
      'is_tax' => 'on',
      'is_deductible' => 'off',
      'is_default' => 'off'
    );
      
    $this->_assertFinancialAccount($verifyData);
    $verifySelectFieldData = array('financial_account_type_id' => $financialAccountType);
    $this->_assertSelectVerify($verifySelectFieldData);
    $this->click('_qf_FinancialAccount_cancel-botttom');
        
    //Edit Financial Account
    $editfinancialAccount = $financialAccountTitle;
    $financialAccountTitle .= ' Edited';
    $orgNameEdit = FALSE;
    $financialAccountType = 'Revenue';
        
    if ($orgNameEdit) {
      $orgNameEdit = 'NGO '.substr(sha1(rand()), 0, 7);
      $this->webtestAddOrganization($orgNameEdit);
    }
        
    $this->_testEditFinancialAccount($editfinancialAccount,
      $financialAccountTitle,
      $financialAccountDescription,
      $accountingCode,
      $orgNameEdit,
      $financialAccountType,
      $taxDeductible,
      $isActive,
      $isTax,
      $taxRate,
      $isDefault
    ); 
    if($orgNameEdit) {
      $orgName = $orgNameEdit;
    }
    $this->waitForElementPresent("xpath=//table/tbody//tr/td[1][text()='{$financialAccountTitle}']/../td[9]/span/a[text()='Edit']");
    $this->click("xpath=//table/tbody//tr/td[1][text()='{$financialAccountTitle}']/../td[9]/span/a[text()='Edit']");
    $this->waitForPageToLoad($this->getTimeoutMsec());
    $this->waitForElementPresent('_qf_FinancialAccount_cancel-botttom');
       
    $verifyData = array( 'name' => $financialAccountTitle,
      'description' => $financialAccountDescription,
      'accounting_code' => $accountingCode,
      'contact_name' => $orgName,
      'tax_rate'   => $taxRate,
      'is_tax' => 'on',
      'is_deductible' => 'off',
      'is_default' => 'off',
    );
    $verifySelectFieldData = array('financial_account_type_id'   => $financialAccountType);
    $this->_assertFinancialAccount($verifyData);
    $this->_assertSelectVerify($verifySelectFieldData);
    $this->click('_qf_FinancialAccount_cancel-botttom');
    $this->waitForElementPresent("xpath=//table/tbody//tr/td[1][text()='{$financialAccountTitle}']/../td[9]/span/a[text()='Delete']");
        
    //Delete Financial Account
    $this->_testDeleteFinancialAccount($financialAccountTitle);
  }
}
