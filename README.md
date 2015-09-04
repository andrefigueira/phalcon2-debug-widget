## Phalcon Debug Widget

### Requirements

- PHP: v5.5.0+
- Phalcon: v2.0+

### Installation

Include it in your `composer.json` file

    {
    	"require-dev": {
        	"phalcon-tools/debugger": "dev-master"
    	}
	}

### Usage and Configuration

Define a debug or environment flag in your application bootstrap file so you can easily disable the Phalcon Debug Widget on production environments. e.g.

    define('PHALCON_DEBUG', true);

After you have setup your \Phalcon\Loader and \Phalcon\DI\FactoryDefault() create a new instance of the debug widget. 

    if (PHALCON_DEBUG == true) {
        $debugWidget = new \Phalcon\Debug\DebugWidget($di);
    }
    
That's it! The Phalcon debug widget should appear along the bottom of your application!    

### How it works

The debug widget for now is very simplistic, however can be useful. It expects you have three services in your dependency injector named:

- db
- dispatcher
- view

It expects these services to match the Phalcon services of the same name.

When you pass the DI to Phalcon Debug Widget It looks for those specific services and does the following:

- Sets them as shared services
- Sets the eventManager for them
- Attaches itself to those events

This means passing the DI to the debug widget will alter those services. Generally speaking, a shared db, dispatcher, and view is fine. If you have ideas for other ways to hook in, please open an issue for discussion.

The Phalcon Debug Widget is designed to make development easier by displaying debugging information directly in your browser window. Currently it displays php globals such as $_SESSION as well as outputing resource usage and database queries and connection information.

### Preview
Preview of the available debug panels

#### Server View

![](https://s3.amazonaws.com/f.cl.ly/items/1L2C1A1W2T3M0R1y451q/SERVER.png)

#### Views View

![](https://s3.amazonaws.com/f.cl.ly/items/383l081u3T2c1z3D393A/VIEWS.png)

#### Database View

![](https://s3.amazonaws.com/f.cl.ly/items/2S3N3l1P0P1K3S142d0L/DATABASE.png)

#### Request View

![](https://s3.amazonaws.com/f.cl.ly/items/3U3D1K2j2n1S3A2Y3e0K/REQUEST.png)
