# Laradock CLI helper

This project aims to set a couple of conventions to ease developing using the already easy [Laradock](https://github.com/laradock/laradock) project.

Still a PoC, but already working.

## Dependencies

* You need Docker and docker-compose installed (latest versions)
* You need PHP 7.1 locally
* You also need git configured locally

## Getting Started

There's nothing wrong with a bit of conventions over a nice tool to ease development. This CLI tool will help you setup and use Laradock in a shared project environment. The idea is that you are going to have a shared laradock setup for all your projects.

## Conventions

Before you start using it, you need to arrange your projects in the same folder, so something like this:

```bash
➜  laradock-cli-example tree -L 1 
.
├── project-a
└── project-b

2 directories, 0 files
```

Laradock will be configured in the same level of your projects, and you are to configure a vhosts for each of your projects that you want to use with the laradock setup.

The Laradock CLI is released as single PHP Phar file. You have to download it and give it permission to execute, like this:

```bash
➜  laradock-cli-example curl -L https://github.com/tonysm/laradock-cli/releases/download/0.2/ldk > ldk
➜  laradock-cli-example chmod +x ldk
➜  laradock-cli-example ./ldk list
Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
      --env[=ENV]       The environment the command should run under
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  artisan       Runs artisan for your current project.
  down          Destroys the container and networks.
  hello         Welcome to LDK helper.
  help          Displays help for a command
  init          Install the laradock project on your current folder.
  list          Lists commands
  logs          Watches the logs.
  ps            Lists all running containers.
  stop          Stops running containers.
  up            Starts the base containers if they are not already started.
 db
  db:create     Creates the database.
 schedule
  schedule:run  Run the scheduled commands
 sites
  sites:add     Registers a site and reboots nginx container.
```

After doing this, you can move it to a place that is available in your `$PATH` variable. Now you can use it from anywhere as `ldk`.

### Setting up

The first thing you should do is cd'ing into the Projects folder and run:

```bash
➜  laradock-cli-example ldk init
Cloning the laradock folder, this might take some time...
Configuring the your global setup...
Done!
```

After this command, you should have an `.ldk/` folder at your Project's root folder, like this:

```bash
➜  laradock-cli-example tree -L 1 -a .
.
├── .ldk
├── project-a
└── project-b

3 directories, 0 files
```

### Register the domains locally

We need to bind our application domains locally in our `/etc/hosts` file, like this:

```bash
# laradock
127.0.0.1 project-a.ldk project-b.ldk
```

We are going to use `.ldk` domains here just as an example, you can use whatever you want. If you try to access these domains in the browser (don't forget the `http://` schema before it, otherwise your browser might think you want to search).

If you happen to have something running locally, like Laravel Valet, or Nginx, or Apache, to name a few, you need to stop it, otherwise we won't be able to start our Nginx container.

### Booting our base containers

This step is needed, because any configuration is going to be added to the Docker containers as well. Run:

```bash
➜  laradock-cli-example cd project-a 
➜  project-a ldk up
Starting the desired services...
Creating network "ldk_default" with the default driver
Creating network "ldk_frontend" with driver "bridge"
Creating network "ldk_backend" with driver "bridge"
Creating volume "ldk_phpmyadmin" with local driver
Creating volume "ldk_rethinkdb" with local driver
Creating volume "ldk_mariadb" with local driver
Creating volume "ldk_elasticsearch-data" with local driver
Creating volume "ldk_postgres" with local driver
Creating volume "ldk_aerospike" with local driver
Creating volume "ldk_redis" with local driver
Creating volume "ldk_minio" with local driver
Creating volume "ldk_caddy" with local driver
Creating volume "ldk_adminer" with local driver
Creating volume "ldk_mysql" with local driver
Creating volume "ldk_neo4j" with local driver
Creating volume "ldk_elasticsearch-plugins" with local driver
Creating volume "ldk_memcached" with local driver
Creating volume "ldk_mssql" with local driver
Creating volume "ldk_percona" with local driver
Creating volume "ldk_mongo" with local driver
Creating ldk_applications_1 ... 
Creating ldk_mysql_1 ... 
Creating ldk_applications_1
Creating ldk_applications_1 ... done
Creating ldk_workspace_1 ... 
Creating ldk_workspace_1 ... done
Creating ldk_php-fpm_1 ... 
Creating ldk_php-fpm_1 ... done
Creating ldk_nginx_1 ... 
Creating ldk_nginx_1 ... done
Done!
```

Your workspace containers are now running, but you haven't configured your sites yet. So if you try to access the domains again, you should only see the Nginx default page saying there's no site there.

### Adding your project vhost

To add your site vhost, you need to run:

```bash
➜  project-a ldk sites:add project-a.ldk public
Adding new site http://project-a.ldk/ with document root: public/
Stopping ldk_nginx_1 ... done
Stopping ldk_php-fpm_1 ... done
Stopping ldk_workspace_1 ... done
Stopping ldk_mysql_1 ... done
Removing ldk_nginx_1 ... done
Removing ldk_php-fpm_1 ... done
Removing ldk_workspace_1 ... done
Removing ldk_mysql_1 ... done
Removing ldk_applications_1 ... done
Removing network ldk_default
Removing network ldk_frontend
Removing network ldk_backend
Creating network "ldk_default" with the default driver
Creating network "ldk_frontend" with driver "bridge"
Creating network "ldk_backend" with driver "bridge"
Creating ldk_applications_1 ... 
Creating ldk_mysql_1 ... 
Creating ldk_applications_1
Creating ldk_applications_1 ... done
Creating ldk_workspace_1 ... 
Creating ldk_workspace_1 ... done
Creating ldk_php-fpm_1 ... 
Creating ldk_php-fpm_1 ... done
Creating ldk_nginx_1 ... 
Creating ldk_nginx_1 ... done
```

The first argument is the domain you want to use, so in our case `project-a.ldk`. The second one is this project document root for nginx (the folder that is publicly available) relative to your project. By default, it uses `public`, but you can configure it to whatever you need.

If you try to access it in the browser now, you should see the Laravel welcome page.

### Adding a database

You also need to add a database for your project, you can do it by:

```bash
➜  project-a ldk db:create projectadb
Creating database 'projectadb' on mysql...
Done!
```

Your database is now configured. Remember to change your `.env` with these configs. We use the Laradock defaults here, so you can check the credentials in your `.ldk/.env` file we installed:

```bash
➜  project-a cat ../.ldk/.env | grep MYSQL
PHP_FPM_INSTALL_MYSQLI=false
### MYSQL ##############################################################################################################
MYSQL_VERSION=8.0
MYSQL_DATABASE=default
MYSQL_USER=default
MYSQL_PASSWORD=secret
MYSQL_PORT=3306
MYSQL_ROOT_PASSWORD=root
```

So our project-a `.env` file should be:

```bash
➜  project-a cat .env | grep DB_
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=projectadb
DB_USERNAME=default
DB_PASSWORD=secret
```

### Running migrations

Let's use the Laravel scaffolding for authentication. Run:

```bash
➜  project-a ldk artisan make:auth
Authentication scaffolding generated successfully.
```

You can see the "Login" and "Register" buttons in the welcome page, but if you try to use the forms, it won't work. We have not ran the migrations yet. Now we need to run the migrations for our projecta. Run:

```bash
➜  project-a ldk artisan migrate
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table
Migrating: 2014_10_12_100000_create_password_resets_table
Migrated:  2014_10_12_100000_create_password_resets_table
```

You can also use the `ldk artisan` command from your project, it will forward any calls to the artisan inside the Laradock container for your current project.

Now, the application should work just fine.

### Adding Redis

Laradock has lots of services you can use on your apps, to demonstrate its power, we are going to add Redis to our application. First, you need to boot the `redis` service, like so:

```bash
➜  project-a ldk up redis
Starting the desired services...
Creating ldk_redis_1 ... 
Creating ldk_redis_1 ... done
Done!
```

Great, now you can change your redis configuration to point to:

```bash
➜  project-a cat .env | grep REDIS
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Now you have redis running in your docker setup (you still need to pull the `predis/predis` using composer. You should have composer installed  in your host machine to pull it).

If you use the Redis Facade, things are just working.

### Running 2 apps side-by-side

You can more than one app at a time. Let's say you have 2 apps that interact with each other, as we have now:

```bash
➜  laradock-cli-example tree -L 1 .
.
├── project-a
└── project-b

2 directories, 0 files
```

Change directory into the `project-b` app. You don't need to run `ldk up` anymore, as it's already running. You can check if it's running with:

```bash
➜  project-b ldk ps
Listing running containers...
       Name                     Command               State                     Ports                   
-------------------------------------------------------------------------------------------------------
ldk_applications_1   /true                            Exit 0                                            
ldk_mysql_1          docker-entrypoint.sh mysqld      Up       0.0.0.0:3306->3306/tcp                   
ldk_nginx_1          nginx                            Up       0.0.0.0:443->443/tcp, 0.0.0.0:80->80/tcp 
ldk_php-fpm_1        docker-php-entrypoint php-fpm    Up       9000/tcp                                 
ldk_redis_1          docker-entrypoint.sh redis ...   Up       0.0.0.0:6379->6379/tcp                   
ldk_workspace_1      /sbin/my_init                    Up       0.0.0.0:2222->22/tcp
```

Now we only have to add the domain for our `project-b`:

```bash
➜  project-b ldk sites:add project-b.ldk 
Adding new site http://project-b.ldk/ with document root: public/
Stopping ldk_redis_1 ... done
Stopping ldk_nginx_1 ... done
Stopping ldk_php-fpm_1 ... done
Stopping ldk_workspace_1 ... done
Stopping ldk_mysql_1 ... done
Removing ldk_redis_1 ... done
Removing ldk_nginx_1 ... done
Removing ldk_php-fpm_1 ... done
Removing ldk_workspace_1 ... done
Removing ldk_mysql_1 ... done
Removing ldk_applications_1 ... done
Removing network ldk_default
Removing network ldk_frontend
Removing network ldk_backend
Creating network "ldk_default" with the default driver
Creating network "ldk_frontend" with driver "bridge"
Creating network "ldk_backend" with driver "bridge"
Creating ldk_mysql_1 ... 
Creating ldk_redis_1 ... 
Creating ldk_applications_1 ... 
Creating ldk_redis_1
Creating ldk_mysql_1
Creating ldk_applications_1 ... done
Creating ldk_workspace_1 ... 
Creating ldk_workspace_1 ... done
Creating ldk_php-fpm_1 ... 
Creating ldk_php-fpm_1 ... done
Creating ldk_nginx_1 ... 
Creating ldk_nginx_1 ... done
```

We are not using a database for this project, but you can add one if you need and change the credentials locally in this project's `.env` file. You can now access (http://project-b.ldk)(http://project-b.ldk).

You might have noticed that we are restarting every container when you add a new domain. This happens because we change some configurations in our Nginx container to alias it with all of our domains. So, internally, your containers can talk to each other using their domains, `http://project-a.ldk` and `http://project-b.ldk`, respectively. This would not be possible otherwise.

### Gotchas

There are some gotchas here that are not addressed yet.

1. You're stuck with the same version for all services fow now. So same PHP, Elasticsearch, Redis versions for all your projects;
2. The idea here is to reuse the same services for all projects that runs locally, so let's say you use Redis in more than 1 project. You would need to change the database number in your database configuration for Redis to avoid applications change each other datastores.
