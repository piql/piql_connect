# piqlConnect

**piqlConnect** is a software platform for secure and reliable online digital preservation and long term storage based on the Open Archival Information System.


## Features

- Fully integrated with Archivematica supporting industry standard, OAIS compliant archival workflows
- Configurable S3 online archival storage 
- File ingest from a dedicated, web based file uploader
- Metadata editing directly from the browser
- Dashboard for user data statistics
- Access panel with previews for supported file types
- Long term storage options, including sending archives to piqlFilm
- Direct downloads from the access panel in the browser


### Requirements

Ubuntu 18.04 or similar operating system. Run the following to get prerequisites installed:
```
$ sudo apt-get install php7.2-cli php7.2-xml php7.2-mbstring php7.2-bcmath php7.2-mysql php7.2-zip \
  composer npm docker.io docker-compose python3
```
An installation of Archivematica ( https://github.com/artefactual/archivematica ) set up using docker-compose (See Archivematica Documentation)

### Installing for production environments

- TBD, probably built around Ansible






### Installing the development environment

First of all, make sure your user is member of the docker group, by for example issuing:

```
$ sudo usermod -aG docker $USER
```

This allows you to work with dockers as a normal user.

Rename the config.py.example file to config.py and edit the variables in the file to suit your environment.

To enable feedback from Archivematica you will need to do some preparations before running the build script.
The storage location identifier you will find by logging in to the **Archivematica Storage Service** via your browser. Navigate to the **Locations** tab and copy the UUID for **Transfer Source**. Add this to the appropriate variable in the config.py file.

You also need to add two callbacks to the **Archivematica Storage Service** (note that only the event differs between the two setups and that two empty headers need to be created). Navigate to the **Administration** tab and click **Service callbacks**. Then add the new callbacks by clicking **Create new callback** and use the following settings:

```
Event: Post-store AIP / Post-store DIP
URI: -
Method: POST
Headers 1 <key> / <value>: Authorization / -
Headers 2 <key> / <value>: Accept / application/json
Body:
Expected status: 200
```

Now, go to the deploy/ folder, and run the build script:

```
$ cd deploy/
$ python3 build.py
```

If all goes well, you should now after the build process, have a working development environment for piqlConnect, and should be able to access it using any modern web browser.

If you need to rebuild PiqlConnect, simply re-run the build script.


### End-to-end tests

To perform user interface tests, you must run:

```
$ cd tests/endToEnd/
$ ./ui-test.sh

```

It will open a background Chrome browser and navigate thoward some key actions in piqlConnect.

All the actions happens inside "selenium/standalone-chrome" docker image.

However it's possible to se theese actions if you change the image to a debug version.

Edit "deploy/docker-compose.yml" file and append "-debug" to "selenium/standalone-chrome-chrome" image name:

```
  selenium-chrome:
    image: selenium/standalone-chrome-chrome-debug

```

Stop the previous container and start the new one.

Now you wil be able to use VNC at 5900 port and see Chrome running. Use VNC password "secret". 