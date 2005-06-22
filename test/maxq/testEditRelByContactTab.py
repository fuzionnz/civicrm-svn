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
class testEditRelByContactTab(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')

        drupal_path = commonConst.DRUPAL_PATH

        commonAPI.login(self)

        nameI      = 'Zope, Manish'
        nameH      = 'Zope House'
        queryCA    = 'select id from crm_contact where sort_name like \'%s\' and contact_type=\'Individual\'' % nameI
        contactIID = db.loadVal(queryCA)
        
        if contactIID :
            CID = '''%s''' % contactIID
            queryCB    = 'select id from crm_contact where sort_name like \'%s\' and contact_type=\'Household\'' % nameH
            contactHID = db.loadVal(queryCB)
            
            if contactHID :
                queryRID  = 'select id from crm_relationship where contact_id_a=%s and contact_id_b=%s' % (contactIID, contactHID)
                relID      = db.loadVal(queryRID)
                queryRTID  = 'select relationship_type_id from crm_relationship where contact_id_a=%s and contact_id_b=%s' % (contactIID, contactHID)
                relTID      = db.loadVal(queryRTID)
                if relTID == '7' :
                    RTID = '''6_a_b'''
                else :
                    RTID = '''7_a_b'''
                        
                RID = '''%s''' % relID 

                params = [
                    ('''reset''', '''1'''),
                    ('''cid''', CID),]
                url = "%s/civicrm/contact/view" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                
                params = [
                    ('''action''', '''update'''),
                    ('''rid''', RID),
                    ('''rtype''', '''b_a'''),]
                url = "%s/civicrm/contact/view/rel" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "get", url, params)
                self.get(url, params)
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
        
                params = [
                    ('''_qf_default''', '''Relationship:next'''),
                    ('''relationship_type_id''', RTID),
                    ('''start_date[d]''', '''24'''),
                    ('''start_date[M]''', '''10'''),
                    ('''start_date[Y]''', '''1998'''),
                    ('''end_date[d]''', '''12'''),
                    ('''end_date[M]''', '''12'''),
                    ('''end_date[Y]''', '''2022'''),
                    ('''_qf_Relationship_next''', '''Save Relationship'''),]
                url = "%s/civicrm/contact/view/rel" % drupal_path
                self.msg("Testing URL: %s" % url)
                Validator.validateRequest(self, self.getMethod(), "post", url, params)
                self.post(url, params)
                if relTID :
                    self.msg("Response code: %s" % self.getResponseCode())
                    self.assertEquals("Assert number 7 failed", 302, self.getResponseCode())
                    Validator.validateResponse(self, self.getMethod(), url, params)

                    params = [
                        ('''action''', '''browse'''),]
                    url = "%s/civicrm/contact/view/rel" % drupal_path
                    self.msg("Testing URL: %s" % url)
                    Validator.validateRequest(self, self.getMethod(), "get", url, params)
                    self.get(url, params)
                    self.msg("Response code: %s" % self.getResponseCode())
                    self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
                    Validator.validateResponse(self, self.getMethod(), url, params)
                else :
                    self.msg("Response code: %s" % self.getResponseCode())
                    self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
                    Validator.validateResponse(self, self.getMethod(), url, params)
                    print ("**************************************************************************************")
                    print " No Relationship Between \'%s\' and \'%s\'" % (nameI, nameH)
                    print ("**************************************************************************************")
            else :
                print ("**************************************************************************************")
                print " Household \'%s\' do not Exists" % nameH
                print ("**************************************************************************************")
        else :
            print ("**************************************************************************************")
            print " Individual \'%s\' do not Exists" % nameI
            print ("**************************************************************************************")
                        
                    
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testEditRelByContactTab("testEditRelByContactTab")
    test.Run()
