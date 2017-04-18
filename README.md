# Tree Stories

## Installation

To install, just copy the config file over to wherever you keep your vhosts.
Edit the paths shown in the file and restart the service.

    cp treestories.conf.example /etc/httpd/vhost.d/treestories.conf
    vi /etc/httpd/vhost.d/treestories.conf
    service httpd restart

You will also need to load the database schema.
    mysqld treestories < treestories.sql

## DOCKER installation and serving
A DockerFile and docker-compose file are provided. To build a php
webserver container and a db container run:

    docker-compose up

This will build and launch the containers, load the db schema, and run a
development PHP server on port 8000 (localhost:8000)

## Tree Data
You will need to copy the GEOJson files from the server in order to
serve the tree data.

    mkdir data && cd data
    wget -r --no-parent https://climatecope.research.pdx.edu/cs/data/

## Social Media

### Facebook

There is a Facebook application configured for the web application. It must be configured to point
to the urls of the staging and production sites. This must be done via facebook by one of the pre-approved
site administrators. There are a few settings that will need to be changed

- Accepted redirect URI's
- Accepted OAuth Callback URI's
- Permissions

To do this, go to developers.facebook.com and login with your facebook account. Your account will have to
be approved as a developer account before you will be able to make changes. Once you have done this, navigate to
the "settings" tab in the left panel, and change the Site URL to match the URL configured for the application.
You will also need to activate the following OAuth settings in the "Advanced" tab:

- Client OAuth Login
- Web OAuth Login
- Embedded Browser OAuth Login
- Force Web OAuth Reauthentication.

You will also need to add the site's base url as a valid OAuth redirect URI.


### Google

A google application has been configured to allow single sign on (SSO) through their federated SSO client.
There are a few settings that will have to be modified as well in order to be able to use the application.
Settings can only be modified by pre-apprived administrators through the google interface. The settings are

- Accepted redirect URI's
- Accepted OAuth Callback URI's
- Permissions
