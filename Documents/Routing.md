# 🚦Routing

In **Papism**, routing has introduced a new syntax that encourages representing routes as actual code instead of strings. This change aims to simplify the writing process and enhance readability.

The new syntax provides a more concise and expressive way to define routes, making it easier for developers to write and maintain their code.

## Classname and Namespaces

the **`Route`** classname is for define new routes and related jobs to routing. router use namespace as directory name and classname as file name for your class.

**Class example**:

```php
<?php
# {project_root}/Controller/Page.php

namespace Controller;

class Page
{
    function index($id)
    {
        echo $id;
    }
}

```

use defined routes are inside the **`routes.php`** file

## Static Route

this is an example of static route followed by this instruction

- **Method**: call router function like **GET** ,**POST**,**DELETE** etc... with url

- **Target Class**: class name in **Contrller** namespace syntax similar to **PHP**

**Class example**:

```php
<?php

namespace Controller;

class Page
{
    function index()
    {
        echo 'Hello world';
    }
}

```

**Route example**:

```php
Router::get('/{id}')->Page::index();
```

## Dinamic route

dynamic route have clear syntax for readability.

**Class example**:

```php
<?php

namespace Controller;

class Page
{
    function index($id)
    {
        echo $id;
    }
}

```

**Route example**:

```php
Router::get('/{id}')->Page::index();
```

## Dinamic route (Filtred)

you can filter the pattern of url by predefined or custom regex

> **syntax** : {name:pattern}

- **{skip}**: skip any character and wont capture as parameter
- **{name:int}**:   negetive or posetive integer number
- **{name:float}**  negetive or posetive float number
- **{name:hex}**  hex digits [0-9A-F]
- **{name:octal}**  octal digits [0-7]
- **{name:decimal}**  digits [0-9]
- **{name:string}**:  letter in UTF8
- **{name:kabab}**  kabab-case string
- **{name:snake}**  snake_case string

**Class example**:

```php
<?php

namespace Controller;

class Page
{
    function index($id)
    {
        echo $id;
    }
}

```

**Route example**:

```php
Router::get('/{id:int}')->Page::index(); // route with predefined pattern
Router::get('/{id:\s\S}')->Page::index(); // route with user defined regex
```

## Variant use same class

in routing you can add set custom parameters manualy

**Class example**:

```php
<?php

namespace Controller;

class Page
{
  function index($id, $lang = 'fa')
  {
    echo $lang == 'fa' ? 'شناسه شما :' . $id : 'Your id is :' . $id;
  }
}

```

**Route example**:

```php
Router::get('/{id}')->Page::index(lang: 'en');
```
