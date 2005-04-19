# Generated by MaxQ [com.bitmechanic.maxq.generator.JythonCodeGenerator]
from PyHttpTestCase import PyHttpTestCase
from com.bitmechanic.maxq import Config
global validatorPkg
if __name__ == 'main':
    validatorPkg = Config.getValidatorPkgName()
# Determine the validator for this testcase.
exec 'from '+validatorPkg+' import Validator'


# definition of test class
class testEditContactOrganization(PyHttpTestCase):
    def runTest(self):
        self.msg('Test started')

        drupal_path = self.userInput("Enter the Drupal Path (e.g. http://192.168.2.9/drupal)")
        self.msg("Testing URL: %s" % self.replaceURL('''%s/''') % drupal_path)
        url = "%s/" % drupal_path
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 1 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        params = [
            ('''edit[destination]''', '''node'''),
            ('''edit[name]''', self.userInput('Enter Drupal UserName')),
            ('''edit[pass]''', self.userInput('Enter Drupal Password')),
            ('''op''', '''Log in'''),]
        self.msg("Testing URL: %s" % self.replaceURL('''%s/drupal/user/login?edit[destination]=node&edit[name]=manishzope&edit[pass]=manish&op=Log in''') % drupal_path)
        url = "%s/user/login" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 2 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        self.msg("Testing URL: %s" % self.replaceURL('''%s/node''') % drupal_path)
        url = "%s/node" % drupal_path
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 3 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)

        params = [
            ('''reset''', '''1'''),
            ('''cid''', '''104'''),]
        self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/edit?reset=1&cid=104''') % drupal_path)
        url = "%s/civicrm/contact/edit" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 4 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        #self.msg("Testing URL: %s" % self.replaceURL('''http://192.168.2.9/favicon.ico'''))
        #url = "http://192.168.2.9/favicon.ico"
        #params = None
        #Validator.validateRequest(self, self.getMethod(), "get", url, params)
        #self.get(url, params)
        #self.msg("Response code: %s" % self.getResponseCode())
        #self.assertEquals("Assert number 5 failed", 404, self.getResponseCode())
        #Validator.validateResponse(self, self.getMethod(), url, params)
               
        params = [
            ('''organization_name''', '''Zope Organization'''),
            ('''legal_name''', '''Zope Pvt. ltd'''),
            ('''nick_name''', '''Zope Companies'''),
            ('''sic_code''', '''20'''),
            ('''privacy[do_not_phone]''', '''1'''),
            ('''preferred_communication_method''', '''Email'''),
            ('''location[1][location_type_id]''', '''2'''),
            ('''location[1][is_primary]''', '''1'''),
            ('''location[1][phone][1][phone_type]''', '''Fax'''),
            ('''location[1][phone][1][phone]''', '''67-5677832'''),
            ('''location[1][phone][2][phone_type]''', '''Phone'''),
            ('''location[1][phone][2][phone]''', '''57834556'''),
            ('''location[1][email][1][email]''', '''contact_us@zope.com'''),
            ('''location[1][email][2][email]''', '''zope@zope.com'''),
            ('''location[1][im][1][provider_id]''', '''4'''),
            ('''location[1][im][1][name]''', '''This is zope.com'''),
            ('''location[1][im][2][provider_id]''', '''5'''),
            ('''location[1][im][2][name]''', '''Welcome to zope.com'''),
            ('''location[1][address][street_address]''', '''123, Zope Garden Estates, Chandivali , Khirane road, Pune'''),
            ('''location[1][address][supplemental_address_1]''', ''''''),
            ('''location[1][address][supplemental_address_2]''', ''''''),
            ('''location[1][address][city]''', '''Pune'''),
            ('''location[1][address][state_province_id]''', '''1018'''),
            ('''location[1][address][postal_code]''', '''452630'''),
            ('''location[1][address][country_id]''', '''1101'''),
            ('''location[2][location_type_id]''', '''1'''),
            ('''location[2][phone][1][phone_type]''', '''Phone'''),
            ('''location[2][phone][1][phone]''', '''456789'''),
            ('''location[2][phone][2][phone_type]''', '''Mobile'''),
            ('''location[2][phone][2][phone]''', '''9890056443'''),
            ('''location[2][email][1][email]''', '''zope_home@zope.com'''),
            ('''location[2][email][2][email]''', '''abc@zope.com'''),
            ('''location[2][im][1][provider_id]''', '''3'''),
            ('''location[2][im][1][name]''', '''This is Zope Organization'''),
            ('''location[2][im][2][provider_id]''', '''4'''),
            ('''location[2][im][2][name]''', '''Hello'''),
            ('''location[2][address][street_address]''', '''23, Zope Villa, Parvati Nagar, Paud Road, Kothrud, Pune'''),
            ('''location[2][address][supplemental_address_1]''', ''''''),
            ('''location[2][address][supplemental_address_2]''', ''''''),
            ('''location[2][address][city]''', '''Pune'''),
            ('''location[2][address][state_province_id]''', '''1018'''),
            ('''location[2][address][postal_code]''', '''456230'''),
            ('''location[2][address][country_id]''', '''1101'''),
            ('''_qf_Edit_next''', '''Save'''),]
        self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/edit?organization_name=Zope Organization&legal_name=Zope Pvt. ltd&nick_name=Zope Companies&sic_code=20&privacy[do_not_phone]=1&preferred_communication_method=Email&location[1][location_type_id]=2&location[1][is_primary]=1&location[1][phone][1][phone_type]=Fax&location[1][phone][1][phone]=67-5677832&location[1][phone][2][phone_type]=Phone&location[1][phone][2][phone]=57834556&location[1][email][1][email]=contact_us@zope.com&location[1][email][2][email]=zope@zope.com&location[1][im][1][provider_id]=4&location[1][im][1][name]=This is zope.com&location[1][im][2][provider_id]=5&location[1][im][2][name]=Welcome to zope.com&location[1][address][street_address]=123, Zope Garden Estates, Chandivali , Khirane road, Pune&location[1][address][supplemental_address_1]=&location[1][address][supplemental_address_2]=&location[1][address][city]=Pune&location[1][address][state_province_id]=1018&location[1][address][postal_code]=452630&location[1][address][country_id]=1101&location[2][location_type_id]=1&location[2][phone][1][phone_type]=Phone&location[2][phone][1][phone]=456789&location[2][phone][2][phone_type]=Mobile&location[2][phone][2][phone]=9890056443&location[2][email][1][email]=zope_home@zope.com&location[2][email][2][email]=abc@zope.com&location[2][im][1][provider_id]=3&location[2][im][1][name]=This is Zope Organization&location[2][im][2][provider_id]=4&location[2][im][2][name]=Hello&location[2][address][street_address]=23, Zope Villa, Parvati Nagar, Paud Road, Kothrud, Pune&location[2][address][supplemental_address_1]=&location[2][address][supplemental_address_2]=&location[2][address][city]=Pune&location[2][address][state_province_id]=1018&location[2][address][postal_code]=456230&location[2][address][country_id]=1101&_qf_Edit_next=Save''') % drupal_path)
        url = "%s/civicrm/contact/edit" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "post", url, params)
        self.post(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 6 failed", 302, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        params = [
            ('''reset''', '''1'''),
            ('''cid''', '''104'''),]
        self.msg("Testing URL: %s" % self.replaceURL('''%s/civicrm/contact/view?reset=1&cid=104''') % drupal_path)
        url = "%s/civicrm/contact/view" % drupal_path
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 7 failed", 200, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        
        self.msg("Testing URL: %s" % self.replaceURL('''%s/crm/i/inform.gif''') % drupal_path)
        url = "%s/crm/i/inform.gif" % drupal_path
        params = None
        Validator.validateRequest(self, self.getMethod(), "get", url, params)
        self.get(url, params)
        self.msg("Response code: %s" % self.getResponseCode())
        self.assertEquals("Assert number 8 failed", 404, self.getResponseCode())
        Validator.validateResponse(self, self.getMethod(), url, params)
        

    # ^^^ Insert new recordings here.  (Do not remove this line.)


# Code to load and run the test
if __name__ == 'main':
    test = testEditContactOrganization("testEditContactOrganization")
    test.Run()
