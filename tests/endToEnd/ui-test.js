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
	
	before(() => {
        driver = new Builder()
            .forBrowser('chrome')
            .build();
    });

	after(() => {
        driver.quit();
	});
	it('should be possible to login', async () => {
        await driver.get('http://piqlconnect_nginx_1');
		var usernameObj = await driver.wait(until.elementLocated(By.name('username')), 10000);
		usernameObj.sendKeys('kare');
		var passInputObj = driver.findElement(By.name('password'));
		passInputObj.sendKeys('kare');
		await sleep(1000);
		var submitButtonObj = await driver.wait(until.elementLocated(By.xpath("//button[@type='submit']")), 10000);
		submitButtonObj.click();

		var poweredByObj = await driver.wait(until.elementLocated(By.className("poweredBy")), 10000);
	});

	it('should be possible to ingest files', async () => {
		var packageName = 'pkg-'+Math.floor(Math.random() * 1000);
		await driver.findElement(By.id('navbarIngest')).click();
		await sleep(3000);
		var bagNameObj = await driver.wait(until.elementLocated(By.id('bagName')), 10000);
		bagNameObj.sendKeys(packageName);
		await sleep(3000);
		var fileObj = driver.findElement(By.xpath("//input[@type='file']"));
		fileObj.sendKeys('/var/log/bootstrap.log');
		
		var fileNameObj = await driver.wait(until.elementLocated(By.xpath("//span[contains(@title, 'bootstrap.log')]")), 20000);

		var toastFileNameObj = await driver.wait(until.elementLocated(By.xpath("//div[@class='toast-body' and contains(string(), 'bootstrap.log')]")), 20000);
		await sleep(3000);

		await driver.findElement(By.id('processButton')).click();
		await sleep(3000);

		var bagNameObj = await driver.wait(until.elementLocated(By.xpath("//div[@class='toast-body' and contains(string(), 'queue')]")), 20000);
		
		await driver.findElement(By.id('sideBarProcessing')).click();

		var progressbarObj = await driver.wait(until.elementLocated(By.xpath("//div[@role='progressbar']")), 10000);
		var bagObj = driver.findElement(By.className("processingPackageName"));
		
		await driver.executeScript("return arguments[0].innerHTML;", bagObj).then(function (returnValue) {
			assert.ok(returnValue.includes(packageName));
		});
	});

	it('should be possible to access files', async () => {
		await driver.findElement(By.id('navbarAccess')).click();

		var previewBtnObj = await driver.wait(until.elementLocated(By.className("previewButton")), 10000);
		previewBtnObj.click();

		var closeBtnObj = await driver.wait(until.elementLocated(By.className("closebtn")), 10000);
		closeBtnObj.sendKeys(Key.ESCAPE);

		var openBtnObj = await driver.wait(until.elementLocated(By.className("openFiles")), 10000);
		openBtnObj.click();

		var downloadBtnObj = await driver.wait(until.elementLocated(By.className("downloadFile")), 10000);
		downloadBtnObj.click();
	});

});

function sleep(ms) {
  return new Promise((resolve) => {
    setTimeout(resolve, ms);
  });
}  
