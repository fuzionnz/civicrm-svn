# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
import commonConst, commonAPI
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testAdminDeleteTags(PyHttpTestCase):
    #def setUp(self):
    #    global db
    #    db = commonAPI.dbStart()
    
    #def tearDown(self):
    #    commonAPI.dbStop(db)
    
    def runTest(self):
        self.msg('Test started')

        drupal_path = commonConst.DRUPAL_PATH

        commonAPI.login(self)

        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin''') % drupal_path)
        url = "%s/civicrm/admin" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 5 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        #url = "http://localhost/favicon.ico"
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 6 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''action''', '''delete'''),
            ('''id''', '''6'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/category?action=delete&id=6''') % drupal_path)
        url = "%s/civicrm/admin/category" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        #url = "http://localhost/favicon.ico"
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 8 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''_qf_default''', '''Category:next'''),
            ('''_qf_Category_next''', '''Delete'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/category?_qf_default=Category:next&_qf_Category_next=Delete''') % drupal_path)
        url = "%s/civicrm/admin/category" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 9 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''reset''', '''1'''),
            ('''action''', '''browse'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/category?reset=1&action=browse''') % drupal_path)
        url = "%s/civicrm/admin/category" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 10 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''http://localhost/favicon.ico'''))
        #url = "http://localhost/favicon.ico"
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 11 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)
        
        self.msg('Test successfully complete.')
    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testAdminDeleteTags("testAdminDeleteTags")
    test.Run()
