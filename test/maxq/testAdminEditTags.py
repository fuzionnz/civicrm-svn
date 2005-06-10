# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
from com.bitmechanic.maxq import DBUtil
import Common
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testAdminEditTags(PyHttpTestCase):
    def runTest(self):
        self.msg('Test started')

        drupal_path = Common.DRUPAL_PATH
        
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/''') % drupal_path)
        url = "%s/" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 1 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        params = [
            ('''edit[destination]''', '''node'''),
            ('''edit[name]''', Common.USERNAME),
            ('''edit[pass]''', Common.PASSWORD),
            ('''op''', '''Log in'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/user/login?edit[destination]=node&edit[name]=manishzope&edit[pass]=manish&op=Log in''') % drupal_path)
        url = "%s/user/login" % drupal_path
        self.msg("Testing URL: %s" % url)
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 2 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/node''') % drupal_path)
        url = "%s/node" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 3 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/search''') % drupal_path)
        url = "%s/civicrm/contact/search" % drupal_path
        self.msg("Testing URL: %s" % url)
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 4 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

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
        
        db = DBUtil("%s" % Common.MSQLDRIVER, "jdbc:mysql://%s/%s" % (Common.DBHOST, Common.DBNAME), "%s" % Common.DBUSERNAME, "%s" % Common.DBPASSWORD)

        name  = '\'Test Tag\''
        query = 'select id from crm_tag where name=%s' % name  
        tagID = db.loadVal(query)
        
        db.close()

        TID = '''%s''' % tagID 
        params = [
            ('''action''', '''update'''),
            ('''id''', TID),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/category?action=update&id=6''') % drupal_path)
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
            ('''name''', '''Test Tag Edit'''),
            ('''description''', '''This is test tag....tested for editing'''),
            ('''_qf_Category_next''', '''Save'''),]
        #self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/admin/category?_qf_default=Category:next&name=Test Tag Edit&description=This is test tag....tested for editing&_qf_Category_next=Save''') % drupal_path)
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
    test = testAdminEditTags("testAdminEditTags")
    test.Run()
