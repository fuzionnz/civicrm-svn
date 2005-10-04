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
class testAdminDeleteRel(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')
        
        drupal_path = commonConst.DRUPAL_PATH
        
        commonAPI.login(self)
        
        name       = '\'Owner Of\''
        query      = 'select id from civicrm_relationship_type where name_a_b=%s' % name  
        relationID = db.loadVal(query)
        
        params = [
            ('''reset''', '''1'''),]
        url = "%s/civicrm/admin" % drupal_path
        self.msg("Testing URL : %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        url = "%s/civicrm/admin/reltype" % drupal_path
        self.msg("Testing URL : %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        if relationID :
            RID = '''%s''' % relationID 
            params = [
                ('''action''', '''delete'''),
                ('''id''', RID),]
            url = "%s/civicrm/admin/reltype" % drupal_path
            self.msg("Testing URL : %s" % url)
            Validator.validateRequest(self, self.getMethod(), "get", url, params)
            self.get(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            
            params = [
                ('''_qf_default''', '''RelationshipType:next'''),
                ('''_qf_RelationshipType_next''', '''Delete'''),]
            url = "%s/civicrm/admin/reltype" % drupal_path
            self.msg("Testing URL : %s" % url)
            Validator.validateRequest(self, self.getMethod(), "post", url, params)
            self.post(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 10 failed", 302, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            
            params = [
                ('''reset''', '''1'''),
                ('''action''', '''browse'''),]
            url = "%s/civicrm/admin/reltype" % drupal_path
            self.msg("Testing URL : %s" % url)
            Validator.validateRequest(self, self.getMethod(), "get", url, params)
            self.get(url, params)
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 11 failed", 200, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
            print ("**************************************************************************************")
            print ("Relationship %s Deleted Successfully" % name)
            print ("**************************************************************************************")
        else :
            print ("**************************************************************************************")
            print ("Relationship %s Does not Exists" % name)
            print ("**************************************************************************************")
        
        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAdminDeleteRel("testAdminDeleteRel")
    test.Run()
