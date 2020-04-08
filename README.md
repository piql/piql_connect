# piqlConnect

**piqlConnect** is an interface for a secure and reliable offline and online digital repository solution complying with the OAIS (Open Archival Information System) repository reference model. 

This service is offered to clients for data ingestion, preservation and access.




## Features



## Getting started



### Requirements

Ubuntu 18.04 or similar operating system. Run the following to get prerequisites installed:
sudo apt-get install php7.2-cli composer npm docker.io docker-compose

An installation of Archivematica ( git@github.com:artefactual/archivematica.git ) set up using docker-compose (See Archivematica Documentation)

### Installation

### Installing the development environment

First of all, make sure your user is member of the docker group, by for example issuing:

```
sudo usermod -aG docker $USER
```

This allows you to work with dockers as a normal user.

Now, go to the deploy/ folder, and run the build script:

```
cd deploy/
./automated-build-connect.sh
```

If all goes well, you should now after the build process, have a working development environment for piqlConnect, and should be able to type:


```
npm run dev

```

To get piqlConnect rebuilt from your local shell.

