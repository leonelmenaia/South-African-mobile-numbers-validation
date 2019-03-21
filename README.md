OLX Backend Challenge
==============

This challenge consists on a basic API that can validate phone numbers.

## Table of Contents

* [Local Development](#local-development)
* [Running on Cloud](#running-on-cloud)
* [API consumption](#api-consumption)
  * [Client communication with the API](#client-communication-with-the-api)
  * [Client API Authorization](#client-api-authorization)
* [Shortcuts](#shortcuts)


## Local Development

You need [Docker](https://docker.com) installed first.

The local project is meant to be run through docker-compose to provide a seamless development environment between every developer machine.

### Before running the project


### Running the project

Run

`make start`

to initiate the following:
* NGINX server
* PHP7
* Database
* Test Database
* PHPMYAdmin

## Shortcuts

| Shortcut                       | Description                                                                                                                                      |
| :---                           | :---                                                                                                                                              |
| make start                     |  Start the development environment.                                                                                                               |
| make stop                      |  Stop the development environment.                                                                                                                |
| make restart                   |  Restart the development environment.                                                                                                             |
| make build                     |  Run if you changed any of the services initialization scripts. Will rebuild the docker-composer services.                              |
| make build-clean               |  Run if you changed any of the services initialization scripts. Will ignore cached images and rebuild the docker-composer services.              |
| make test                      |  Run automated tests.                                                                  |
| make space                     |  Remove all docker cached images.                                                                |