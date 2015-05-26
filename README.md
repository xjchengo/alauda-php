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
	
