const assert = require('assert');
describe('When open piql connect', () => {
	const {
        Builder,
        By,
        Key,
		Keys,
        until
    } = require('selenium-webdriver');
	var driver;
	
	beforeEach(() => {
        driver = new Builder()
            .forBrowser('chrome')
            .build();
    });

	afterEach(() => {
        driver.quit();
    });	

	it('should be possible to ingest files', async () => {
		var packageName = 'pkg-'+Math.floor(Math.random() * 1000);
        await driver.get('http://piqlconnect_nginx_1');
		var usernameObj = await driver.wait(until.elementLocated(By.name('username')), 10000);
		usernameObj.sendKeys('kare');
		var passInputObj = driver.findElement(By.name('password'));
		passInputObj.sendKeys('kare');
		await sleep(3000);
		await passInputObj.sendKeys(Key.ENTER);
		var poweredByObj = await driver.wait(until.elementLocated(By.className("poweredBy")), 10000);

		await driver.findElement(By.id('navbarIngest')).click();
		await sleep(3000);
		var bagNameObj = await driver.wait(until.elementLocated(By.id('bagName')), 10000);
		bagNameObj.sendKeys(packageName);
		await sleep(3000);
		var fileObj = driver.findElement(By.xpath("//input[@type='file']"));
		fileObj.sendKeys('/var/log/bootstrap.log');
		await sleep(3000);
		var bagNameObj = await driver.wait(until.elementLocated(By.xpath("//div[@class='toast-body' and contains(string(), 'bootstrap.log')]")), 10000);

		await driver.findElement(By.id('processButton')).click();
		await sleep(3000);
		
		var bagNameObj = await driver.wait(until.elementLocated(By.xpath("//div[@class='toast-body' and contains(string(), 'queue')]")), 10000);
		
		await driver.findElement(By.id('sideBarProcessing')).click();

		var progressbarObj = await driver.wait(until.elementLocated(By.xpath("//div[@role='progressbar']")), 10000);
		var bagObj = driver.findElement(By.className("processingPackageName"));
		
		await driver.executeScript("return arguments[0].innerHTML;", bagObj).then(function (returnValue) {
			assert.ok(returnValue.includes(packageName));
		});
    });
});

function waitRender(driver) {
	driver.wait(function() {
	  return driver.executeScript('return document.readyState').then(function(readyState) {
	    return readyState === 'complete';
	  });
	});
}

function sleep(ms) {
  return new Promise((resolve) => {
    setTimeout(resolve, ms);
  });
}  
