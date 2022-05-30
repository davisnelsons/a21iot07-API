# a21iot07 API

REST API I wrote for a final bachelors project. Written in PHP using the Klein.php framework for routing, coupled with MySQL database.

The project was an IoT smart watch that had an app, and this API ensured communication between the smart watch and the Android app.

## Features:

* JWT Token authorization
* Firebase push notifications on specified conditions
* Login/Sign up a new user
* Save and update user data
* Receive and store BPM data, step data
* Enable linking of the device with the user
* Simple user setting key-value store

## Issues
Since this was my first time using PHP, I incurred high amounts of technical debt at the start of the project.
I was able to relieve this debt somewhat but still some issues persisted.

* Due to problems in formatting the requests from the IoT device, I had to use dubious url parsing to make it work
* Close to zero input sanitization, open to SQL injection
* Confusing controller-model structure
* Security issues
* No unit tests
