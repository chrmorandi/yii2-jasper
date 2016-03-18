# JasperReports for PHP

[![Latest Stable Version](https://poser.pugx.org/chrmorandi/yii2-jasper/v/stable)](https://packagist.org/packages/chrmorandi/yii2-jasper) 
[![Latest Unstable Version](https://poser.pugx.org/chrmorandi/yii2-jasper/v/unstable)](https://packagist.org/packages/chrmorandi/yii2-jasper)
[![Total Downloads](https://poser.pugx.org/chrmorandi/yii2-jasper/downloads)](https://packagist.org/packages/chrmorandi/yii2-jasper) 
[![License](https://poser.pugx.org/chrmorandi/yii2-jasper/license)](https://packagist.org/packages/chrmorandi/yii2-jasper)

Package to generate reports with [JasperReports 6](http://community.jaspersoft.com/project/jasperreports-library) library through [JasperStarter v3](http://jasperstarter.sourceforge.net/) command-line tool.

##Install

```
composer require chrmorandi/jasper
```

##Introduction

This package aims to be a solution to compile and process JasperReports (.jrxml & .jasper files).

###Why?

**JasperReports** is the best open source solution for reporting.

Generating HTML + CSS to make a PDF. Never think about it, that doesn't make any sense! :p

###What can I do with this?

Well, everything. JasperReports is a powerful tool for **reporting** and **BI**.

**From their website:**

> The JasperReports Library is the world's most popular open source reporting engine. It is entirely written in Java and it is able to use data coming from any kind of data source and produce pixel-perfect documents that can be viewed, printed or exported in a variety of document formats including HTML, PDF, Excel, OpenOffice and Word.

I recommend using [Jaspersoft Studio](http://community.jaspersoft.com/project/jaspersoft-studio) to build your reports, connect it to your datasource (ex: MySQL), loop thru the results and output it to PDF, XLS, DOC, RTF, ODF, etc.

*What you can do with Jaspersoft:*

* Graphical design environment
* Pixel-perfect report generation
* Output to PDF, HTML, CSV, XLS, TXT, RTF and more

##Examples

###The *Hello World* example.

Go to the examples directory in the root of the repository (`vendor/chrmorandi/yii2-jasper/examples`).
Open the `hello_world.jrxml` file with iReport or with your favorite text editor and take a look at the source code.


##Requirements

* Java JDK 1.6 or higher
* PHP [exec()](http://php.net/manual/function.exec.php) function
* [optional] [Mysql Connector](http://dev.mysql.com/downloads/connector/j/) (if you want to use Mysql database)
* [optional] [PostgreSQL Connector](https://jdbc.postgresql.org/download.html) (if you want to use PostgreSQL database)
* [optional] [Jaspersoft Studio](http://community.jaspersoft.com/project/jaspersoft-studio) (to draw and compile your reports)


##Installation

###Java

Check if you already have Java installed:

```
$ java -version
java version "1.7.0_80"
Java(TM) SE Runtime Environment (build 1.7.0_80-b15)
Java HotSpot(TM) 64-Bit Server VM (build 24.80-b11, mixed mode)
```

If you get:

	command not found: java

Then install it with: (Ubuntu/Debian)

	$ sudo apt-get install default-jdk

Now run the `java -version` again and check if the output is ok.

###Composer

Install [Composer](http://getcomposer.org) if you don't have it.

```
composer require chrmorandi/yii2-jasper
```

Or in your `composer.json` file add:

```javascript
{
    "require": {
        "chrmorandi/yii2-jasper": "*",
    }
}
```

And the just run:

    composer update

and thats it.

###Add the component to the configuration

```php
return [
    ...
    'components'          => [
        'jasper' => [
            'class' => 'chrmorandi\jasper',
            'db' => [
                'host' => localhost,
                'port' => 5432,    
                'driver' => 'postgres',
                'dbname' => db_banco,
                'username' => 'username',
                'password' => 'password',
                //'jdbcDir' => './jdbc',
                //'jdbcUrl' => 'jdbc:postgresql://"+host+":"+port+"/"+dbname',
            ], 
        ],
        ...
    ],
    ...
];
```

###Using

```php
use chrmorandi\Jasper;

public function actionIndex()
{
    $jasper = Yii::$app->jasper;;

    // Compile a JRXML to Jasper
    $jasper->compile(__DIR__ . '/../../vendor/chrmorandi/yii2-jasper/examples/hello_world.jrxml')->execute();

    // Process a Jasper file to PDF and RTF (you can use directly the .jrxml)
    $jasper->process(
        __DIR__ . '/../../vendor/chrmorandi/yii2-jasper/examples/hello_world.jasper',
        false,
        array("pdf", "rtf"),
        array("php_version" => "xxx")
    )->execute();

    // List the parameters from a Jasper file.
    $array = $jasper->list_parameters(
        __DIR__ . '/../../vendor/chrmorandi/yii2-jasper/examples/hello_world.jasper'
    )->execute();

    return ;
}
```

###MySQL

We ship the [MySQL connector](http://dev.mysql.com/downloads/connector/j/) (v5.1.34) in the `/src/JasperStarter/jdbc/` directory.

###PostgreSQL

We ship the [PostgreSQL](https://jdbc.postgresql.org/) (v9.4-1208) in the `/src/JasperStarter/jdbc/` directory.

##Performance

Depends on the complexity, amount of data and the resources of your machine (let me know your use case).

I have a report that generates a *Invoice* with a DB connection, images and multiple pages and it takes about **3/4 seconds** to process. I suggest that you use a worker to generate the reports in the background.

##License

MIT
