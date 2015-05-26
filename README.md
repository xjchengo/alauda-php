# alauda-php

## Introduction

This is a package for managing alauda account in command-line.

## Install

You must install composer first. Head to [Install Composer](https://getcomposer.org/doc/00-intro.md) for details.

	composer global require xjchen/alauda:\*@dev
	
After above command, add compose bin to your path.

	export PATH=~/.composer/vendor/bin:$PATH
	
## Usage

### Login

	alauda login
	
### Logout

	alauda logout
	
### List all commands

	alauda
	
### Show doc of some command

	alauda help command
	
## Comand list

-	`login`			Log in to alauda registry server
-	`logout`		Log out from alauda registry server
-	`up`			Deploy a php web server in alauda
-	`auth:profile`		Get the profile of a user
-	`db:create`		Create a database if not exist
-	`db:create-user`	Create a database user if not exist
-	`instance:describe`	Get the details of an instance
-	`instance:list`		List all instances belong to the application
-	`repository:create`	Create a new Repository
-	`repository:destroy`	Delete a repository, all tags will be removed
-	`repository:list`	List all repositories
-	`repository:tags`	List all tags of a repository
-	`service:describe`	Get the details of an application
-	`service:destroy`	Destroy an service, all instances and related resources will be removed
-	`service:list`		List all services in a namespace
-	`service:logs`		Get service log

