## Phalcon Debug Widget

### Requirements

- PHP v5.6+
- Phalcon v2.0+

### How it works

The debug widget for now is very simplistic and more of a proof-of-concept. It expects you have three services in your dependency injector named "db", "dispatcher" and "view" and that they correspond to those services. When you pass the DI to Phalcon Debug Widget It looks for those specific services and:
- sets them as shared services
- sets the eventManager for them
- Attaches itself to those events

This means passing the DI to the debug widget will alter those services. Generally speaking, a shared db, dispatcher, and view is fine. If you have ideas for other ways to hook in, please open an issue for discussion.

The Phalcon Debug Widget is designed to make development easier by displaying debugging information directly in your browser window. Currently it displays php globals such as $_SESSION as well as outputing resource usage and database queries and connection information. It includes syntax highlighting via [Prismjs.com](http://prismjs.com/).

### Installation

composer.json:

    "phalcon/debugger": "dev-master"

## Usage and Configuration

Define a debug or environment flag in your main index.php file so you can easily disable the Phalcon Debug Widget on production environments. Example:

    defined('PHALCONDEBUG') || define('PHALCONDEBUG', true);

After you have setup your \Phalcon\Loader and \Phalcon\DI\FactoryDefault() create a new instance of the debug widget. 

    if (PHALCONDEBUG == true) {
        $debugWidget = new \Phalcon\Debug\DebugWidget($di);
    }

## Preview

![](/preview.png)

### Credits

Forked from [Zazza/phalcon2-debug-widget](https://github.com/Zazza/phalcon2-debug-widget)

