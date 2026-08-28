# Changelog

## 3.3.0

* Add: option to not repeatedly print the same description on repeated events 

## 3.2.2

* Fix: events using a `TZID` timezone displayed at the wrong time
* Fix: all-day events are now identified from the iCal `VALUE=DATE` parameter rather than guessed from their duration
* Fix: "floating" times without a timezone are interpreted in the site's timezone

## 3.2.1 – May 2026

* Fix: timezone bug with all-day events displaying wrong day

## 3.2.0

* Add optional location link to Google Maps
* Add regex replace on location address

## 3.1.0

* Use blocks
* Add `refresh cache` button
* Fix issue with null end dates
* Catch exceptions during rendering blocks
* Improve date/time: all day events, display only start/both start and end times
* Improve location: optional Google Maps link
* Improve location: apply regex replacement to shorten addresses (e.g. remove "USA")

## 3.0.2

* Use internal `Calendar_Event` class to pass data

## 3.0.1

* Update bh-wp-logger

## 3.0 2026-02

* Require PHP 8.4

## 2023-10-06

* Remove documentation page
* Add block via `@wordpress/create-block`

## 2020 August

* Working and in production with PHP rendering
