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
class testAdminAddTitle(PyHttpTestCase):
    def setUp(self):
        global db
        db = commonAPI.dbStart()
    
    def tearDown(self):
        commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')
        
        drupal_path = commonConst.DRUPAL_PATH
        
        commonAPI.login(self)
        
        params = [
            ('''reset''', '''1'''),]
        url = "%s/civicrm/admin" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        url = "%s/civicrm/admin/prefix" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''action''', '''add'''),
            ('''reset''', '''1'''),]
        url = "%s/civicrm/admin/prefix" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        queryName = 'select name from civicrm_individual_prefix'          
        queryID   = 'select max(id) from civicrm_individual_prefix'
        titleName = db.loadRows(queryName)
        titleNum  = db.loadVal(queryID)
        
        params = [
            ('''_qf_default''', '''IndividualPrefix:next'''),
            ('''name''', '''New Prefix'''),
            ('''weight''', '''4'''),
            ('''is_active''', '''1'''),
            ('''_qf_IndividualPrefix_next''', '''Save'''),]
        url = "%s/civicrm/admin/prefix" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        for i in range(int(titleNum)) :
            if titleName[i].values()[0] == params[1][1] :
                print ("**************************************************************************************")
                print ("Title \'" + titleName[i].values()[0] + "\' already exists")
                print ("**************************************************************************************")
                self.msg("Response code: %s" % self.getResponseCode())
                self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
                Validator.validateResponse(self, self.getMethod(), url, params)
                break
            else :
                continue
        else :
            print ("**************************************************************************************")
            print ("Title \'" + params[1][1] + "\' Added")
            print ("**************************************************************************************")
            self.msg("Response code: %s" % self.getResponseCode())
            self.assertEquals("Assert number 11 failed", 302, self.getResponseCode())
            Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''reset''', '''1'''),
            ('''action''', '''browse'''),]
        url = "%s/civicrm/admin/prefix" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 12 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        commonAPI.logout(self)
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAdminAddTitle("testAdminAddTitle")
    test.Run()
