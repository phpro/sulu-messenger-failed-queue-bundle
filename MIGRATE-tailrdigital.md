# Migrating from tailrdigital/sulu-messenger-failed-queue-bundle  to phpro/sulu-messenger-failed-queue-bundle

This guide helps you migrate from the old tailrdigital/sulu-messenger-failed-queue-bundle to the new phpro/sulu-messenger-failed-queue-bundle.

## 1. Remove the old package

First, remove the old package from your project:

```sh
composer remove tailrdigital/sulu-messenger-failed-queue-bundle
```

## 2. Install the new package

Next, install the new package:

```sh
composer require phpro/sulu-messenger-failed-queue-bundle
```

## 3. Find / Replace references

You need to replace all references from tailr or tailrdigital to phpro.
You can do this with a simple find/replace (with preserve case) in your IDE.

## 4. Rebuild admin

Make sure that the package.json and lock file contains links to phpro instead of tailrdigital.
Rebuild the Sulu admin to ensure all changes are applied:

```sh
bin/console sulu:admin:build
```

## 5. Reactivate permissions

Make sure you've set the correct permissions in the Sulu admin for this package.
Go to Settings > User Roles and enable the permissions you need.
Afterwards you could find the Failed Queue view/panel via Settings > Failed Queue.
