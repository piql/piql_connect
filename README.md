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
  composer npm docker.io docker-compose
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

To enable feedback from Archivematica you will need to do some preparations before running the build script.
The storage location identifier you will find by logging in to the **Archivematica Storage Service** via your browser. Navigate to the **Locations** tab and copy the UUID for **Transfer Source**. Export the variables to your shell:

```
$ export STORAGE_LOCATION_ID=[UUID]
$ export UPDATE_AM_SERVICE_CALLBACKS=true
```

You also need to add two callbacks to the **Archivematica Storage Service** (note that only the event differs between the two setups and that two empty headers need to be created). Navigate to the **Administration** tab and click **Service callbacks**. Then add the new callbacks by clicking **Create new callback** and use the following settings:

```
Event: Post-store AIP / Post-store DIP
URI: -
Method: POST
Headers 1:
Headers 2:
Body:
Expected status: 200
```

Now, go to the deploy/ folder, and run the build script:

```
$ cd deploy/
$ ./automated-build-connect.sh
```

If all goes well, you should now after the build process, have a working development environment for piqlConnect, and should be able to type:


```
$ npm run dev

```

To get piqlConnect rebuilt from your local shell.

