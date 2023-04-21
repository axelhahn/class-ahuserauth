
## Access control for an application


### Init

Initialize access class:

```php
require(__DIR__.'/../classes/ah-access.class.php');
$oAccess=new axelhahn\ahAccesscontrol();
```

### Autodetect user

The method detectUser() tries to fetch login information from $_SERVER, eg. Shibboleth or Basic authentication.

```php
if(!$oAccess->detectUser()) {
    echo "NO USER user was detected. Now should follow a login form. ".PHP_EOL;
} else {
    echo "A user was detected.".PHP_EOL;    
}
```

It looks too magic right?! Where does the classs knows what to check?


### Authenticate with user and password

TODO

If no user was detected you will need to show a dialog to enter user and password.

```php
$oAccess->setAuthtype('file');
if (!$oAccess->auth->authenticate($USER, $PASSWORD)){
       echo 'Login failed';
} else {
       echo 'Login successful';
}
```

### Get data of an authenticated user

The access control object has sub objects

```txt
$oAC -+-> auth   {object} -> create/ read/ update/ delete
       -> user   {string} -> the current userid
       -> groups {array}  -> the current app groups
       -> roles  {array}  -> the current app roles
```

During testing you might want to get a fast overview:

```php
print_r($oAccess->dump());
```

## auth

Authenticate a user a user somehow and somewhere - locally or on another instance.

Expect ... 
* to get as minumum a user id only - without real name, group, password.
* you can only read the user id and have no other action

To get a user id means that the user is authenticated.
This auth user you can map into a local user.

## user

The user inside your application. The uid is the same like read freom auth uid.
With the application user you can add personal data.

## groups

Additional to the user you can add / change/ delete groups.

## roles

Based on groups or single user ids you can set roles to 

You should add roles for

* anonymous access - give limited or no access to CUG pages
* authenticated user - access for any user that has a login but no defined group yet
* access by group name - ecxtend the access for members, project managers, admin
* access by uuid - same access like described for group access ... 
