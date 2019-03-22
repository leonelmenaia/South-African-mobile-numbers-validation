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

### Features

I used a MVC framework for PHP. 

I decided to implement the authentication using a Bearer Token. The client uses the Auth endpoint to send a username and password that will match in the database and return the Bearer JWT. After that, the client will need to use the token in the Authorization Header until it expires and the client will need to request a new one.

I decided to implement api version control. The version 1 of the API is available. If needed, it's possible to implement a new version and to keep the first one available until it's safe to disable it.

I implemented Unit tests that test specific features (model functions) of the API, and API tests, that test endpoint calling with correct params.

### Notes

I didn't implement CI/CD so the environment variables are public in the repository. This is not optimal and in a work case, the code would need to pull the variables from somewhere.

I only implemented two ways of fixing phone numbers. Removing non digits and adding the full country indicative. For me, those seem to be the ideal. It doesn't make sense to add a single country indicative number or reversing the number. It would only produce invalid numbers.

I focused on South African numbers and didn't implement other types of numbers.
### Running the project

Run

`make start`

to initiate the following:
* NGINX server (localhost:8080)
* PHP7 
* Database (localhost:3306)
* Test Database
* PhpMyAdmin (localhost:8000)

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