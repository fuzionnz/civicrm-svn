# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
from com.bitmechanic.maxq import DBUtil
import commonConst, commonAPI
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testDeleteRelByRelTab(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')

        drupal_path = commonConst.DRUPAL_PATH

        commonAPI.login(self)
        
        queryCA    = 'select id from crm_contact where sort_name=\'Zope, Manish\' and contact_type=\'Individual\''
        queryCB    = 'select id from crm_contact where sort_name=\'Zope House\' and contact_type=\'Household\''
        contactA   = db.loadVal(queryCA)
        contactB   = db.loadVal(queryCB)
        
        if contactA :
            CID = '''%s''' % contactA
            if contactB :
                queryRID = 'select id from crm_relationship where contact_id_a=%s and contact_id_b=%s' % (contactA, contactB)
                relID    = db.loadVal(queryRID)
                RID      = '''%s''' % relID
                print relID

                params = [
                    ('''reset''', '''1'''),
                    ('''cid''', CID),]
                url = "%s/civicrm/contact/view" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 1 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)

                url = "%s/civicrm/contact/view/rel" % drupal_path
                self.msg("Testing URL: %s" % url)
                params = None
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 3 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                params = [
                    ('''action''', '''delete'''),
                    ('''rid''', RID),
                    ('''rtype''', '''b_a'''),]
                url = "%s/civicrm/contact/view/rel" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                if relID :
                    print ("**************************************************************************************")
                    print "Relationship between \" \'Zope, Manish\' and \'Zope House\' \" is Deleted Successfully"
                    print ("**************************************************************************************")
                else :
                    print ("**************************************************************************************")
                    print "Some Problem while Deleting Relationship between \" \'Zope, Manish\' and \'Zope House\' \"."
                    print ("**************************************************************************************")
            else :
                print ("**************************************************************************************")
                print " Household \'Zope House\' do not Exists"
                print ("**************************************************************************************")
        else :
            print ("**************************************************************************************")
            print " Individual \'Zope, Manish\' do not Exists"
            print ("**************************************************************************************")
                
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testDeleteRelByRelTab("testDeleteRelByRelTab")
    test.Run()
