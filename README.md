# Puzzle App Blog Bundle
**=========================**

Puzzle app bundle

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

`composer require webundle/puzzle-app-blog`

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
{
    $bundles = array(
    // ...

    new Puzzle\App\BlogBundle\PuzzleAppBlogBundle(),
                    );

 // ...
}

 // ...
}
```

### Step 3: Register the Routes

Load the bundle's routing definition in the application (usually in the `app/config/routing.yml` file):

# app/config/routing.yml
```yaml
puzzle_app:
        resource: "@PuzzleAppBlogBundle/Resources/config/routing.yml"
```

### Step 4: Configure Bundle

Then, configure bundle by adding it to the list of registered bundles in the `app/config/config.yml` file of your project under:

```yaml
# Puzzle App Blog
puzzle_app_blog:
    title: blog.title
    description: blog.description
    icon: blog.icon
    templates:
        article:
            list: 'AppBundle:Blog:list_articles.html.twig'
            show: 'AppBundle:Blog:article.html.twig'
        category:
            list: 'AppBundle:Blog:list_categories.html.twig'
            show: 'AppBundle:Blog:category.html.twig'
        comment:
            list: 'AppBundle:Blog:list_comments.html.twig'
            create: 'AppBundle:Blog:create_comment.html.twig'
            show: 'AppBundle:Blog:comment.html.twig'
```