<?php
require_once 'Common.php';
require_once 'GenDataset.php';
require_once 'InsertContact.php';
require_once 'InsertRel.php';
require_once 'UpdateContact.php';
require_once 'AddContactToGroup.php';

class test_RSTest_Run
{
    private $_recordSetSize;

    // This constant is used to set size fo dataset. 
    // The value entered is multiple of 1000 or 1k
    // Ex. if the constant value is 10 then dataset size will be (1000 * 10)
    private $_sizeOfDS      = 5;
    // Following constant is used for setting the step for generating the dataset.
    private $_stepOfDS      = 500;
    // Following array is used to store the timings for all the steps of generating the dataset.
    private $_genDataset    = array();
    
    // Following constant is used for setting the no of records to be inserted into the database.
    // Value should be multiple of 10.
    private $_insertRecord  = 1000;
    // Following constant is used for setting the step for inserting the contact. 
    private $_stepOfInsert  = 10;
    // Following array is used to store the timings for all the steps of inserting the contact.
    private $_insertContact = array();
    
    // Following constant is used for setting the no of records to be updated from the database. 
    private $_updateRecord  = 4000;
    // Following constant is used for setting the starting record from which update should start. 
    private $_startRecord   = 1000;
    // Following constant is used for setting the step for updaing the contacts.
    private $_stepOfUpdate  = 500;
    // Following array is used to store the timings for all the steps of updations. 
    private $_updateContact = array();
    
    // Following constant is used for setting the no of contact for which relationships needs to be entered
    private $_insertRel        = 3500;
    // Following constant is used for setting the starting contact from which the relationships needs to be entered.
    private $_startRel         = 500;
    // Following constant is used for setting the step for inserting relationships.
    private $_stepOfInsertRel  = 500;
    // Following array is used to store the timings for all the steps of updations. 
    private $_insertRelTime    = array();
    
    // Following constant is used for setting the no of Contacts which needs to be added to a Group. 
    private $_addToGroup       = 4000;
    // Following constant is used for setting the starting contact from which Contacts needs to be added to a Group.
    private $_startOfAdd       = 500;
    // Following constant is used for setting the step for adding Contact to a Group.
    private $_stepOfAddToGroup = 500;
    // Following array is used to store the timings for all the steps of adding Contact to a Group. 
    private $_addToGroupTime   = array();
    
    private $_startTimeG;
    private $_endTimeG;
    
    private $_startTimeIC;
    private $_endTimeIC;
    
    private $_startTimeUC;
    private $_endTimeUC;
    
    private $_startTimeIR;
    private $_endTimeIR;

    private $_startTimeAG;
    private $_endTimeAG;
    
    function callCommon()
    {
        $objCommon            = new test_RSTest_Common();
        $this->_recordSetSize = $objCommon->recordsetSize($this->_sizeOfDS);
    }

    function callGenDataset()
    {
        $startID = 0;
        for ($i=0; $i<($this->_recordSetSize / $this->_stepOfDS); $i++) {
            $objGenDataset       =& new test_RSTest_GenDataset($this->_stepOfDS);
            $this->_startTimeG   = microtime(true);
            $objGenDataset->run($startID);
            $this->_endTimeG     = microtime(true);
            $this->_genDataset[$i] = $this->_endTimeG - $this->_startTimeG;
            $startID = $startID + $this->_stepOfDS;
        }
    }
    
    function callInsertContact()
    {
        $startID = 0;
        for ($i=0; $i<($this->_insertRecord / $this->_stepOfInsert); $i++) {
            if (!($i)) {
                $setDomain = true;
            }
            $objInsertContact  = new test_RSTest_InsertContact($this->_stepOfInsert);
            $this->_startTimeIC = microtime(true);
            $objInsertContact->run($this->_recordSetSize, $startID);
            $this->_endTimeIC   = microtime(true);
            $this->_insertContact[$i] = $this->_endTimeIC - $this->_startTimeIC;
            $startID = $startID + $this->_stepOfInsert;
        }
    }
    
    function callInsertRel()
    {
        if (($this->_startRel + $this->_insertRel) <= ($this->_sizeOfDS * 1000)) {
            $startID = $_startRel;
            for ($i=0; $i<($this->_insertRel / $this->_stepOfInsertRel); $i++) {
                $objInsertRel  = new test_RSTest_InsertRel();
                $this->_startTimeIR = microtime(true);
                $objInsertRel->run($startID, $this->_stepOfInsertRel);
                $this->_endTimeIR   = microtime(true);
                $this->_insertRelTime[$i] = $this->_endTimeIR - $this->_startTimeIR;
                $startID = $startID + $this->_stepOfInsertRel;
            }
        } else {
            echo "\n**********************************************************************************\n";
            echo "Check the number of Contacts for which Relationships are to be inserted..!!! \n";
            echo "**********************************************************************************\n";
        }
    }
    
    function callUpdateContact()
    {
        if (($this->_startRecord + $this->_updateRecord) <= ($this->_sizeOfDS * 1000)) {
            $startID = $this->_startRecord;
            for ($i=0; $i<($this->_updateRecord / $this->_stepOfUpdate); $i++) {
                $objUpdateContact   = new test_RSTest_UpdateContact($this->_stepOfUpdate);
                $this->_startTimeUC = microtime(true);
                $objUpdateContact->run($startID, $this->_stepOfUpdate);
                $this->_endTimeUC   = microtime(true);
                $this->_updateContact[$i] = $this->_endTimeUC - $this->_startTimeUC;
                $startID = $startID + $this->_stepOfUpdate;
            }
        } else {
            echo "\n**********************************************************************************\n";
            echo "Check the number of records needs to be Updated..!!! \n";
            echo "**********************************************************************************\n";
        }
    }
    
    function callAddContactToGroup()
    {
        if (($this->_startOfAdd + $this->_addToGroup) <= ($this->_sizeOfDS * 1000)) {
            $startID = $this->_startOfAdd;
            for ($i=0; $i<($this->_addToGroup / $this->_stepOfAddToGroup); $i++) {
                $objAddContactToGroup   = new test_RSTest_AddContactToGroup($this->_stepOfAddToGroup);
                $this->_startTimeAG = microtime(true);
                $objAddContactToGroup->run($startID, $this->_stepOfAddToGroup);
                $this->_endTimeAG   = microtime(true);
                $this->_addToGroupTime[$i] = $this->_endTimeAG - $this->_startTimeAG;
                $startID = $startID + $this->_stepOfAddToGroup;
            }
        } else {
            echo "\n**********************************************************************************\n";
            echo "Check the number of Contacts needs to be added to Groups..!!! \n";
            echo "**********************************************************************************\n";
        }
    }


    function printResult()
    {
        echo "\n**********************************************************************************\n";
        echo "Recordset of Size " . ($this->_recordSetSize / 1000) . " K is Generated. Records were generated through the step of " . $this->_stepOfDS . " Contacts \n";
        for ($ig=0; $ig<count($this->_genDataset); $ig++) {
            echo "Time taken for step " . ($kg = $ig + 1) . " : " . $this->_genDataset[$ig] . " seconds\n";
        }
        echo "**********************************************************************************\n";
                
        echo "\n**********************************************************************************\n";
        echo $this->_insertRecord . " Contact(s) Inserted into the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfInsert . " Contacts \n";
        
        for ($ii=0; $ii<count($this->_insertContact); $ii++) {
            echo "Time taken for step " . ($ki = $ii + 1) . " : " . $this->_insertContact[$ii] . " seconds\n";
        }
        echo "**********************************************************************************\n";
        
        echo "\n**********************************************************************************\n";
        echo "Relationships entered for Contact No. " . $this->_startRel . " To Contact No. " . ($this->_startRel + $this->_insertRel) . " From the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfInsertRel . " Contacts \n";
        
        for ($ir=0; $ir<count($this->_insertRelTime); $ir++) {
            echo "Time taken for step " . ($kr = $ir + 1) . " : " . $this->_insertRelTime[$ir] . " seconds\n";
        }
        echo "**********************************************************************************\n";
        
        echo "\n**********************************************************************************\n";
        echo "Contact No. " . $this->_startRecord . " To Contact No. " . ($this->_startRecord + $this->_updateRecord) . " Updated from the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfUpdate . " Contacts \n";
        
        for ($iu=0; $iu<count($this->_updateContact); $iu++) {
            echo "Time taken for step " . ($ku = $iu + 1) . " : " . $this->_updateContact[$iu] . " seconds\n";
        }
        echo "**********************************************************************************\n";
        
        echo "\n**********************************************************************************\n";
        echo "Contact No. " . $this->_startOfAdd . " To Contact No. " . ($this->_startOfAdd + $this->_addToGroup) . " Added to Groups from the dataset of size " . ($this->_recordSetSize / 1000) . " K through the step of " . $this->_stepOfAddToGroup . " Contacts \n";
        
        for ($iag=0; $iag<count($this->_addToGroupTime); $iag++) {
            echo "Time taken for step " . ($kag = $iag + 1) . " : " . $this->_addToGroupTime[$iag] . " seconds\n";
        }
        echo "**********************************************************************************\n";

        echo "\n";
    }
}

$objRun =& new test_RSTest_Run();
echo "**********************************************************************************\n";
echo "Stress Test Started \n";
echo "**********************************************************************************\n";
$objRun->callCommon();
$objRun->callGenDataset();
$objRun->callInsertContact();
$objRun->callUpdateContact();
$objRun->callInsertRel();
$objRun->callUpdateContact();
$objRun->callAddContactToGroup();
$objRun->printResult();

?>