# Helpscout Parachute

This is a command based tool that stores and creates collections, categories and articles into a database an on Helpscout it's self.

## Setup

Configure the following in your `.env`

```
SITE_ID=
HELPSCOUT_DOCS_API_KEY=
```

Next run:

`composer install`

## Commands

The following will pull what ever you have up on Helpscout down and create the associated relations between the three.

`php artisan migrate && php artisan fetch:collections && php artisan fetch:categories && php artisan fetch:articles`

To publish articles you can do:

`php artisan create:docs 'Collection Name' /full/path/to/directory/containing/.md/files directoryNesting removeFirstElement`

- `Collection Name`: The name where the articles should be stored.
- `Path`: The path to the files which have to be MD based.
- `directoryNesting`: Based on nested directory structure, there might be a specific folder in the breakdown that you ant to use as a category, by default we will use the second folder down in the structure.
- `removeFirstElement`: If there are less then 2 folders structures down then we can (by default false) remove that specific section from the list.
