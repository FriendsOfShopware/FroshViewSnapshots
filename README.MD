# View Snapshots

[![Travis CI](https://travis-ci.org/FriendsOfShopware/FroshViewSnapshots.svg?branch=master)](https://travis-ci.org/FriendsOfShopware/FroshViewSnapshots)
[![Download @ Community Store](https://img.shields.io/badge/endpoint.svg?url=https://api.friendsofshopware.com/FroshViewSnapshots)](https://store.shopware.com/en/frosh54349549179f/view-snapshots.html)

View Snapshots is a plugin for shopware that allows you to record/log requests
continiously. It will record snapshots of variables passed to the view and
will persist them in serialized form to the database. Also persisted is the
template path, session ID and steps taken since the start of recording.

## Goal

By having a snapshot of the template, including it's data, you can review most
requests with the more or less exact same outcome as the original request.
Therefore you can customize your templates, css, JavaScript and check your
changes against a previously recorded situation, e.g. a variety of different
articles within the basket or the order confirmation.

## Features

* Start/stop recording sessions at any time
* Backend component to view recorded sessions
* View recorded snapshots
* Step forward/backward between snapshots within a recorded session
* Compare recordings and their steps

### New with v. 1.1.0

* View complete diffs of raw data by selecting two snapshots in the backend app

_Note: The library used to generate diffs is [FineDiff](https://github.com/gorhill/PHP-FineDiff) and it was integrated into this project as it is not available as composer package (yet)_

## Usage

After installing the plugin through the plugin manager, you can find
the accompanying backend app under the Configuration menu.

The backend app will list all recorded sessions.

To start a recording session, request a frontend page and use the
JavaScript console to issue commands:

**Start recording**

```
> snapshots.record()  
▶️️ Recording of session starting next request. Session ID: 0sorspi2md1j2vpt9tn9po3om1
```

**Stop recording**

```
> snapshots.stop()  
✋️️ Stopped recording current session.
```

#### When viewing a recorded session

**View next step in recorded session**

```
> snapshots.next()
```

**View previous step in recorded session**

```
> snapshots.prev()
```

## Installation

* Clone this repository into a folder **FroshViewSnapshots** within the **custom/plugins** directory of the Shopware installation.
* Install the plugin through the Plugin-Manager within the Shopware backend.

### Install with composer
* Change to your root installation of shopware
* Run command `composer require frosh/view-snapshots` and install and activate plugin with Plugin Manager 

## Requirements

* Shopware 5.3.4 or higher
* PHP 5.6 or higher

## Dependencies

* Depends on [finediff](https://github.com/gorhill/PHP-FineDiff) by Raymond Hill
