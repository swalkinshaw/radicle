# Theme Architecture

There are 3 files required by WordPress which must remain in the theme root: `style.css`, `functions.php`, and `index.php`.

* `style.css` contains meta data about the theme that WordPress parses. No actual CSS should be included.

* `index.php` is left blank and shouldn't be modified.

* `functions.php` gets loaded before anything else. Right now this file just acts as a bootstrap to load all required files.

`lib/init.php` is the next file to run. It performs a few WP theme configurations and sets up our views. See below for more.

### Views

WordPress "templates" are always found in the theme root (without folder organization). We've worked around this limitation by hooking into the `template_redirect` action and bypassing the default template hierarchy.

Our custom template loader is found in `lib/init.php`. Add additional mappings as needed. Mappings just consist of a conditional tag function and a view file to load if true. Read ViewLoader class for documentation.

WP templates now act as a kind of Controller + View class. WordPress hits these files first, and their purpose is to load Model data and then render to an actual template.

View classes extend from the base `View` class. To render a template, the view class only needs to implement the data function which returns anything to be passed down to the template.

The class needs to be instantiated and rendered. This isn't done automatically since there's no router.

Example of a View

```php
  namespace ts;

  class HomeView extends View {
    protected function data() {
      $blog = get_last_updated_blogs();

      return array('blogs' => $blogs);
    }
  }

  $view = new HomeView();
  $view->render('home.twig');
```

### WP Query

Although this theme bypasses a lot of WordPress default functionality, it still leaves the complex stuff alone.

For the home page, we are doing manual SQL queries since we're displaying posts across _all_ blogs.

However, for the individual blogs, we leave that up to [WP_Query](http://codex.wordpress.org/Class_Reference/WP_Query). WP_Query automatically queries your database for objects based on the request URL.

It will return posts, a single post, categories, comments, pages, etc. Here's an example of how to access WP_Query for a blog post in a view:

```php
global $wp_query

$post = $wp_query->posts[0]
```

### Models

Under the `classes` directory are models for a `Blog`, `Post`, `Author`, `Comment`. In order to get around WordPress Network Install (WPMU) constraints, there's some manual SQL queries are done in these files.

Currently, the models are plain classes that don't inherit from a parent Model class since they don't have much in common right now.

### Templates

Templates are found in the `templates` directory. They use a real templating language instead of PHP mixed in with HTML. [Twig](http://twig.sensiolabs.org/) is used as the templating language. It's basically a PHP port of Jinja which Django uses.

These templates work just like any template/view would in MVC since they only have access to the data passed down to them. There's one big exception to this: all built-in WP functions are available through the `wp` object to make our lives easier.
