module.exports = {
    'Test Login' : function(browser) {
      browser
        .url('http://localhost/')
        .assert.urlContains('https://auth.piqlconnect.com')
        .waitForElementVisible('#kc-page-title')
        .assert.containsText('#kc-page-title','Log In')
        .assert.visible('input[name=username]')
        .setValue('input[name=username]', 'kare')
        .assert.visible('input[type=password]')
        .setValue('input[type=password]', 'kare')
        .assert.visible('input[type=submit]')
        .click('input[type=submit]')
        .waitForElementVisible('.titleText')
        .assert.containsText('.titleText','DASHBOARD')
        .end();
    }
  };