# Laravel Wrapper for Phery Ajax Library

Wraps [Phery](https://github.com/pheryjs/phery) to work with Laravel.
Phery is a great ajax library for use with just [jQuery](https://jquery.com/)

Phery is pretty flexible which features a steep learning curve.  The example
below shows a simple pattern to quickly get started before moving on to more
advanced examples.

### View

```html
...
<a id="link-test">Test Alert</a>
...
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$('#link-test').on('click', function() {
    phery.remote('pheryTest', {message:'Hi There!'});
});
</script>
...
```

### Controller

```php
class BaseController extends Controller
{
    use ProcessPheryRequests;  // Use the trait  
    ...
    public function pheryTest($form)
    {
        // $form has everything in the object above
        $message = array_get($form, 'message', null);
        // there will be a pheryResponse object already ready to go
        $this->pheryResponse->alert($message);
        // then just return the $this->pheryResponse object
        return $this->pheryResponse;
    }
    ...
}
```

## TODO

* Tested with Laravel 5.7.x and Phery `dev-master` (3.0.0-alpha2)
* CSRF to use Laravel Session (for now, need to turn off csrf middleware)
* Publish phery.min.js and phery.js to /public/js folder

## Installation

`composer require chrisgo/laravel-phery`

This will create a couple of files in your Laravel app

* `app/config/phery.js`
*


## Configuration

*


## Usage



---

For more advanced examples, check out the full
[Phery documentation](http://phery-php-ajax.net/)
