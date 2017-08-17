# JasperReports for PHP

[![Latest Stable Version](https://poser.pugx.org/chrmorandi/yii2-jasper/v/stable)](https://packagist.org/packages/chrmorandi/yii2-jasper)
[![Total Downloads](https://poser.pugx.org/chrmorandi/yii2-jasper/downloads)](https://packagist.org/packages/chrmorandi/yii2-jasper) 
[![License](https://poser.pugx.org/chrmorandi/yii2-jasper/license)](https://packagist.org/packages/chrmorandi/yii2-jasper)
[![Build Status](https://travis-ci.org/chrmorandi/yii2-jasper.svg?branch=master)](https://travis-ci.org/chrmorandi/yii2-jasper)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chrmorandi/yii2-jasper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chrmorandi/yii2-jasper/?branch=master)

Package to generate reports with [JasperReports 6](http://community.jaspersoft.com/project/jasperreports-library) library through [JasperStarter v3](http://jasperstarter.sourceforge.net/) command-line tool.

##Install

```sh
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

* Java JDK 8 or higher
* PHP [exec()](http://php.net/manual/function.exec.php) function
* [optional] [Mysql Connector](http://dev.mysql.com/downloads/connector/j/) (if you want to use Mysql database)
* [optional] [PostgreSQL Connector](https://jdbc.postgresql.org/download.html) (if you want to use PostgreSQL database)
* [optional] [Jaspersoft Studio](http://community.jaspersoft.com/project/jaspersoft-studio) (to draw and compile your reports)


##Installation

###Java

Check if you already have Java installed:

```sh
$ java -version
java version "1.8.0_91"
Java(TM) SE Runtime Environment (build 1.8.0_91-b14)
Java HotSpot(TM) 64-Bit Server VM (build 25.91-b14, mixed mode)
```

If you get:

    command not found: java

Then install it with: (Ubuntu/Debian)

```sh
    $ sudo apt-get install default-jdk
```

Now run the `java -version` again and check if the output is ok.

###Composer

Install [Composer](http://getcomposer.org) if you don't have it.

```sh
composer require chrmorandi/yii2-jasper
```

Or in your `composer.json` file add:

```json
{
    "require": {
        "chrmorandi/yii2-jasper": "*"
    }
}
```

And the just run:

```sh
composer update
```

and thats it.

###Add the component to the configuration

Use Data Source Name (DSN) for connection information with the database in format like Driver invocation.
See [http://php.net/manual/pt_BR/pdo.construct.php](http://php.net/manual/pt_BR/pdo.construct.php)

```php
return [
    ...
    'components'          => [
        'jasper' => [
            'class' => 'chrmorandi\jasper\Jasper::className()',
            'redirect_output' => false, //optional
            'resource_directory' => false, //optional
            'locale' => 'pt_BR', //optional
            'db' => [
                'dsn' =>'psql:host=localhost;port=5432;dbname=myDatabase',
                'username' => 'username',
                'password' => 'password',
                //'jdbcDir' => './jdbc', **Defaults to ./jdbc
                //'jdbcUrl' => 'jdbc:postgresql://"+host+":"+port+"/"+dbname',
            ]
        ]
        ...
    ],
    ...
];
```

###Using

```php
use chrmorandi\jasper\Jasper;

public function actionIndex()
{
    // Set alias for sample directory
    Yii::setAlias('example', '@vendor/chrmorandi/yii2-jasper/examples');

    /* @var $jasper Jasper */
    $jasper = Yii::$app->jasper;

    // Compile a JRXML to Jasper
    $jasper->compile(Yii::getAlias('@example') . '/hello_world.jrxml')->execute();

    // Process a Jasper file to PDF and RTF (you can use directly the .jrxml)
    $jasper->process(
        Yii::getAlias('@example') . '/hello_world.jasper', 
        ['php_version' => 'xxx'],
        ['pdf', 'rtf'],
        false, 
        false 
    )->execute();

    // List the parameters from a Jasper file.
    $array = $jasper->listParameters(Yii::getAlias('@example') . '/hello_world.jasper')->execute();

    // return pdf file
    Yii::$app->response->sendFile(Yii::getAlias('@example') . '/hello_world.pdf');

}
```

###MySQL

We ship the [MySQL connector](http://dev.mysql.com/downloads/connector/j/) (v5.1.39) in the `/src/JasperStarter/jdbc/` directory.

###PostgreSQL

We ship the [PostgreSQL](https://jdbc.postgresql.org/) (v9.4-1208) in the `/src/JasperStarter/jdbc/` directory.

##Performance

Depends on the complexity, amount of data and the resources of your machine.

Is possible generate reports in the background.

##License

MIT
