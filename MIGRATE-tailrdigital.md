# Migrating from tailrdigital/sulu-translations-bundle to phpro/sulu-translations-bundle

 This guide helps you migrate from the old tailrdigital/sulu-translations-bundle to the new phpro/sulu-translations-bundle.
 
## 1. Remove the old package
 First, remove the old package from your project:
 
 ```sh
 composer remove tailrdigital/sulu-translations-bundle
 ```

## 2. Install the new package

Next, install the new package:

```sh
composer require phpro/sulu-translations-bundle
```

## 3. Find / Replace references

You need to replace all references from `tailr` or `tailrdigital` to `phpro`.
You can do this with a simple find/replace (with preserve case) in your IDE.


## 4. Rename translations table

The new package uses a different table name for storing translations.
You need to rename the existing table `tailr_translations` to `phpro_translations`.
You can do this with the following SQL command:

```sql
ALTER TABLE tailr_translations RENAME TO phpro_translations;
```
